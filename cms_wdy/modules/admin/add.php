<?php
	
	$messages = array();
	if (isset($_POST['save'])) {
		$email = $_POST['email'];
		$_POST['password'] = md5($_POST['password']);
		
		if (admin_exists($email)) {		
			$messages[] = 'Email is already registered.';
		} else {
			$fields = array('name', 'email', 'password');
			table_insert(TBL_ADMIN, $fields, $_POST);
			
			$messages[] = 'Saved successfully.';
		}
	}
	
?>
<form class="validate-form" method="post">
<table>
<tr>
	<td colspan="2"><h1>Add Administrator</h1></td>
</tr>
<tr>
    <td colspan="2"><?php show_messages($messages); ?></td>
</tr>
<tr>
	<td>Name:</td>
    <td><input class="required form-control" name="name" id="name" size="40" type="text" value="" /></td>
</tr>
<tr>
	<td>Email:</td>
    <td><input class="required form-control" name="email" id="email" size="40" type="text" value="" /></td>
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