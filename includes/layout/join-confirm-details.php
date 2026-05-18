<!-- Wrapper -->
<section id="wrapper">
  <section id="one" class="wrapper style1">
      <div class="inner">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 mt-5">
              	
					<?php

					$params = array_map("sanitize_sql_string",$_POST);

					$check = table_fetch_row('accounts','email="'.$params['email'].'"');

					//check if member already exists
					if ($check) {
						// if exists but status = 0, move member to archived for logging purposes and delete the member so process can continue
						if ($check['status'] == '0'){
							$check['archived_date'] = date('Y-m-d H:i:s');
							$check['archived_reason'] = 'User had status of 0 when a new user with same email was created on the join page'; 
							table_insert('archived_accounts',array_keys($check),$check);
							table_delete_row('accounts','id="'.$check['id'].'"');
						} else {
							header('Location: /join?accountexists=true');
							exit();
						}
					}; 

					$fields = array
					(
					    'title',
					    'firstname',
					    'lastname',
					    'email',
					    'mobile_number',
					    'telephone_number',
					    'address_line_1',
					    'address_line_2',
					    'town',
					    'postcode',
					    'password',
					    'status',
					    'c_date'
					);

					$values = array
					(
					    'title' => $params['title'],
					    'firstname' => $params['firstname'],
					    'lastname' => $params['lastname'],
					    'email' => $params['email'],
						'mobile_number' => $params['mobile_number'],
						'telephone_number' => $params['telephone_number'],
					    'address_line_1' => $params['address_line_1'],
					    'address_line_2' => $params['address_line_2'],
					    'town' => $params['town'],
					    'postcode' => $params['postcode'],
					    'password' => md5($params['password']),
					    'status' => 0,
					    'c_date' => date('Y-m-d H:i:s')
					);

					//generate member but with status of 0;
					$newaccount_id = table_insert('accounts',$fields,$values);
					?>

					
					<h2>Confirm Details</h2>
					<table class="table table-striped table-responsive w-100">
						<tr>
							<th>Title</th>
							<td><?= $params['title'] ;?></td>
							<th>First Name</th>
							<td><?= $params['firstname'] ;?></td>
							</tr>
						<tr>
							<th>Surname</th>
							<td><?= $params['lastname'] ;?></td>
							<th>Email</th>
							<td><?= $params['email'] ;?></td>
						</tr>
						<tr>
							<th>Mobile</th>
							<td><?= $params['mobile_number'] ;?></td>
							<th>Telephone</th>
							<td><?= $params['telephone_number'] ;?></td>
						</tr>
						<tr>
							<th>Address 1</th>
							<td><?= $params['address_line_1'] ;?></td>
							<th>Address 2</th>
							<td><?= $params['address_line_2'] ;?></td>
						</tr>
						<tr>
							<th>Town</th>
							<td><?= $params['town'] ;?></td>
							<th>Post Code</th>
							<td><?= $params['postcode'] ;?></td>
						</tr>
					</table>
					
					<form method="POST" action="/account-success">
						<input  type="hidden" name="title" value="<?= $params['title'] ;?>" />
						<input  type="hidden" name="firstname" value="<?= $params['firstname'] ;?>" />
						<input  type="hidden" name="lastname" value="<?= $params['lastname'] ;?>" />
						<input  type="hidden" name="email" value="<?= $params['email'] ;?>" />
						<input  type="hidden" name="mobile" value="<?= $params['mobile_number'] ;?>" />
						<input  type="hidden" name="telephone" value="<?= $params['telephone_number'] ;?>" />
						<input  type="hidden" name="address1" value="<?= $params['address_line_1'] ;?>" />
						<input  type="hidden" name="address2" value="<?= $params['address_line_2'] ;?>" />
						<input  type="hidden" name="town" value="<?= $params['town'] ;?>" />
						<input  type="hidden" name="postcode" value="<?= $params['postcode'] ;?>" />
						<input  type="hidden" name="password" value="<?= md5($params['password']) ;?>" />
						<input  type="hidden" name="newaccount_id" value="<?= $newaccount_id; ?>" />
						<input  type="hidden" name="step3" value="1" />
						<div class="form-group">
							<input type="submit" class="red-btn-proceed btn-more-details-green-square" value="Confirm" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</section>