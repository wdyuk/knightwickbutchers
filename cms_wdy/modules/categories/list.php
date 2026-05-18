<?php

	$limit = 15;
	$page = 1;
	
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	$total_rows = table_row_count('categories');
	$total_pages = ceil($total_rows / $limit);
	
	$rows = table_fetch_rows('categories', '', 'position ASC', ($page-1) * $limit, $limit);
?>

<table class="list">
<thead>
<tr>
	<td colspan="3"><h1>Categories</h1></td>
</tr>

<thead>
<tr>
	<th>&nbsp;</th>
	<th>name</th>
</tr>

</thead>
	
<tbody class="cursor-move">
<?php 
	$operations = array('form', 'delete');
	show_rows($rows, 'categories', array('name'), $operations);
?>
</tbody>
<tfoot>
<tr>
	<td colspan="3"><?php show_pagination($total_pages, $page); ?></td>
</tr>
</tfoot>
</table>

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

                                                var params = 'table=categories&' + arr.join('&');
                                                $.post('ajax/sort-order.php', params, function() {

                                                });
                                            }
					});
	
	$('.operation').live('click', function() {
        
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		
		if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=categories&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parents('tr');
			
			if (confirm('Are you sure you want to delete this?')) {
				$.post('ajax/delete.php', 'id=' + value + '&table=categories', function() {
					$li.remove();
				});
			}
		}
		
		return false;
	});
	
});
</script>

