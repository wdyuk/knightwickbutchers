<script type="text/javascript">
$(function() {
	
	$('.delete,.operation-delete,.operation-deletea2s').click(function() {
		
		if (confirm("Are you sure you want to delete this?")) {
			var $tr = $(this).parents('tr');
			var row_id = $tr.attr('row_id'), table = $tr.attr('table');
			
			$.post('ajax/delete.php', 'id=' + row_id + '&table=' + table, function() {
				$tr.remove();
			});
		}
		
		return false;
	});
	
});
</script>