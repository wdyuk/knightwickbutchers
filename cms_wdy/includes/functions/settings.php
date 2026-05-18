<?php

function get_settings_value($name)
{
	global $db;
	$where = sprintf('name="%s"', $name);
	$row = table_fetch_row(TBL_SETTINGS, $where);
	
	return $row['value'];
}

function save_setting($name, $value)
{
	$id = substr($name, strpos($name, '-') + 1);
	$fields = array('value');
	$values = array('value' => $value);
	$where = sprintf('id=%d', $id);
	
	table_update(TBL_SETTINGS, $fields, $values, $where);
}

function draw_settings_control($setting)
{
	
	switch ($setting['control']) {
		case 'input':
			
			switch ($setting['type']) {
				case 'text':
					printf('<input size="%d" type="text" class="%s form-control" name="setting-%d" value="%s" />', 
						   $setting['size'], $setting['class'], $setting['id'], $setting['value']);
					break;
					
				case 'radio':
				case 'checkbox':
					printf('<input type="%s" class="%s form-control" name="setting-%d" value="%s" />', 
						   $setting['type'], $setting['class'], $setting['id'], $setting['value']);
					break;
			}
			
			break;
			
		case 'select':
			$rows = table_fetch_distinct_rows($setting['table'], array($setting['column']), '', $setting['column'] . ' ASC');
			
			printf('<select class="%s form-control" name="setting-%s">', $setting['class'], $setting['id']);
			
			foreach ($rows as $row) {
				if ($row[$setting['column']] == $setting['value']) {
					printf('<option selected="selected" value="%s">%s</option>', $row[$setting['column']], $row[$setting['column']]);
				} else {
					printf('<option value="%s">%s</option>', $row[$setting['column']], $row[$setting['column']]);
				}
			}
			
			echo '</select>';
			
			break;
			
		case 'textarea':
			
			break;
	}
	
}

?>