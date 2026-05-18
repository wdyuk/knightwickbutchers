<?php

	$limit = 15;
	$page = 1;
	
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	$total_rows = table_row_count('homepage_slider');
	$total_pages = ceil($total_rows / $limit);
	
	$rows = table_fetch_rows('homepage_slider', '', 'position ASC', ($page-1) * $limit, $limit);
?>

<div class="card mb-4">
    <div class="card-header">
        Homepage Slider - Slides
    </div>
    <div class="card-body">
        <div class="form-row align-items-center">
        	<table class="list">
        		<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Heading 1</th>
						<th>Heading 2</th>
					</tr>
				</thead>
			
				<tbody class="cursor-move">
				<?php 
					$operations = array('form', 'delete');
					show_rows($rows, 'homepage_slider', array('heading_line_1','heading_line_2'), $operations);
				?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="3"><?php show_pagination($total_pages, $page); ?></td>
				</tr>
				</tfoot>
			</table>
		</div>
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

                                                var params = 'table=homepage_slider&' + arr.join('&');
                                                $.post('ajax/sort-order.php', params, function() {

                                                });
                                            }
					});
	
	$('.operation').live('click', function() {
        
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		
		if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=homepage_slider&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parents('tr');
			
			if (confirm('Are you sure you want to delete this?')) {
				$.post('ajax/delete.php', 'id=' + value + '&table=homepage_slider', function() {
					$li.remove();
				});
			}
		}
		
		return false;
	});
	
});
</script>

