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

function fulfilment_date_override_statuses()
{
    return array(
        'blocked' => 'Blocked',
        'available' => 'Available',
    );
}

function blocked_fulfilment_dates_fetch_all()
{
    if (!blocked_fulfilment_dates_table_exists()) {
        return array();
    }

    return table_fetch_rows(blocked_fulfilment_dates_table_name(), '', 'blocked_date ASC, fulfilment_type ASC');
}

function fulfilment_date_overrides_get_map()
{
    static $map = null;

    if ($map !== null) {
        return $map;
    }

    $map = array(
        'blocked' => array('delivery' => array(), 'collection' => array(), 'both' => array()),
        'available' => array('delivery' => array(), 'collection' => array(), 'both' => array()),
    );

    foreach (blocked_fulfilment_dates_fetch_all() as $row) {
        $type = isset($row['fulfilment_type']) ? $row['fulfilment_type'] : '';
        $status = isset($row['availability_status']) ? $row['availability_status'] : 'blocked';
        $normalized = preferred_fulfilment_date_normalize($row['blocked_date']);

        if (!isset($map[$status][$type]) || $normalized === false) {
            continue;
        }

        $map[$status][$type][] = $normalized;
    }

    foreach ($map as $status => $types) {
        foreach ($types as $type => $dates) {
            $map[$status][$type] = array_values(array_unique($dates));
        }
    }

    return $map;
}

function blocked_fulfilment_dates_get_map()
{
    $overrides = fulfilment_date_overrides_get_map();
    return $overrides['blocked'];
}

function fulfilment_date_is_explicitly_available($date, $fulfilmentType = 'delivery')
{
    $normalizedDate = preferred_fulfilment_date_normalize($date);

    if ($normalizedDate === false) {
        return false;
    }

    $fulfilmentType = strtolower(trim((string) $fulfilmentType));
    $map = fulfilment_date_overrides_get_map()['available'];

    return in_array($normalizedDate, $map['both'], true)
        || (isset($map[$fulfilmentType]) && in_array($normalizedDate, $map[$fulfilmentType], true));
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
