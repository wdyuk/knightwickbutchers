<?php
	
	require 'init.php';

	$id = (int)$_REQUEST['id'];
	$table = $_REQUEST['table'];

	$where = sprintf('id = %d', $id);

	
	table_delete_row($table, $where);
	
	
	if ($table == 'home_blocks') {
		$img_path = get_image_path('home_blocks/' . $id, true);
		@unlink($img_path);
	}
	
	if ($table == 'photos') {
		$img_path = get_image_path('photos/' . $id, true);
		@unlink($img_path);
	}

	if ($table == 'products') {
		table_delete_row('url_rewrite','table_name="product" AND table_id = "'.$id.'"');
	}

	if ($table == 'products') {
		table_delete_row('url_rewrite','table_name="product" AND table_id = "'.$id.'"');
		table_delete_row('products_categories','product_id = "'.$id.'"');
		table_delete_row('product_weights','product_id = "'.$id.'"');
	}

	if ($table == 'categories') {
		table_delete_row('url_rewrite','table_name="category" AND table_id = "'.$id.'"');
		table_delete_row('products_categories','category_id = "'.$id.'"');
	}

	
?>
