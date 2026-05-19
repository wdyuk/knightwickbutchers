<?php

function homepage_popup_table_name()
{
    return 'homepage_popup';
}

function homepage_popup_default_config()
{
    return array(
        'id' => 1,
        'enabled' => '0',
        'title' => '',
        'content' => '',
        'button_text' => '',
        'button_url' => '',
        'updated_at' => '',
    );
}

function homepage_popup_table_exists()
{
    global $conn;

    static $exists = null;

    if ($exists !== null) {
        return $exists;
    }

    try {
        $stmt = $conn->prepare('SHOW TABLES LIKE :table_name');
        $stmt->execute(array(':table_name' => homepage_popup_table_name()));
        $exists = ($stmt->fetchColumn() !== false);
    } catch (PDOException $e) {
        $exists = false;
    }

    return $exists;
}

function homepage_popup_get_config()
{
    $defaults = homepage_popup_default_config();

    if (!homepage_popup_table_exists()) {
        return $defaults;
    }

    $data = table_fetch_row(homepage_popup_table_name(), 'id = 1');

    if (!$data) {
        return $defaults;
    }

    return array_merge($defaults, $data);
}

function homepage_popup_save_config($data)
{
    if (!homepage_popup_table_exists()) {
        return false;
    }

    $fields = array('enabled', 'title', 'content', 'button_text', 'button_url', 'updated_at');
    $config = array_merge(homepage_popup_default_config(), $data);
    $existing = table_fetch_row(homepage_popup_table_name(), 'id = 1');

    if ($existing) {
        table_update(homepage_popup_table_name(), $fields, $config, 'id = 1');
    } else {
        $fields_with_id = array_merge(array('id'), $fields);
        table_insert(homepage_popup_table_name(), $fields_with_id, $config);
    }

    return $config;
}

function homepage_popup_delete_image()
{
    delete_files('popup-image', 'homepage_popup');
}

function homepage_popup_get_image()
{
    return get_file('popup-image', 'homepage_popup');
}

function homepage_popup_get_image_url()
{
    $file = homepage_popup_get_image();

    if ($file === false) {
        return false;
    }

    return UPLOADS_URL . $file;
}

?>
