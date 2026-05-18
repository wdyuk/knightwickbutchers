

<div class="container">
    <div class="col-md-12">
<?php

if (empty($accountData)) {
	header('Location: /login');
};


if (isset($_POST['submit-edit-details'])) {
	$params = array_map("sanitize_sql_string",$_POST);
	// $params['dob'] = $params['dobyear'].'-'.$params['dobmonth'].'-'.$params['dobday'];
	if(!isset($params['contactemail'])) { $params['contactemail'] = 0;};

	table_update('accounts',array_keys($params),$params,'id="'.$accountData['id'].'"');
	echo '<div class="alert alert-success">Updated details have been saved.</div>';
};
if (isset($_POST['submit-edit-password'])) {
	$params = array_map("sanitize_sql_string",$_POST);
	if (($params['password'] == "")|| ($params['confirm_password'] == "")) {
		echo '<div class="alert alert-warning">Password or confirm password missing</div>';
	}elseif ($params['password'] != $params['confirm_password']) {
		echo '<div class="alert alert-warning">Passwords don\'t match</div>';
	}else {
		$params['password'] = md5($params['password']);
		table_update('accounts',array_keys($params),$params,'id="'.$accountData['id'].'"');
		echo '<div class="alert alert-success">Your new password has been saved</div>';
		unset($_GET['change-password']);
	}
};
if (isset($_GET['dd-cancelled'])) {
		echo '<div class="alert alert-success">Your direct debit has been cancelled and your paid membership will not auto-renew.</div>';
}

$_SESSION['account'] = get_member($accountData['email']);
$accountData = $_SESSION['account'];

$address = '<br>';
if (strlen($accountData['address_line_1']) > 0) { $address .= $accountData['address_line_1'].'<br>';};
if (strlen($accountData['address_line_2']) > 0) { $address .= $accountData['address_line_2'].'<br>';};
if (strlen($accountData['town']) > 0) { $address .= $accountData['town'].'<br>';};
if (strlen($accountData['postcode']) > 0) { $address .= $accountData['postcode']; };


if ($accountData['contactemail'] == 0) {

	$contactemail = '<i class="fa fa-times in-a-box"></i>';

} else {

	$contactemail = '<i class="fa fa-check in-a-box"></i>';

}

/*if ($accountData['contactsms'] == 0) {

	$contactsms = '<i class="fa fa-times in-a-box"></i>';

} else {

	$contactsms = '<i class="fa fa-check in-a-box"></i>';

}
if ($accountData['contactpost'] == 0) {

	$contactpost = '<i class="fa fa-times in-a-box"></i>';

} else {

	$contactpost = '<i class="fa fa-check in-a-box"></i>';

}*/
?>
<div class="col-md-12 col-sm-12 col-xs-12 account-section">
	<div class="col-md-9 col-sm-12">
	<h2>Member ID # <span style="color: #333;"><?= $accountData['id']; ?></span></h2>
	<?php if (isset($_GET['edit-details'])): ?>
		<form class="account-form" method="POST">
			<div class="col-md-12 col-sm-12">
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="title">Title:</label><em>*</em>
						<select name="title" class="form-control" required>
							<option value="Mr" <?= ($accountData['title'] == "Mr") ? 'selected="selected"' : ''; ?>>Mr</option>
							<option value="Mrs" <?= ($accountData['title'] == "Mrs") ? 'selected="selected"' : ''; ?>>Mrs</option>
							<option value="Miss" <?= ($accountData['title'] == "Miss") ? 'selected="selected"' : ''; ?>>Miss</option>
							<option value="Master" <?= ($accountData['title'] == "Master") ? 'selected="selected"' : ''; ?>>Master</option>
							<option value="Dr" <?= ($accountData['title'] == "Dr") ? 'selected="selected"' : ''; ?>>Dr</option>
							<option value="Sir" <?= ($accountData['title'] == "Sir") ? 'selected="selected"' : ''; ?>>Sir</option>
							<option value="Lord" <?= ($accountData['title'] == "Lord") ? 'selected="selected"' : ''; ?>>Lord</option>
							<option value="Lady" <?= ($accountData['title'] == "Lady") ? 'selected="selected"' : ''; ?>>Lady</option>
							<option value="Madame" <?= ($accountData['title'] == "Madame") ? 'selected="selected"' : ''; ?>>Madame</option>
							<option value="Ms" <?= ($accountData['title'] == "Ms") ? 'selected="selected"' : ''; ?>>Ms</option>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="firstname">First Name:</label><em>*</em>
						<input type="text" name="firstname" value="<?= $accountData['firstname']; ?>" size="50"  class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="lastname">Surname:</label><em>*</em>
						<input type="text" name="lastname" value="<?= $accountData['lastname']; ?>" size="50"  class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="address_line_1">Address 1:</label><em>*</em>
						<input type="text" name="address_line_1" value="<?= $accountData['address_line_1']; ?>" size="50"  class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="address_line_2">Address 2:</label>
						<input type="text" name="address_line_2" value="<?= $accountData['address_line_2']; ?>" size="50"  class="form-control">
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="town">Town:</label><em>*</em>
						<input type="text" name="town" value="<?= $accountData['town']; ?>" size="50"  class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="postcode">Post Code:</label><em>*</em>
						<input type="text" name="postcode" value="<?= $accountData['postcode']; ?>" size="50"  class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="email">Email:</label><em>*</em>
						<input type="email" name="email" value="<?= $accountData['email']; ?>" size="50" class="form-control" required>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="telephone">Telephone:</label>
						<input type="text" name="telephone_number" value="<?= $accountData['telephone_number']; ?>" size="50"  class="form-control">
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="mobile">Mobile:</label>
						<input type="text" name="mobile_number" value="<?= $accountData['mobile_number']; ?>" size="50"  class="form-control">
					</div>
				</div>
				<!-- <div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="dobday">Date of Birth:</label><em>*</em><br>
						<select name="dobday" size="1" required>
						<?php/* $dateparts = explode('-',$accountData['dob']);
							for ($i=1; $i <= 31; $i++) {
								$day = str_pad($i,2,'0',STR_PAD_LEFT);
								?>
								<option value="<?= $day; ?>" <?= ($dateparts[2] == $day) ? 'selected="selected"' : ''; ?>><?= $day; ?></option>
								<?php
							};
						?>
						</select> - <select name="dobmonth" size="1" required>
						<?php
						for ($i=1; $i <= 12; $i++) {
								$month = str_pad($i,2,'0',STR_PAD_LEFT);
								?>
								<option value="<?= $month; ?>" <?= ($dateparts[1] == $month) ? 'selected="selected"' : ''; ?>><?= $month; ?></option>
								<?php
							}; ?>
						></select> -
						<select name="dobyear" size="1" required><option value="<?= $accountData['']; ?>" selected></option>
						<?php
						if ($accountData['membership'] == 4) { $datefrom = date('Y'); $dateto = (date('Y') - 16); } else { $datefrom = (date('Y') - 17); $dateto = (date('Y') - 100); };
						 for ($i=$datefrom; $i >= $dateto; $i--) {  ?>

							<option value="<?= $i; ?>" <?= ($dateparts[0] == $i) ? 'selected="selected"' : ''; ?>><?= $i ;?></option>
						<?php

						}; */?>
						</select>
					</div>
				</div> -->
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label for="contactemail">Contact me by:</label><div class="inputdiv"><input type="checkbox" name="contactemail" value="1" <?= ($accountData['contactemail'] == 1) ? 'checked="checked"' : ''; ?> style="width:1.2em;">Email <!-- <input type="checkbox" name="contactsms" value="1" <?=  ($accountData['contactsms'] == 1) ? 'checked="checked"' : ''; ?> style="width:1.2em;">Text <input type="checkbox" name="contactpost" value="1" <?= ($accountData['contactpost'] == 1) ? 'checked="checked"' : ''; ?> style="width:1.2em;">Post--></div>
					</div>
				</div>
				<div class="col-md-12 col-sm-12">
					<div class="form-group">
						<input type="submit" name="submit-edit-details" class="btn-more-details-green-square pull-right" value="Save" />
					</div>
				</div>
			</div>
		</form>
	<?php elseif (isset($_GET['change-password'])): ?>
		<form method="POST" class="account-form" name="submit-edit-password-form">
			<div class="col-md-12 no-padding" id="pwd-container">
			    <div class="col-md-6">
			    	<label for="password">Password:</label><em>*</em>
				    <section class="login-form">
				      <div class="form-group">
				          <input type="password" name="password" class="form-control input-lg" id="passwd1" placeholder="Password" required="" />
				      </div>
				    </section>
			    </div>
			    <div class="col-md-6">
			    	<label for="confirm_password">Confirm Password:</label><em>*</em>
				    <section class="login-form">
				      <div class="form-group">
				          <input type="password" name="confirm_password" class="form-control input-lg" id="confirm_password" placeholder="Confirm Password" required="" />
				      </div>
				    </section>
			    </div>
			    <div class="col-md-12">
			    	<div class="pwstrength_viewport_progress"></div>
			    </div>
			    <div class="col-sm-12">
					<div class="form-group">
						<input type="submit" name="submit-edit-password" class="red-btn pull-right btn-more-details-green-square" value="Save" />
					</div>
				</div>
		  	</div>
		</form>
	<?php elseif (isset($_GET['member-settings'])): ?>
		<div class="col-sm-12 no-padding">
			<h2>Cancel Account Immediately</h2>
			<p><button href="#" class="btn-more-details-red-square cancel-account">Cancel</button></p>
		</div>
	<?php else:?>
			<p><strong>Name: </strong><?= $accountData['title'].' '.$accountData['firstname'].' '.$accountData['lastname']; ?></p>
			<p><strong>Address: </strong><?= $address; ?></p>
			<p><strong>Email: </strong><?= $accountData['email']; ?></p>
			<p><strong>Telephone: </strong><?= $accountData['telephone_number']; ?></p>
			<p><strong>Mobile: </strong><?= $accountData['mobile_number']; ?></p>
			<!-- <p><strong>Sex: </strong><?= $sex; ?></p> -->
			<!-- <p><strong>DOB: </strong><?= $accountData['dob']; ?></p> -->
			<p><strong>Contact Preferences:</strong></p>
			<p>Email: <?= $contactemail; ?></p>
		<!-- <p>SMS: <?= $contactsms; ?></p>
			<p>Post: <?= $contactpost; ?></p> -->
	<?php endif; ?>
	</div>
	<div class="col-md-3 col-sm-12 account-settings-menu">
		<h2>Settings</h2>
		<a class="btn-more-details-green-square mt-20" href="/account">Account Info</a>
		<a class="btn-more-details-green-square mt-20 " href="/account?edit-details">Edit Details</a>
		<a class="btn-more-details-green-square mt-20" href="/account?change-password">Change Password</a>
		<a class="btn-more-details-red-square mt-20" href="/logout">Logout</a>
	</div>
</div>
</div>
</div>