$(function() {

	/*$('.validate-form').validate();
	
	$('input[type=text],input[type=password]').focus(function() {
		$(this).addClass('focused');
	}).blur(function() {
		$(this).removeClass('focused');
	});
	
	$('.module').click(function() {
		$(this).parent().toggleClass('selected');
		return false;
	});
	
	$('table.list tbody > tr').mouseover(function() {
		$(this).addClass('hover');
	}).mouseout(function() {
		$(this).removeClass('hover');
	});*/
	
	$('.datepicker').each(function() {
		var $calendar_icon = $('<span class="calendar-icon"></span>');
		$calendar_icon.insertAfter(this);
		$calendar_icon.click(function() {
			$(this).prev().click();
		});
	});
	
	$('.datepicker').datepicker({
									dateFormat: 'dd/mm/yy'
								});
								
	$('.calendar-icon').click(function() {
		$(this).prev().focus();
	});
	
	/*$('.delete_file').click(function() {
		var path = $(this).attr('path'), $tr = $(this).parents('tr');
		
		$.get('ajax/delete-file.php?path=' + encodeURI(path), function() {
			$tr.remove();
		});
		
		return false;
	});*/

});