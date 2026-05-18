<?php

function show_list($list, $name, $value, $label, $type, $selected = array())
{
	printf('<div id="%s-list" class="%s-list">', $name, $type);
	
	
	if ($type == 'select') {
		printf('<select name="%s" id="%s" class="form-control">', $name, $name);
	}
	
	foreach ($list as $item) {
		
		switch ($type)
		{
			case 'select':
				if (in_array($item[$value], $selected)) {
					printf('<option selected="selected" value="%s">%s</option>', $item[$value], $item[$label]);
				} else {
					printf('<option value="%s">%s</option>', $item[$value], $item[$label]);
				}
				break;
				
			case 'checkbox':
			case 'radio':
				if (in_array($item[$value], $selected)) {
					printf('<div class="list-row"><label><input type="%s" class="form-control" checked="checked" name="%s" value="%s"> %s</label></div>', $type, $name, $item[$value], $item[$label]);
				} else {
					printf('<div class="list-row"><label><input type="%s" class="form-control" name="%s" value="%s"> %s</label></div>', $type, $name, $item[$value], $item[$label]);
				}
				break;
		}
		
	}
	
	if ($type == 'select') {
		echo '</select>';
	}
	
	echo '</div>';
}

?>