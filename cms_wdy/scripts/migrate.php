<?php

if (PHP_SAPI !== 'cli') {
    echo "This migration runner must be executed from the command line.\n";
    exit(1);
}

if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';

date_default_timezone_set('Europe/London');

$command = isset($argv[1]) ? $argv[1] : 'status';
$migrations_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'migrations';
$base_schema = realpath($migrations_dir . DIRECTORY_SEPARATOR . 'base.sql');

ensure_migration_history_table($conn);

switch ($command) {
    case 'up':
        run_pending_migrations($conn, $migrations_dir, $base_schema);
        break;

    case 'status':
        show_migration_status($conn, $migrations_dir, $base_schema);
        break;

    default:
        echo "Usage:\n";
        echo "  php cms_wdy/scripts/migrate.php status\n";
        echo "  php cms_wdy/scripts/migrate.php up\n";
        exit(1);
}

function ensure_migration_history_table(PDO $conn)
{
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS `migration_history` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `filename` varchar(255) NOT NULL,
            `checksum` char(64) NOT NULL,
            `applied_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `migration_history_filename_unique` (`filename`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

function get_migration_files($migrations_dir, $base_schema)
{
    $files = glob($migrations_dir . DIRECTORY_SEPARATOR . '*.sql');
    sort($files);

    return array_values(array_filter($files, function ($file) use ($base_schema) {
        $real = realpath($file);
        return $real !== false && $real !== $base_schema;
    }));
}

function get_applied_migrations(PDO $conn)
{
    $stmt = $conn->query('SELECT filename, checksum, applied_at FROM migration_history ORDER BY id ASC');

    $rows = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[$row['filename']] = $row;
    }

    return $rows;
}

function show_migration_status(PDO $conn, $migrations_dir, $base_schema)
{
    $files = get_migration_files($migrations_dir, $base_schema);
    $applied = get_applied_migrations($conn);

    if (empty($files)) {
        echo "No migration files found.\n";
        return;
    }

    foreach ($files as $file) {
        $filename = basename($file);
        $status = isset($applied[$filename]) ? 'applied' : 'pending';
        echo sprintf("[%s] %s\n", strtoupper($status), $filename);
    }
}

function run_pending_migrations(PDO $conn, $migrations_dir, $base_schema)
{
    $files = get_migration_files($migrations_dir, $base_schema);
    $applied = get_applied_migrations($conn);
    $ran_any = false;

    foreach ($files as $file) {
        $filename = basename($file);
        $checksum = hash_file('sha256', $file);

        if (isset($applied[$filename])) {
            if ($applied[$filename]['checksum'] !== $checksum) {
                echo sprintf("Checksum mismatch for already applied migration: %s\n", $filename);
                exit(1);
            }

            continue;
        }

        apply_sql_file($conn, $file);

        $stmt = $conn->prepare('INSERT INTO migration_history (filename, checksum, applied_at) VALUES (:filename, :checksum, :applied_at)');
        $stmt->execute(array(
            ':filename' => $filename,
            ':checksum' => $checksum,
            ':applied_at' => date('Y-m-d H:i:s'),
        ));

        echo sprintf("Applied %s\n", $filename);
        $ran_any = true;
    }

    if (!$ran_any) {
        echo "No pending migrations.\n";
    }
}

function apply_sql_file(PDO $conn, $file)
{
    $sql = file_get_contents($file);

    if ($sql === false) {
        echo sprintf("Could not read migration file: %s\n", $file);
        exit(1);
    }

    $statements = split_sql_statements($sql);

    try {
        foreach ($statements as $statement) {
            $trimmed = trim($statement);

            if ($trimmed === '') {
                continue;
            }

            $result = $conn->query($trimmed);

            if ($result instanceof PDOStatement) {
                do {
                    $result->fetchAll(PDO::FETCH_ASSOC);
                } while ($result->nextRowset());

                $result->closeCursor();
            }
        }
    } catch (PDOException $e) {
        echo sprintf("Migration failed for %s\n%s\n", basename($file), $e->getMessage());
        exit(1);
    }
}

function split_sql_statements($sql)
{
    $lines = preg_split("/(\r\n|\n|\r)/", $sql);
    $statements = array();
    $buffer = '';

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || strpos($trimmed, '-- ') === 0 || strpos($trimmed, '#') === 0) {
            continue;
        }

        $buffer .= $line . "\n";

        if (substr($trimmed, -1) === ';') {
            $statements[] = $buffer;
            $buffer = '';
        }
    }

    if (trim($buffer) !== '') {
        $statements[] = $buffer;
    }

    return $statements;
}

?>
