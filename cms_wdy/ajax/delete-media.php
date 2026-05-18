<?php
	
	require 'init.php';

	$id = (int)$_REQUEST['id'];
	$type = $_REQUEST['type'];
	$table = $_REQUEST['table'];
	$path_to_folder = BASE_DIR."media/".$_REQUEST['folder_path'];

	$where = sprintf('id = %d AND media_type = "%s"', $id, $type);
	
	$medias =table_fetch_rows($table, $where, 'position ASC'); 
	$mediaDIR = $path_to_folder;
	

	foreach ($medias as $media){
	if (file_exists($mediaDIR . '/' .$media['filename']))
		{
			unlink($mediaDIR . '/' .$media['filename']);
		}	
	}
	
	table_delete_row($table, $where);
	
?>
