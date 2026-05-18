<?php
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
   

    if(isset($_POST['save']))
    {
        $fields = array('title','page_title','teaser','content','youtube_url','author','publish_date','meta_description','status');

        $_POST['publish_date'] = str_replace('/', '-', $_POST['publish_date']);
    	$_POST['publish_date'] = date('Y-m-d H:i:s', strtotime($_POST['publish_date']));
        $_POST['author'] = $_SESSION['admin']['name'];
        

        if($_POST['id'] == 0) 
        {
            $table_id = table_insert('videos', $fields, $_POST);
            $messages[] = 'Saved successfully.';
           
        }
        else 
        {
            table_update('videos', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }   

        if( isset($_POST['delete']) ){
            foreach($_POST['delete'] as $image) {
                if( file_exists('../' . $image) ) {
                    unlink('../' . $image);
                }
            }
        }
        if( isset($_POST['delete-meta']) ){
            foreach($_POST['delete-meta'] as $image) {
                if( file_exists('../' . $image) ) {
                    unlink('../' . $image);
                }
            }
        }
    	if(isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {

    		$imgData = pathinfo($_FILES['image']['name']);
    		$image = new AdvancedSimpleImage();
        
    		$image->fromFile($_FILES['image']['tmp_name']);

    		$image->resize(1000,1000);
    		$image->toFile(UPLOADS_DIR . 'videos/' .$table_id . '-video-image.' . $imgData['extension']);

    		// if ($image->getWidth() > 300) {
      //           $image->resize(300);
      //       }
    		// $image->toFile(UPLOADS_DIR . 'videos/' .$table_id . '-small.' . $imgData['extension']);
    	}

        // if(isset($_FILES['meta-image']) && !empty($_FILES['meta-image']['tmp_name'])) {

        //     $imgData = pathinfo($_FILES['meta-image']['name']);
        //     $meta_image = new AdvancedSimpleImage();
        //     $meta_image->fromFile($_FILES['meta-image']['tmp_name']);

        //     if ($meta_image->getWidth() > 1200) {
        //         $meta_image->resize(1200);
        //         if($meta_image->getHeight() > 630) {
        //             $meta_image->crop(0,0,1200,630);
        //         }
        //     }
        //     $meta_image->toFile(UPLOADS_DIR . 'videos/' .$table_id . '-meta-image.' . $imgData['extension']);
        // }

        saveRewrite('videos',$table_id,'',$_POST['url']);
  
    }
     
    if($table_id > 0) {
        $data = table_fetch_row('videos', 'id=' . $table_id); 
        if($data !== false) {
        	$data['url'] = getRewriteUrl('videos', $data['id']);
    		$publish_date = date('d/m/Y H:i', strtotime($data['publish_date']));
        }
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
    <input name="url" id="url" size="50" type="hidden" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
    <div class="card mb-4">
        <div class="card-header">
            <h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Video Post</h1>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="image">Image: <span style="color: red;">(800px by 800px)</span></label>
                <input size="40" type="file" id="image" name="image" value="" class="form-control"  />
            </div>
            <div class="form-group">
                <?php    
                if($table_id > 0) {  
            	    $path = get_image('videos/' . $data['id'] . '-video-image');

            	    if (strlen($path) > 0):
                        ?>
               
            	        <?php show_image('videos/' . $data['id'] . '-video-image'); ?>
            	        <label><input type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
            	    
                    <?php endif; 
                }?>
            </div>
            <div class="form-group">
                <label for="title">Blog Title:</label>
                <input class="required form-control" name="title" id="title" size="50" type="text" value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="page_title">Page Title:</label>
                <input class="required form-control" name="page_title" id="page_title" size="50" type="text" value="<?php echo isset($data['page_title']) ? $data['page_title'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="date">Publish Date/Time:</label>
                <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                    <input type="text" name="publish_date" class="form-control mb-2 datetimepicker-input" data-target="#datetimepicker1" value="<?php echo isset($publish_date) ? $publish_date : ''; ?>"/>
                    <div class="input-group-append mb-2" data-target="#datetimepicker1" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input class="required form-control" name="author" id="author" size="50" type="text" value="<?php echo isset($data['author']) ? $data['author'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="content">Teaser Line:</label>
               
                <?php show_fckeditor('teaser', isset($data['teaser']) ? $data['teaser'] : '' ); ?>
                   
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
               
                <?php show_fckeditor('content', isset($data['content']) ? $data['content'] : '' ); ?>
                   
            </div>
            <div class="form-group">
                <label for="youtube_url">Youtube Link: <span style="color:red;">(Copy the embed code and paste below)</span></label>
                <textarea name="youtube_url" id="youtube_url" rows="4" cols="50" class="form-control"><?php echo isset($data['youtube_url']) ? $data['youtube_url'] : ''; ?></textarea>
            </div>
<!--             <div class="row">
                <div class="col-md-12">
                    <label for="meta-image">Meta Image: Recommended size (1200px x 630px)</label>
                    <input class="form-control" name="meta-image" id="meta-image" size="100" type="file" />
                </div>
            </div>
            <?php
            /*if(!empty($table_id)) { 
                    
                $path = get_image('blog/' . $data['id'] . '-meta-image');

                if (strlen($path) > 0):
                    ?>
                     <div class="row">
                        <div class="col-md-12">
                            <?php show_image('blog/' . $data['id'] . '-meta-image'); ?>
                            <label><input class="form-control" type="checkbox" name="delete-meta[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                        </div>
                    </div>
       
                <?php endif; 
            } */?> -->
            <div class="form-group">
                <label for="meta_description">Meta description:</label>
                <textarea cols="50" rows="3" class="form-control" name="meta_description" id="meta_description"><?php echo isset($data['meta_description']) ? $data['meta_description'] : ''; ?></textarea>
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
<script type="text/javascript">
$(function() {
    $(function () {
        $('#datetimepicker1').datetimepicker({
            format: 'DD/MM/YYYY HH:mm'
        });
    });

    $('#title').keyup(function() {
            var val = $('#title').val();

            val = val.toLowerCase();
            val = val.replace(/[^a-z0-9 ]+/g, '');
            val = val.replace('  ', ' ');

            var url = '/articles/videos/' + val.replace(/\s/g, '-');

            $('#url').val(url);  
    });
});
</script>
