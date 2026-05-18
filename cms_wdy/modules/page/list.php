<div class="card mb-4"><!-- Search Results -->
    <div class="card-header">
        Pages
    </div>
    <div class="card-body">
        <div class="form-row align-items-center">
			<table class="width_100_percent">
			<tbody>
			<tr>
				<td colspan="3">
			    <div class="category-tree">
			    <?php
					$list = table_fetch_rows(TBL_PAGE, '', 'position ASC');
					$tree = get_parent_child_array($list);
					
					$operations = array('form', 'delete', 'status', 'browser-preview');
					show_category_tree($tree, $operations, 'id', 'menu_title');
				?>
			    </div>
			    </td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	
	$('.category-row').hover(function() {
		$(this).addClass('hover');
	}, function() {
		$(this).removeClass('hover');
	});
	
	$('.operation').unbind('click').click(function(evt) {
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		
		if (elm_class.indexOf('operation-status-online') > -1) {
			var params = 'table=<?php echo TBL_PAGE; ?>&id=' + value + '&status=OFFLINE';
			
			$(this).removeClass('operation-status-online').addClass('operation-status-offline');
			$.post('ajax/status.php', params);
		} else if (elm_class.indexOf('operation-status-offline') > -1) {
			var params = 'table=<?php echo TBL_PAGE; ?>&id=' + value + '&status=ONLINE';
			
			$(this).removeClass('operation-status-offline').addClass('operation-status-online');
			$.post('ajax/status.php', params);
		} else if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=<?php echo TBL_PAGE; ?>&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-upload') > -1) {
			var location = 'control-panel.php?module=<?php echo TBL_PAGE; ?>/photos&action=add&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-photos') > -1) {
			var location = 'control-panel.php?module=<?php echo TBL_PAGE; ?>/photos&action=list&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parent().parent().parent();
			
			if (confirm("Are you sure you want to delete this?")) {
				$.post('ajax/delete.php', 'id=' + value + '&table=<?php echo TBL_PAGE; ?>', function() {
					$li.remove();
				});
			}
		} else if (elm_class.indexOf('operation-browser-preview') > -1) {
			return true;
		}
		
		return false;
	});
	
	$('.category-tree ul').sortable({
										axis: 'y',
										opacity: 0.5,
										stop: function (evt, ui) {
											var arr = [];
											
											$('.category-tree .category-row').each(function(i) {
												var id = $(this).attr('value');
												arr[arr.length] = 'positions[' + id + ']=' + i;
											});
											
											var params = 'table=<?php echo TBL_PAGE; ?>&' + arr.join('&');
											$.post('ajax/sort-order.php', params, function() {
												
											});
										}
									});
	
});
</script>
