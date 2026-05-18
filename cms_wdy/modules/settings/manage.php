<?php
	
	$messages = array();
	if (isset($_POST['save'])) {
		
		foreach ($_POST as $name => $value) {
			if (strpos($name, 'setting-') !== false) {
				save_setting($name, $value);
			}
		}
		
		$messages[] = 'Saved successfully.';
	}
	
?>
<div class="row">
    <div class="col-md-12">
        <?php if(!empty($messages)) {
           show_messages($messages);
        };
        if(!empty($errors)) {
           show_errors($errors);
        };
        ?>
    </div>
</div>
<form class="validate-form" method="post">
<div class="card mb-4">
        <div class="card-header">
            <h1>Edit Settings</h1>
        </div>
        <div class="card-body">
           
            <?php
				$rows = table_fetch_rows(TBL_SETTINGS, '', 'id ASC');
	
				foreach ($rows as $row) {
					?>
					<div class="form-group">
					    <label for="status"><?php translate($row['label']); ?>:</label>
						<?php draw_settings_control($row); ?>
					</div>
					<?php
				}
			?>
		<div class="form-group">
            <?php show_big_button('save', 'Save'); ?>
            </div>
        </div>
    </div>

</form>