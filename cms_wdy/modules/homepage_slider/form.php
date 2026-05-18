<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('heading_line_1','heading_line_2','content','link_url','button_text','status');

        if($_POST['id'] == 0) 
        {
            $table_id = table_insert('homepage_slider', $fields, $_POST);
            $messages[] = 'Saved successfully.';
        }
        else 
        {
            table_update('homepage_slider', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }

        if( isset($_POST['delete']) ){
            foreach($_POST['delete'] as $image) {
                if( file_exists('../' . $image) ) {
                    unlink('../' . $image);
                }
            }
        }

        if(isset($_FILES['desktop-slide']) && !empty($_FILES['desktop-slide']['tmp_name'])) {

            $imgData = pathinfo($_FILES['desktop-slide']['name']);
            $image = new AdvancedSimpleImage();
        
            $image->fromFile($_FILES['desktop-slide']['tmp_name']);

            if ($image->getWidth() > 1920) {
                $image->resize(1920);
            }
            if ($image->getHeight() > 750) {
                $image->crop(0,0,1920,750);
            }
            
            $image->toFile(UPLOADS_DIR . 'homepage_slider/' .$table_id . '-desktop-slide.' . $imgData['extension']);

        }

        if(isset($_FILES['mobile-slide']) && !empty($_FILES['mobile-slide']['tmp_name'])) {

            $imgData = pathinfo($_FILES['mobile-slide']['name']);
            $image = new AdvancedSimpleImage();
        
            $image->fromFile($_FILES['mobile-slide']['tmp_name']);

            if ($image->getWidth() > 1260) {
                $image->resize(1260);
            }
            if ($image->getHeight() > 630) {
                $image->crop(0,0,1260,630);
            }
            
            $image->toFile(UPLOADS_DIR . 'homepage_slider/' .$table_id . '-mobile-slide.' . $imgData['extension']);

        }

    }
     
    if(!empty($table_id)) {
        $data = table_fetch_row('homepage_slider', 'id=' . $table_id); 
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
<form class="validate-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
    <div class="card mb-4">
        <div class="card-header">
            <h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Homepage Slider Slide</h1>
        </div>
        <div class="card-body">
           <div class="row">
            <div class="col-md-12">
                <label for="desktop-slide">Slide Image (DESKTOP): <span style="color: red;">(1920px by 750px)</span></label>
                <input class="form-control" name="desktop-slide" id="desktop-slide" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('homepage_slider/' . $data['id'] . '-desktop-slide');

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('homepage_slider/' . $data['id'] . '-desktop-slide'); ?>
                        <label><input class="form-control" type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>
   
            <?php endif; 
        endif; ?>
        <div class="row">
            <div class="col-md-12">
                <label for="mobile-slide">Slide Image (MOBILE) - will try to use desktop if not provided: <span style="color: red;">(1260px by 630px)</span></label>
                <input class="form-control" name="mobile-slide" id="mobile-slide" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('homepage_slider/' . $data['id'] . '-mobile-slide');

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('homepage_slider/' . $data['id'] . '-mobile-slide'); ?>
                        <label><input class="form-control" type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>
   
            <?php endif; 
        endif; ?>
            <div class="form-group">
                <label for="heading_line_1">Heading Line 1:</label>
                <input class="required form-control" name="heading_line_1" id="heading_line_1" size="50" type="text" value="<?php echo isset($data['heading_line_1']) ? $data['heading_line_1'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="heading_line_2">Heading Line 2:</label>
                <input class="required form-control" name="heading_line_2" id="heading_line_2" size="50" type="text" value="<?php echo isset($data['heading_line_2']) ? $data['heading_line_2'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="content_box">Content:</label>
                <input class="required form-control" name="content" id="content_box" size="50" type="text" value="<?php echo isset($data['content']) ? $data['content'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="link_url">Link (URL you want the button to go to):</label>
                <input class="required form-control" name="link_url" id="link_url" size="50" type="text" value="<?php echo isset($data['link_url']) ? $data['link_url'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="button_text">Button Text:</label>
                <input class="required form-control" name="button_text" id="button_text" size="50" type="text" value="<?php echo isset($data['button_text']) ? $data['button_text'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" class="form-control">
                    <option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                    <option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
                </select>
            </div>
            <div class="form-group">
            <?php show_big_button('save', 'Save'); ?>
            </div>
        </div>
    </div>
</form>