<?php

function blocked_fulfilment_dates_table_name()
{
    return 'blocked_fulfilment_dates';
}

function blocked_fulfilment_dates_table_exists()
{
    global $conn;

    static $exists = null;

    if ($exists !== null) {
        return $exists;
    }

    try {
        $stmt = $conn->prepare('SHOW TABLES LIKE :table_name');
        $stmt->execute(array(':table_name' => blocked_fulfilment_dates_table_name()));
        $exists = ($stmt->fetchColumn() !== false);
    } catch (PDOException $e) {
        $exists = false;
    }

    return $exists;
}

function blocked_fulfilment_date_types()
{
    return array(
        'delivery' => 'Delivery',
        'collection' => 'Collection',
        'both' => 'Both',
    );
}

function blocked_fulfilment_dates_fetch_all()
{
    if (!blocked_fulfilment_dates_table_exists()) {
        return array();
    }

    return table_fetch_rows(blocked_fulfilment_dates_table_name(), '', 'blocked_date ASC, fulfilment_type ASC');
}

function blocked_fulfilment_dates_get_map()
{
    static $map = null;

    if ($map !== null) {
        return $map;
    }

    $map = array(
        'delivery' => array(),
        'collection' => array(),
        'both' => array(),
    );

    foreach (blocked_fulfilment_dates_fetch_all() as $row) {
        if (!isset($map[$row['fulfilment_type']])) {
            continue;
        }

        $normalized = preferred_fulfilment_date_normalize($row['blocked_date']);

        if ($normalized === false) {
            continue;
        }

        $map[$row['fulfilment_type']][] = $normalized;
    }

    foreach ($map as $type => $dates) {
        $map[$type] = array_values(array_unique($dates));
    }

    return $map;
}

function blocked_fulfilment_date_is_blocked($date, $fulfilmentType = 'delivery')
{
    $normalizedDate = preferred_fulfilment_date_normalize($date);

    if ($normalizedDate === false) {
        return false;
    }

    $fulfilmentType = strtolower(trim((string) $fulfilmentType));
    $map = blocked_fulfilment_dates_get_map();

    if (in_array($normalizedDate, $map['both'], true)) {
        return true;
    }

    if (isset($map[$fulfilmentType]) && in_array($normalizedDate, $map[$fulfilmentType], true)) {
        return true;
    }

    return false;
}

function blocked_fulfilment_dates_badge_class($type)
{
    switch ($type) {
        case 'delivery':
            return 'badge-primary';
        case 'collection':
            return 'badge-success';
        case 'both':
            return 'badge-dark';
        default:
            return 'badge-secondary';
    }
}

?>
