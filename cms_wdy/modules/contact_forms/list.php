<?php

	$limit = 30;
	$page = 1;
	
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	$total_rows = table_row_count('contact');
	$total_pages = ceil($total_rows / $limit);

	$rows = table_fetch_rows('contact', '', 'id DESC', ($page-1) * $limit, $limit);

?>

<table class="list">
<thead>
<tr>
	<td colspan="9"><h1>Contact Form Enquiries</h1></td>
</tr>

<thead>

<tr><td>&nbsp;</td></tr>
<tr>
	<th>&nbsp;</th>
	<th>Name</th>
	<th>Email</th>
	<th>Message</th>
	<th>Status</th>
</tr>

</thead>
	
<tbody class="cursor-move">
<?php 
if (!$rows) {?>
	<tr><td colspan="7">No Inquiries matching these criteria</td></tr>
<?php };
	$operations = array('form', 'delete');
	show_rows($rows, 'contact', array('contact_name','contact_email','contact_message', 'status'), $operations);
?>
</tbody>
<tfoot>
<tr> 
	<td colspan="7"><?php show_pagination($total_pages, $page); ?></td>
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

                                                var params = 'table=contact&' + arr.join('&');
                                                $.post('ajax/sort-order.php', params, function() {

                                                });
                                            }
					});
	
	$('.operation').live('click', function() {
        
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		
		if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=contact_forms&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parents('tr');
			
			if (confirm('Are you sure you want to delete this?')) {
				$.post('ajax/delete.php', 'id=' + value + '&table=contact', function() {
					$li.remove();
				});
			}
		}
		
		return false;
	});
	
});
</script>


