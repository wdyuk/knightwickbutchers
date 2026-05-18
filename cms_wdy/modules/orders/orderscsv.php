<form class="validate-form" method="post" enctype="multipart/form-data" action="/admin/csvdownload.php">
<input type="hidden" name="orders_csv" value="1" />
<table>
   
    <tr>
    	<td>From Date:<br>
    	<input name="fromdate" id="fromdate" size="50" type="text" class="datepicker" value="" /></td>
    	<td>To Date:<br><input name="todate" id="todate" size="50" type="text" class="datepicker" value="" /></td></tr>
    	<tr><td colspan="2">Filter by event<br>
	    	<select name="event">
	    		<option value="">All Events</option>
	    		<?php
	    		$tickets = table_fetch_rows('tickets','','date DESC');
	    		 foreach($tickets as $ticket) {
	    			?>
	    				<option value="<?= $ticket['id']; ?>"><?= $ticket['match_name'].' - '.date('d/m/Y', strtotime($ticket['date'])); ?></option>
	    			<?php 
	    		}; ?>
	    	</select>
	    </td></tr>
	    <tr>
        <td><?php show_big_button('submit', 'Download csv'); ?></td><td></td></tr>
    </tr>
</table>
</form>
<script type="text/javascript">
$(function() {
});
</script>
