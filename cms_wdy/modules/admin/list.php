<?php

	$limit = 10;
	$page = 1;
	
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	$total_rows = table_row_count(TBL_ADMIN);
	$total_pages = ceil($total_rows / $limit);
	
	$rows = table_fetch_rows(TBL_ADMIN, '', 'name ASC', ($page-1) * $limit, $limit);
?>
<table class="list">
<thead>
<tr>
	<td colspan="3"><h1>Administrators List</h1></td>
</tr>
<tr>
	<th>&nbsp;</th>
	<th>Name</th>
    <th>Email</th>
</tr>
</thead>
<tbody>
	<?php show_rows($rows, TBL_ADMIN, array('name', 'email')); ?>
</tbody>
<tfoot>
<tr>
	<td colspan="3"><?php show_pagination($total_pages, $page); ?></td>
</tr>
</tfoot>
</table>

<?php require 'modules/delete.php'; ?>