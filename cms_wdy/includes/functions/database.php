<?php

function table_row_count($table, $where = '')
{
    global $conn;
    
    if (strlen($where) == 0) {
        $sql = sprintf('SELECT COUNT(*) as total FROM %s', $table);
    } else {
        $sql = sprintf('SELECT COUNT(*) as total FROM %s WHERE %s', $table, $where);
    }
        
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $row = $stmt->fetch();

        return $row['total'];
    } 
    catch(PDOException $e) 
    {
        echo 'ROW COUNT ERROR: ' . $e->getMessage();
    }
}
        
function table_fetch_row($table, $where, $order_by = '')
{
    global $conn;
    
    $sql = sprintf('SELECT * FROM %s', $table);
    
    if (strlen($where) > 0) 
    {
        $sql .= sprintf(' WHERE %s', $where);
    }

    if (strlen($order_by) > 0) 
    {
        $sql .= sprintf(' ORDER BY %s', $order_by);
    }

    $sql .= ' LIMIT 0,1';
        
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    } 
    catch(PDOException $e) 
    {
        echo 'FETCH ROW ERROR: ' . $e->getMessage();
    }
}
        
function table_fetch_rows($table, $where = '', $order_by = '', $limit_from = 0, $limit_to = 0)
{
    global $conn;
    
    $sql = sprintf('SELECT * FROM %s', $table);

    if (strlen($where) > 0) {
        $sql .= sprintf(' WHERE %s', $where);
    }

    if (strlen($order_by) > 0) {
        $sql .= sprintf(' ORDER BY %s', $order_by);
    }

    if ($limit_from > 0 || $limit_to > 0) {
        $sql .= sprintf(' LIMIT %d, %d', $limit_from, $limit_to);
    }

    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $rows = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        
        return $rows;
    } 
    catch(PDOException $e) 
    {
        echo 'FETCH ROWS ERROR: ' . $e->getMessage() . ' - ' . $sql;
    }
}

function table_fetch_sql($sql)
{
    global $conn;
    
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $rows = array();
        
        while($row = $stmt->fetch()) {
            $rows[] = $row;
        }
        
        return $rows;
    } 
    catch(PDOException $e) 
    {
        echo 'FETCH SQL ERROR: ' . $e->getMessage();
    }
}

function table_update_sql($sql)
{
    global $conn;
    
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } 
    catch(PDOException $e) 
    {
        echo 'EXECUTE SQL ERROR: ' . $e->getMessage();
		console_sql_log($sql, 'sql-update-sql.txt');
		exit();
    }
}

function table_insert_sql($sql)
{
    global $conn;
    
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $conn->lastInsertId();
    } 
    catch(PDOException $e) 
    {
        echo 'EXECUTE SQL ERROR: ' . $e->getMessage();
		console_sql_log($sql, 'sql-insert-sql.txt');
		exit();
    }
}

function table_fetch_distinct_rows($table, $columns, $where = '', $order_by = '')
{
    global $conn;
    
    $sql = sprintf('SELECT DISTINCT %s FROM %s', implode(', ', $columns), $table);

    if (strlen($where) > 0) 
    {
        $sql .= sprintf(' WHERE %s', $where);
    }

    if (strlen($order_by) > 0) 
    {
        $sql .= sprintf(' ORDER BY %s', $order_by);
    }
        
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $rows = array();
        
        while($row = $stmt->fetch()) {
            $rows[] = $row;
        }
        
        return $rows;
    } 
    catch(PDOException $e) 
    {
        echo 'FETCH DISTINCT ROWS ERROR: ' . $e->getMessage();
    }
}

function get_table_fields($table)
{
    global $conn;
    
    $sql = sprintf('SHOW COLUMNS FROM %s', $table);
        
    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $rows = array();

        while($row = $stmt->fetch()) {
            $rows[$row['Field']] = $row['Type'];
        }
        
        return $rows;
    } 
    catch(PDOException $e) 
    {
        echo 'TABLE FIELDS ERROR: ' . $e->getMessage();
    }
}

function table_insert($table, $fields, $values)
{
    global $conn;
    
    $table_fields = get_table_fields($table);

    $field_availables = array();
    $field_params = array();
    $execute_params = array();

    foreach ($fields as $field) 
    {
        $field = trim($field);
        if(isset($table_fields[$field]) && isset($values[$field]))
        {
            $field_availables[] = $field;
            $field_params[] = ':' . $field;
            $execute_params[':' . $field] = $values[$field];
        }
    }

    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $field_availables), implode(', ', $field_params));

    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute($execute_params);

        return $conn->lastInsertId();
    } 
    catch(PDOException $e) 
    {
        echo 'INSERT ERROR: ' . $e->getMessage();
        print_r($sql);
		console_sql_log($sql, 'sql-insert.txt');
		exit();
    }
    
    return NULL;
}


function table_update($table, $fields, $values, $where)
{
    global $conn;
    
    $table_fields = get_table_fields($table);

    $field_params = array();
    $execute_params = array();
    
    foreach ($fields as $field) 
    {
        if(isset($table_fields[$field]) && isset($values[$field]))
        {
            $field_params[] = sprintf('%s = :%s', $field, $field);
            $execute_params[':' . $field] = $values[$field];
        }
    }

    $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, implode(', ', $field_params), $where);

    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute($execute_params);
        
        return $stmt->rowCount();
    } 
    catch(PDOException $e) 
    {
        echo 'UPDATE ERROR: ' . $e->getMessage();
        print_r($sql);
		console_sql_log($sql, 'sql-update.txt');
		exit();
    }
}


function table_delete_row($table, $where)
{
    global $conn;
    
    $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);

    try 
    {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->rowCount();
    } 
    catch(PDOException $e) 
    {
        echo 'DELETE ERROR: ' . $e->getMessage();
    }
}

?>
