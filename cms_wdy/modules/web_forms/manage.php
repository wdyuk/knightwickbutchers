<?php

	$limit = 10;
	$page = 1;
	
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	$total_rows = table_row_count('web_forms');
	$total_pages = ceil($total_rows / $limit);
	
	$where = '';
	
	if (isset($_GET['key']) && $_GET['key'] != 'Show All') {
		$key = $_GET['key'];
		$where = sprintf('form_key = "%s"', $key);
	}
	
	
	$rows = table_fetch_rows('web_forms', $where, 'ts DESC', ($page-1) * $limit, $limit);
	foreach($rows as $key => $row) {
		$row['ts'] = date('d/m/Y H:i:s', strtotime($row['ts']));
		$rows[$key] = $row;
	}
?>

<form method="get" action="">
	<input name="module" type="hidden" value="web_forms" />
    <input name="action" type="hidden" value="manage" />
    
    <table>
    <tr>
    	<td>Select form to filter:</td>
        <td>
        	<?php
				
				$forms = table_fetch_distinct_rows('web_forms', array('form_key'), '', 'form_key ASC');
				
				$selected = array();
				
				if (isset($_GET['key'])) {
					$selected[] = $_GET['key'];
				}
				
				$forms = array_merge(array(array('form_key' => 'Show All')), $forms);
				
				show_list($forms, 'key', 'form_key', 'form_key', 'select', $selected);
				
			?>
        </td>
        <td><?php show_big_button('filter', 'Filter'); ?></td>
    </tr>
    </table>

</form>

<div class="card mb-4 mt-4">
    <div class="card-header">
        Web Forms
    </div>
    <div class="card-body">
        <div class="form-row align-items-center">

		<table class="list">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Date / Time</th>
			    <th>Subject</th>
			</tr>
			</thead>
			<tbody class="cursor-move">
				<?php 
					$operations = array('form','delete');
					show_rows($rows, 'web_forms', array('ts', 'form_key'), $operations);
				?>
			</tbody>
			<?php if (strlen($where) == 0) { ?>
				<tfoot>
				<tr>
					<td colspan="3"><?php show_pagination($total_pages, $page); ?></td>
				</tr>
				</tfoot>
			<?php } ?>
		</table>
	</div>
</div>

<?php require 'modules/delete.php'; ?>

<script type="text/javascript">
$(function() {
	$('table.list > tbody').sortable({
		axis: 'y',
		opacity: 0.5,
		stop: function (evt, ui) {
			var arr = [];
			
			$('table.list > tbody tr').each(function(i) {
				var row_id = $(this).attr('row_id');
				arr[arr.length] = 'positions[' + row_id + ']=' + i;
			});
			
			var params = 'table=web_forms&' + arr.join('&');
			$.post('ajax/sort-order.php', params, function() {
				
			});
		}
	});
	
});
</script>