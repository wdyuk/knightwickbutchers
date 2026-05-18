<?php

function get_parent_child_array($list, $parent_id = -1)
{
	global $db;
	
	$tree = array();
	
	foreach ($list as $node) {
		if ($node['parent_id'] == $parent_id) {
			$tree[] = array('node' => $node, 'children' => get_parent_child_array($list, $node['id']));
		}
	}
	
	return $tree;
}

function format_tree_node($node, $name, $type, $value, $label, $indent_level, $selected)
{
	switch ($type)
	{
		case 'select':
			if ($selected == $node[$value]) {
				return sprintf('<option selected="selected" value="%s">%s%s</option>', $node[$value], str_repeat('&nbsp;', $indent_level * 5), $node[$label]);
			} else {
				return sprintf('<option value="%s">%s%s</option>', $node[$value], str_repeat('&nbsp;', $indent_level * 5), $node[$label]);
			}
		
		case 'radio':
		case 'checkbox':
			if ($selected == $node[$value]) {
				return sprintf('<div class="tree-row"><label>%s<input checked="checked" type="%s" name="%s" value="%s"> %s</label></div>', 
					   str_repeat('&nbsp;', $indent_level * 5), $type, $name, $node[$value], $node[$label]);
			} else {
				return sprintf('<div class="tree-row"><label>%s<input type="%s" name="%s" value="%s"> %s</label></div>', 
					   str_repeat('&nbsp;', $indent_level * 5), $type, $name, $node[$value], $node[$label]);
			}
			break;
	}
}

function get_category_operations($operations, $node, $value)
{
	if (is_array($operations) && count($operations) > 0) {
		$output = array();
		
		foreach ($operations as $operation) {
			
			if ($operation == 'status') {
				$output[] = sprintf('<a class="operation-%s-%s operation" value="%s">&nbsp;</a>', $operation, strtolower($node['status']), $value);
			} elseif ($operation == 'browser-preview') {
				if(isset($node['url'])) {
					$href = $node['url'];
				}
				
				if(isset($node['link'])) {
					if (strlen(trim($node['link'])) > 0) {
						$href = $node['link'];
					}
				}
				if (isset($href) && strlen($href) > 0) {
					$output[] = sprintf('<a target="_blank" href="%s" class="operation-%s operation" value="%s">&nbsp;</a>', $href, $operation, $value);
				} else {
					continue;
				}
				
			} else {
				$output[] = sprintf('<a class="operation-%s operation" value="%s">&nbsp;</a>', $operation, $value);
			}
		}
		
		return implode('', $output);
	} else {
		return '';
	}
}

function show_category_tree($tree, $operations, $value, $label, $parent_id = -1, $indent_level = 0)
{
	echo '<ul>';
	
	foreach ($tree as $tree_node) {
		$node = $tree_node['node'];
		$children = $tree_node['children'];
		
		if ($parent_id == $node['parent_id']) {
			echo '<li>';
			
			printf('<div class="category-row indent-%d" value="%s"><div class="label">%s</div> <div class="operations">%s</div><div class="clear"></div></div>',
				   $indent_level, $node[$value], stripcslashes($node[$label]), get_category_operations($operations, $node, $node[$value]));
			
			if (count($children) > 0) {
				show_category_tree($children, $operations, $value, stripcslashes($label), $node['id'], ($indent_level + 1));
			}
			
			echo '</li>';
		}
		
	}
	
	echo '</ul>';
}

function show_category_list($tree, $operations, $value, $label)
{
	echo '<ul>';
	
	foreach ($tree as $tree_node) {
		$node = $tree_node;
		
		echo '<li>';
		printf('<div class="category-row indent-%d" value="%s"><div class="label">%s</div> <div class="operations">%s</div><div class="clear"></div></div>',
			   $indent_level, $node[$value], stripcslashes($node[$label]), get_category_operations($operations, $node, $node[$value]));
		echo '</li>';
	}
	
	echo '</ul>';
}

function show_tree($tree, $name, $type, $value, $label, $first_node = array(), $selected = -1)
{
	if ($type == 'menu') {
		show_menu($tree, $name, $type, $value, $label, 0, $selected);
	} else {
		$result = get_tree($tree, $name, $type, stripcslashes($value), $label, -1, 0, $selected);
		
		$output = '';
		if (is_array($first_node) && count($first_node) > 0) {
			$output = format_tree_node($first_node, $name, $type, stripcslashes($value), $label, 0, $selected);
		}
		
		$output .= implode('', $result);
		
		switch ($type)
		{
			case 'select':
				printf('<select name="%s" class="form-control" id="%s">%s</select>', $name, $name, $output);
				break;
				
			case 'radio':
			case 'checkbox':
				printf('<div class="%s-tree form-control" id="%s-tree">%s</select>', $type, $name, $output);
				break;
		}
	}
}

function get_tree($tree, $name, $type, $value, $label, $parent_id = -1, $indent_level = 0, $selected = -1)
{
	$output = array();
	
	foreach ($tree as $tree_node) {
		$node = $tree_node['node'];
		$children = $tree_node['children'];
		
		if ($node['parent_id'] == $parent_id) {
			$output[] = format_tree_node($node, $name, $type, $value, stripcslashes($label), $indent_level, $selected);
			if (count($children) > 0) {
				$sub_tree = get_tree($children, $name, $type, $value, stripcslashes($label), $node['id'], ($indent_level + 1), $selected);
				$output = array_merge($output, $sub_tree);
			}
		}
		
	}
	
	return $output;
}

function show_menu($tree, $name, $type, $value, $label, $parent_id = -1, $indent_level = 0, $selected = -1)
{
	if ($indent_level == 0) {
		echo '<ul class="dropdown">';
	} else {
		echo '<ul class="submenu">';
	}
	
	foreach ($tree as $tree_node) {
		$node = $tree_node['node'];
		$children = $tree_node['children'];
		
		if ($node['parent_id'] == $parent_id) {
			
			echo '<li>';
			
			if ($selected == $node[$value]) {
				printf('<a href="%s" class="selected">%s</a>', $node[$value], stripcslashes($node[$label]));
			} else {
				printf('<a href="%s">%s</a>', $node[$value], stripcslashes($node[$label]));
			}
			
			if (count($children) > 0) {
				show_menu($children, $name, $type, $value, stripcslashes($label), $node['id'], ($indent_level + 1));
			}
			
			echo '</li>';
		}
		
	}
	
	echo '</ul>';
}

?>