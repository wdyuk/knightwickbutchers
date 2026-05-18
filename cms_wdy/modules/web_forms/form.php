<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    

     
    if(!empty($table_id)) {
        $data = table_fetch_row('web_forms', 'id=' . $table_id);     
    }
    
?>
<div class="row mt-5">
    <div class="col-md-12">

		<div class="card mb-4">
		    <div class="card-header">
		        <h1>View Web Form</h1>
		    </div>
		    <div class="card-body">
				<a href="control-panel.php?module=web_forms&action=manage" class="btn btn-primary float-right"><i class="fa fa-caret-left"></i> Back to all emails</a>
				<h2>Subject: <?= $data['form_key']; ?></h2>
				<p><?= date('d/m/Y H:i', strtotime($data['ts'])); ?></p>
				<?php  echo $data['data'];?>
			</div>
		</div>
       
    </div>
</div>
