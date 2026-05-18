<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('title','content','author','status');

        if($_POST['id'] == 0) 
        {
            table_insert('testimonials', $fields, $_POST);
            $messages[] = 'Saved successfully.';
            $table_id = mysqli_insert_id($db);
        }
        else 
        {
            table_update('testimonials', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }   
  
        // saveRewrite('testimonials',$table_id,'',$_POST['url']);
  
    }
     
    if(!empty($table_id)) {
        $data = table_fetch_row('testimonials', 'id=' . $table_id); 
        if($data !== false) {
            $data['url'] = getRewriteUrl('testimonials', $data['id']);
        }
    }

    
?>
<form class="validate-form" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
<input name="url" id="url" size="50" type="hidden" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
<table>
    <tr>
        <td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Testimonial</h1></td>
    </tr>
    <tr>
        <td colspan="2"><?php show_messages($messages); ?></td>
    </tr>
    <tr>
        <td>Title:</td>
        <td><input name="title" id="title" size="50" type="text" value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Content:</td>
        <td><?php show_fckeditor('content', isset($data['content']) ? $data['content'] : '' ); ?></td>
    </tr>
   
    <tr>
        <td>Status:</td>
        <td>
		<select name="status">
			<option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
			<option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
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
	$('#title').keyup(function() {
            var val = $('#title').val();

            val = val.toLowerCase();
            val = val.replace(/[^a-z0-9 ]+/g, '');
            val = val.replace('  ', ' ');

            var url = '/testimonials-' + val.replace(/\s/g, '-');

            $('#url').val(url);  
	});
});
</script>
