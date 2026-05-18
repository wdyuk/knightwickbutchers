<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = 1;
    
    if(isset($_POST['save']))
    {
            
    	if( isset($_POST['delete-image']) ){
    		foreach($_POST['delete-image'] as $photo) {
    		    table_delete_row('module_images', 'groupings="'. $photo . '"');
    		}
    	}


        if(isset($_FILES['photo'])) {
            bulk_upload_image($_FILES['photo'], 'gallery', $table_id);
        }
  
    }
     

?>
<form class="validate-form" method="post" enctype="multipart/form-data">

<table>
    <tr>
        <td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Gallery</h1></td>
    </tr>

    
    <tr>
	<td valign="top">Images:</td>
	<td>
            <input size="40" type="button" id="add-images" name="add-images" value="Add Photo" class=""  />
            <div id="photo-container">
            </div>
            <div id="photos">
                <?php 
                    $photos = get_table_photos('gallery', 1, 'THUMB');
                    
                    $imageCount = 0;
                    
                    if($photos != NULL):
                        echo "<span style='display:block;padding:5px;'>Check to delete.</span>";
                        foreach($photos as $key => $photo):
                            $imageCount++;
                    ?>
                        <label>
                            <img src="<?php echo $photo['file']; ?>" style="width:100px;" />
                            <input type="checkbox" name="delete-image[]" value="<?php echo $photo['group']; ?>" />
                        </label>
                            
                            <br />
                        
                    <?php 
                        endforeach;
                    endif;
                ?>
            </div>
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

	

        var imageCount = <?php echo $imageCount; ?>;
        
        $('#add-images').click(function(){
            $('#photo-container').append('<input type="file" name="photo[' + imageCount + ']" /><br />');
            imageCount++;
        });

});
</script>
