<?php
	
	$messages = array();
	if (isset($_POST['save'])) {
		$fields = array('name');
		if (strlen($_POST['password']) > 0) {
			$fields[] = 'password';
			$_POST['password'] = md5($_POST['password']);
		}
		
		$where = sprintf('id = %d', get_id());
		
		table_update(TBL_ADMIN, $fields, $_POST, $where);
		
		$messages[] = 'Saved successfully.';
	}
	
	$id = get_id();
	$where = sprintf('id = %d', $id);
	$admin = table_fetch_row(TBL_ADMIN, $where);
	
?>
<form class="validate-form" method="post">
<?php show_id(); ?>
<table>
<tr>
	<td colspan="2"><h1>Edit Administrator</h1></td>
</tr>
<tr>
    <td colspan="2"><?php show_messages($messages); ?></td>
</tr>
<tr>
	<td>Name:</td>
    <td><input class="required form-control" name="name" id="name" size="40" type="text" value="<?php echo $admin['name']; ?>" /></td>
</tr>
<tr>
	<td>Email:</td>
    <td><input readonly="readonly" class="required form-control" name="email" id="email" size="40" type="text" value="<?php echo $admin['email']; ?>" /></td>
</tr>
<tr>
	<td>Password:</td>
    <td><input class="required form-control" name="password" id="password" size="40" type="text" value="" /></td>
</tr>
<tr>
	<td></td>
    <td><?php show_big_button('save', 'Save'); ?></td>
</tr>
</table>
</form>