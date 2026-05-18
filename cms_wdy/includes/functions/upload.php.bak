<?php

function show_upload_component($table, $name, $directory)
{
	$upload_files_text = 'Upload Files';
	$clear_queue = 'Clear Queue';
	
	echo <<<HTML
	<tr>
		<td>&nbsp;</td>
		<td><input type="file" id="files" name="files" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<a id="file_upload_link" href="javascript:;">{$upload_files_text}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" onclick="javascript:$('#files').fileUploadClearQueue();">{$clear_queue}</a>
		</td>
	</tr>
	<script type="text/javascript">
	$(function() {
		var fileUploader = $('#files').fileUpload({
			'uploader':'resources/swf/uploader.swf',
			'script':'ajax/upload-files.php',
			'cancelImg':'resources/images/cancel.png',
			'folder':'/uploads/{$directory}/',
			'scriptData': {'ref_id': $('#{$name}').val(), 'table': '{$table}', 'field': '{$name}'},
			'multi':true
		});
		
		$('#{$name}').change(function(){
			 $('#files').fileUploadSettings('scriptData','&table={$table}&field={$name}&ref_id=' + $('#{$name}').val());
		});
		
		$('#file_upload_link').click(function() {
			$('#files').fileUploadStart();
		});
	});
	</script>
HTML;
}

?>