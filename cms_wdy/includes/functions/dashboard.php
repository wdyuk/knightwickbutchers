<?php

function add_points($event_name, $entity_id, $remove = false, $override_user_id = NULL)
{
	if (!isset($_SESSION['admin']['id'])) {
		return false;
	};
	if (!isset($event_name)) {
		return false;
	};
	if (!isset($entity_id) || !is_numeric($entity_id)) {
		return false;
	};

	$event_name = sanitize_sql_string($event_name);
	$entity_id = (int)$entity_id;
	$now = date('Y-m-d H:i:s');

	$event = table_fetch_row('hub_score_events','name="'.$event_name.'"');

	if (!$event) {
		return false;
	}
	if ($remove == true) {
		$event['points'] = '-'.$event['points'];
	}
	$admin_id = $_SESSION['admin']['id'];
	if (isset($override_user_id)) {
		if (!is_numeric($override_user_id)) {
			return false;
		} 
		$admin_id = (int)$override_user_id;
	}

	//check if entity_id is for applicant_id or property_id based on event
	if ($event['id'] == 1) {
		table_insert('hub_scores',array('admin_id','applicant_id','event_id','points','created_at'),
		array('admin_id' => $admin_id,'applicant_id' => $entity_id,'event_id' => $event['id'],'points' => $event['points'],'created_at' => $now));
	} else {
		table_insert('hub_scores',array('admin_id','property_id','event_id','points','created_at'),
		array('admin_id' => $admin_id,'property_id' => $entity_id,'event_id' => $event['id'],'points' => $event['points'],'created_at' => $now));
	}
	
	return true;
}

