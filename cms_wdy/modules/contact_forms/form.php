<?php
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    $notifications = array();

    $table_id = get_id();


    if(isset($_POST['save']))
    {
    	

    	$enquiry = '';

    	
    	


        $fields = array('contact_name','contact_email','contact_message', 'status');



    	if($_POST['id'] == 0) 
    	{
    		
    		table_insert('contact', $fields, $_POST);
    		$messages[] = 'Saved successfully.';
    		$table_id = mysqli_insert_id($db);
    	}
    	else 
    	{
    		table_update('contact', $fields, $_POST, 'id=' . get_id());
    		$messages[] = 'Saved successfully.';
    	}  

    };
    if(!empty($table_id)) {
            $data = table_fetch_row('contact', 'id=' . $table_id); 
        }
	?>
	<form class="validate-form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
		<input name="url" id="url" size="50" type="hidden" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
		<?php if ($table_id == 0) {?>
		<input name="enquiry_date" id="enquiry_date" type="hidden" value="<?php echo date('Y-m-d');?>" />
		<?php } ?>
		<table>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Contact</h1></td>
			</tr>
			<tr>
				<td colspan="2"><?php show_messages($messages); ?></td>
			</tr>
			<tr>
				<td>Reference Number:</td>
				<td>#<?php echo $data['id'];?></td>
			</tr>
			
			<tr>
				<td>Name:</td>
                <td><?php echo $data['contact_name'];?></td>
			</tr>            
            <tr>
                <td>Email:</td>
                <td><?php echo $data['contact_email'];?></td>
            </tr>            

			<tr>
				<td>Message:</td>
                <td><?php echo $data['contact_message'];?></td>
			</tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="status">
                        <option value="Inquiry" <?php echo (isset($data['status']) && $data['status'] == 'Inquiry') ? 'selected="selected"' : ''; ?> >Inquiry</option>
                        <option value="Resolved" <?php echo (isset($data['status']) && $data['status'] == 'Resolved') ? 'selected="selected"' : ''; ?> >Resolved</option>
                    </select>
                </td>
            </tr>
    <tr>
    	<td></td>
    	<td><?php show_big_button('save', 'Save'); ?></td>  	
    </tr>
</table>
</form>
<script type="text/javascript">
$(function() {

	
	var notecount = 0;
	function addNoteForm() 
	{
		var addnote = '';
		notecount++;
		addnote += '<div style="display:block;padding:10px 0px;">';
		addnote += '<textarea cols="50" name="notes[' + notecount + ']" value="" placeholder="Type Note Info Here" ></textarea>';
		
		
		
		addnote += '</div>';
		$('#Notes').append(addnote);
		
	}
	$('#AddNote').click(function(){
		addNoteForm();
	});
	
});

</script>


