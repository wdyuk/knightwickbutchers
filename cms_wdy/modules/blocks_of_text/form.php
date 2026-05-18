<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('title','page_title', 'content','meta_keywords','meta_description','status');
        


        if($_POST['id'] == 0) 
        {
            table_insert('blocks_of_text', $fields, $_POST);
            $messages[] = 'Saved successfully.';
            $table_id = db_insert_id();
        }
        else 
        {
            table_update('blocks_of_text', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }   

        if( isset($_POST['delete']) ){
            foreach($_POST['delete'] as $image) {
                if( file_exists('../' . $image) ) {
                    unlink('../' . $image);
                }
            }
        }

        if(isset($_FILES['image']) && !empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] == 0) {
            $imgData = pathinfo($_FILES['image']['name']);
            $image = new SimpleImageAdmin();
            $image->load($_FILES['image']['tmp_name']);
            if ($image->getWidth() > 300) {
                $image->resizeToWidth(300);
            };
            $image->save(UPLOADS_DIR . 'blocks_of_text/' .$table_id . '.' .$imgData['extension']);
                        
        }
        
        saveRewrite('blocks_of_text',$table_id,'',$_POST['url']);
  
    }
     
    if(!empty($table_id)) {
        $data = table_fetch_row('blocks_of_text', 'id=' . $table_id); 
        if($data !== false) {
            	$data['url'] = getRewriteUrl('blocks_of_text', $data['id']);
		$date = explode('-',$data['blocks_of_text_date']);
		$data['blocks_of_text_date'] = $date[2]  . '/' . $date[1]  . '/' . $date[0];
        }
    }
    
?>
<form class="validate-form" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
<input name="url" id="url" size="50" type="hidden" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
<table>
    <tr>
        <td colspan="2"><?php show_messages($messages); ?></td>
    </tr>
    <tr>
        <td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Blocks of Text</h1></td>
    </tr>
    <tr>
        <td>Title:</td>
        <td><input name="title" id="title" size="50" type="text" value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" /></td>
    </tr>

    <tr>
        <td valign="top">Image (must be square image of at least 300px)</td>
        <td>
            <input type="file" name="image"/>
            <?php 
                if(!empty($data)){
                    if(get_image('blocks_of_text/'.$data['id'])){
                        $path = get_image('blocks_of_text/'.$data['id']);
                       echo sprintf('<p><img src="%s" style="max-width:250px;"/></p>',$path);    
                       ?>
                       <label><input type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                       <?php  
                    }
                }
            ?>
        </td>
    </tr>
    <tr>
        <td>Content:</td>
        <td><?php show_fckeditor('content', isset($data['content']) ? $data['content'] : '' ); ?></td>
    </tr>
    <tr>
	<td valign="top">Meta keywords:</td>
        <td><textarea cols="50" rows="3" name="meta_keywords" id="meta_keywords"><?php echo stripslashes($data['meta_keywords']); ?></textarea></td>
    </tr>
    <tr>
        <td valign="top">Meta Description:</td>
        <td><textarea cols="50" rows="3" name="meta_description" id="meta_description"><?php echo stripslashes($data['meta_description']); ?></textarea></td>
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
	$('#title,#page_title').live('keyup input focusout change',function() {
        var val = $(this).val();
        if(val == ''){
            return;
        }
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9 ]+/g, '');
        val = val.replace('  ', ' ');

        var url = '/blocks_of_text-' + val.replace(/\s/g, '-') + '.html';

        $('#url').val(url);  
	});
    $('#title').trigger('keyup');
});
</script>
