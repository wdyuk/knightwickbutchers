<?php

function get_block($id)
{
	$where = sprintf('id = %d', $id);
	return table_fetch_row('bloks', $where);
}

function show_blok_content($id)
{
	$b = get_block($id);
	echo stripcslashes($b['content']);
}

function record_form($form_key, $data, $html = 0)
{
	$fields = array('form_key', 'data', 'html');
	$values = array('form_key' => $form_key, 'data' => $data, 'html' => $html);
	
	table_insert('web_forms', $fields, $values);
}

function translate($txt)
{
	echo $txt;
}

function get_ip_address()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	  $ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	  $ip=$_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;
}

function find_lat_lng($address)
{
	$url = sprintf('http://maps.google.com/maps/geo?output=xml&key=%s&q=%s', GOOGLE_MAPS_API_KEY, urlencode($address));
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	$xml = simplexml_load_string($response);
	
	if ($xml->Response->Status->code != 200) {
		return false;
	} else {
		foreach ($xml->Response as $response) {
			foreach ($response->Placemark as $place) {
				list($longitude, $latitude) = split(',', $place->Point->coordinates);
				return array('longitude' => $longitude, 'latitude' => $latitude);
			}
		}
	}
}

function set_lat_lng($scrappage_id)
{
	$where = sprintf('id = %d', $scrappage_id);
	$scrappage = table_fetch_row('scrappage', $where);
	
	if (is_array($scrappage)) {
		$address = sprintf('%s %s, England', $scrappage['county'], $scrappage['town']);
		$lat_lng = find_lat_lng($address);
		
		if ($lat_lng !== false) {
			$fields = array('latitude', 'longitude');
			$values = array('latitude' => $lat_lng['latitude'], 'longitude' => $lat_lng['longitude']);
			$where = sprintf('id = %d', $scrappage_id);
			
			table_update('scrappage', $fields, $values, $where);
		}
	}
}

function get_homepage()
{
	global $db;
	
	$sql = sprintf('SELECT * FROM page WHERE status="ONLINE" ORDER BY position ASC LIMIT 0,1', $url);
	$r = mysqli_query($db, $sql);
	
	if (mysqli_num_rows($r) == 0) {
		return false;
	}
	
	return mysqli_fetch_assoc($r);
}

function get_page($url)
{
	global $db;
	
	
	$url = mysqli_real_escape_string($db, $url);
	
	$sql = sprintf('SELECT * FROM page WHERE url="%s" ORDER BY position ASC LIMIT 0,1', $url);
	$r = mysqli_query($db, $sql);
	
	if (mysqli_num_rows($r) == 0) {
		$sql = sprintf('SELECT * FROM page WHERE url="%s" ORDER BY position ASC LIMIT 0,1', addcslashes($url, '"\''));
		$r = mysqli_query($db, $sql);
		
		if (mysqli_num_rows($r) == 0) {
			return false;
		}
	}
	
	return mysqli_fetch_assoc($r);
}

function show_dropdown_menu($tree, $selected = -1, $parent_id = -1, $indent_level = 0)
{
	if ($indent_level == 0) {
		echo '<ul class="dropdown">';
	} else {
		echo '<ul class="submenu">';
	}
	
	$first_one = true;
	
	foreach ($tree as $tree_node) {
		$node = $tree_node['node'];
		$children = $tree_node['children'];
		
		if ($node['parent_id'] == $parent_id) {
			
			$url = $node['url'];
			if (strlen($node['link']) > 0) {
				$url = $node['link'];
			}
			
			if ($node['target'] != '_BLANK') {
				$node['target'] = '';
			}
			
			$url = stripcslashes($url);
			
			if ($selected == $url || ($first_one && strlen(trim($selected)) == 0)) {
				echo '<li class="selected">';
			} else {
				echo '<li>';
			}
			
			if ($selected == $url || ($first_one && strlen(trim($selected)) == 0)) {
				printf('<a target="%s" href="%s">%s</a>', $node['target'], $url, stripcslashes($node['menu_title']));
				$first_one = false;
			} else {
				printf('<a target="%s" href="%s">%s</a>', $node['target'], $url, stripcslashes($node['menu_title']));
			}
			
			if (count($children) > 0) {
				show_dropdown_menu($children, $selected, $node['id'], ($indent_level + 1));
			}
			
			echo '</li>';
		}
		
	}
	
	echo '</ul>';
}

?>