<?php
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('parent_id','menu_title', 'page_title','h1_title', 'content', 'content_2','meta_keywords', 'meta_description', 'target', 'status','top_nav','footer_nav');
        
        if($_POST['id'] == 0) 
        {
            $table_id = table_insert('page', $fields, $_POST);
            $messages[] = 'Saved successfully.';
            
        }
        else 
        {
            table_update('page', $fields, $_POST, 'id=' . get_id());
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
        if(isset($_FILES['desktop-image']) && !empty($_FILES['desktop-image']['tmp_name'])) {

            $imgData = pathinfo($_FILES['desktop-image']['name']);
            $image = new AdvancedSimpleImage();
        
            $image->fromFile($_FILES['desktop-image']['tmp_name']);

            if ($image->getWidth() > 1260) {
                $image->resize(1260);
            }
            if ($image->getHeight() > 400) {
                $image->crop(0,0,1260,400);
            }
            
            $image->toFile(UPLOADS_DIR . 'page/' .$table_id . '-large-banner.' . $imgData['extension']);

        }

        if(isset($_FILES['mobile-image']) && !empty($_FILES['mobile-image']['tmp_name'])) {

            $imgData = pathinfo($_FILES['mobile-image']['name']);
            $image = new AdvancedSimpleImage();
        
            $image->fromFile($_FILES['mobile-image']['tmp_name']);

            if ($image->getWidth() > 1260) {
                $image->resize(1260);
            }
            if ($image->getHeight() > 630) {
                $image->crop(0,0,1260,630);
            }
            
            $image->toFile(UPLOADS_DIR . 'page/' .$table_id . '-mobile-banner.' . $imgData['extension']);

        }

        if(isset($_FILES['meta-image']) && !empty($_FILES['meta-image']['tmp_name'])) {

            $imgData = pathinfo($_FILES['meta-image']['name']);
            $meta_image = new AdvancedSimpleImage();
            $meta_image->fromFile($_FILES['meta-image']['tmp_name']);

            if ($meta_image->getWidth() > 1200) {
                $meta_image->resize(1200);
                if($meta_image->getHeight() > 630) {
                    $meta_image->crop(0,0,1200,630);
                }
            }

            $meta_image->toFile(UPLOADS_DIR . 'page/' .$table_id . '-meta-image.' . $imgData['extension']);

        }

        if(isset($_FILES['about-image']) && !empty($_FILES['about-image']['tmp_name'])) {

            $imgData = pathinfo($_FILES['about-image']['name']);
            $about_image = new AdvancedSimpleImage();
            $about_image->fromFile($_FILES['about-image']['tmp_name']);

            if ($about_image->getWidth() > 900) {
                $about_image->resize(900);
            }

            $about_image->toFile(UPLOADS_DIR . 'page/' .$table_id . '-about-image.' . $imgData['extension']);

        }


        if(isset($_POST['delete-image'])){

            foreach(isset($_POST['delete-image']) as $photo) {
                table_delete_row('module_images', 'id='.$photo);
            }
        }

		if(isset($_FILES['photo'])) {
            bulk_upload_image($_FILES['photo'], 'page', $table_id, $_POST['links']);
        }
        
        saveRewrite(TBL_PAGE,$table_id,'',$_POST['url']);
    }
    
    if(!empty($table_id)) {
        $data = table_fetch_row('page', 'id=' . $table_id); 
        
        if($data !== false) {
            $data['url'] = getRewriteUrl(TBL_PAGE, $data['id']);
          
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
<input type="hidden" name="id" value="<?php echo $table_id; ?>" />
<input type="hidden" id="slug" value="" />
<div class="card mb-4">
    <div class="card-header">
        <h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Page</h1>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="parent_id">Parent:</label>
        
                <?php
                        $first_node = array('id' => -1, 'menu_title' => '--');
                        $list = table_fetch_rows(TBL_PAGE, '', 'parent_id ASC, position ASC');

                        $tree = get_parent_child_array($list);
                        show_tree($tree, 'parent_id', 'select', 'id', 'menu_title', $first_node, isset($data['parent_id']) ? $data['parent_id'] : -1);
                ?>
        </div>
        <div class="form-group">
            <label for="menu_title">Menu Title:</label>
            <input class="required form-control" name="menu_title" id="menu_title" size="100" type="text" value="<?php echo isset($data['menu_title']) ? $data['menu_title'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="url">URL:</label>
            <input class="required form-control" name="url" id="url" size="100" type="text" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="page_title">Page Title:</label>
            <input class="form-control" name="page_title" id="page_title" size="100" type="text" value="<?php echo isset($data['page_title']) ? $data['page_title'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="h1_title">H1 Title:</label>
            <input class="form-control" name="h1_title" id="h1_title" size="100" type="text" value="<?php echo isset($data['h1_title']) ? $data['h1_title'] : ''; ?>" />
        </div>
        <!-- <div class="form-group">
            <label for="add-banners">Banner Slider Images:</label>
            <input size="40" class="btn btn-primary ml-2" type="button" id="add-banners" name="add-banners" value="Add Photo +" class=""  />
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="photo-container">
                </div>
            </div>
        </div> -->
        <!-- <div class="row">
            <div class="col-md-12">
                <div id="photos">
                    <?php 
                        $photos = get_table_photos('page', $data['id']);
                        
                        $bannerCount = 0;
                        
                        if($photos != NULL):
                            echo "<span style='display:block;padding:5px;'>Check to delete.</span><span style='display:block;padding:5px;'>For external links add http:// at start. eg https://bbc.co.uk</span><span style='display:block;padding:5px;'>For internal links just type in info from URL field (See Above) on the edit page you wish to link to.</span><span style='display:block;padding:5px;'>eg /contact-us.html.</span>";
                            foreach($photos as $key => $photo):
                                $bannerCount++;
                        ?>
                            <label>
                                <?php if(!empty($photo['link'])): ?><a href="<?php echo $photo['link']; ?>"><?php endif; ?>
                                <img src="<?php echo $photo['file']; ?>" style="width:100px;" />
                                <?php if(!empty($photo['link'])): ?></a><?php endif; ?>
                                <input class="form-control type="checkbox" name="delete-image[]" value="<?php echo $key; ?>" />
                            </label>
                                <input class="form-control type="text" name="update_links[<?php echo $key ?>]" value="<?php echo $photo['link']; ?>" placeholder="Link" />
                                <br />
                            
                        <?php 
                            endforeach;
                        endif;
                    ?>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div class="col-md-12">
                <label for="desktop-image">Static Header Image (DESKTOP): <span style="color: red;">(1260px by 400px)</span></label>
                <input class="form-control" name="desktop-image" id="desktop-image" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('page/' . $data['id'] . '-large-banner');

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('page/' . $data['id'] . '-large-banner'); ?>
                        <label><input class="form-control" type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>
   
            <?php endif; 
        endif; ?>
        <div class="row">
            <div class="col-md-12">
                <label for="mobile-image">Static Header Image (MOBILE) - will try to use desktop if not provided: <span style="color: red;">(1260px by 630px)</span></label>
                <input class="form-control" name="mobile-image" id="mobile-image" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('page/' . $data['id'] . '-mobile-banner');

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('page/' . $data['id'] . '-mobile-banner'); ?>
                        <label><input class="form-control" type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>
   
            <?php endif; 
        endif; ?>
        <div class="row">
            <div class="col-md-12">
                <label for="about-image">Homepage About Us Image:</label>
                <input class="form-control" name="about-image" id="about-image" size="100" type="file" />
                <small class="form-text text-muted">Used by the About Us section on the homepage.</small>
            </div>
        </div>
        <?php
        if(isset($data['id'])) :

            $path = get_image('page/' . $data['id'] . '-about-image');

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('page/' . $data['id'] . '-about-image'); ?>
                        <label><input class="form-control" type="checkbox" name="delete[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>

            <?php endif; 
        endif; ?>
                
        <div class="form-group">
            <label for="links">Show Link:</label>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="top_nav" value="1" <?php echo (isset($data['top_nav']) && $data['top_nav'] == 1) ? 'checked="checked"' : ''; ?> />Main Navigation
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="footer_nav" value="1" <?php echo (isset($data['footer_nav']) && $data['footer_nav'] == 1) ? 'checked="checked"' : ''; ?> />Footer Navigation
                </label>
            </div>

        </div>
   
        <div class="form-group">
            <label for="content">Content:</label>
           
                    <?php show_fckeditor('content', isset($data['content']) ? $data['content'] : '' ); ?>
               
        </div>
         <div class="form-group">
            <label for="content">Content Area 2:</label>
           
                    <?php show_fckeditor('content_2', isset($data['content_2']) ? $data['content_2'] : '' ); ?>
               
        </div>
        <div class="row">
                <div class="col-md-12">
                    <label for="meta-image">Meta Image: Recommended size (1200px x 630px)</label>
                    <input class="form-control" name="meta-image" id="meta-image" size="100" type="file" />
                </div>
            </div>
            <?php
            if(isset($data['id'])) :
                    
                $path = get_image('page/' . $data['id'] . '-meta-image');

                if (strlen($path) > 0):
                    ?>
                     <div class="row">
                        <div class="col-md-12">
                            <?php show_image('page/' . $data['id'] . '-meta-image'); ?>
                            <label><input class="form-control" type="checkbox" name="delete-meta[]" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                        </div>
                    </div>
       
                <?php endif; 
            endif; ?>
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

</form>

<script type="text/javascript">
$(function() {
    function getSlug(table,parent_id) {
        $.ajax({
            type: "POST",
            url: './ajax/get-slug.php',
            data: { 
                    parent_id : parent_id,
                    table : table },
            success: function(response)
            {
                var jsonData = JSON.parse(response);
 
                if (jsonData.success == "1")
                {
                    $('#slug').val(jsonData.slug);
                    var val = $('#menu_title').val();
                    var slug = $('#slug').val();
                   
                    
                    val = val.toLowerCase();
                    val = val.replace(/[^a-z0-9 ]+/g, '');
                    val = val.replace('  ', ' ');

                    slug = slug.toLowerCase();
                    slug = slug.replace(/[^a-z0-9 \/]/, '');
                    slug = slug.replace('  ', ' ');

                    var url = slug + val.replace(/\s/g, '-');
                    if (url == '/home') {
                        url = '/';
                    }
                    $('#url').val(url);
                }
                else
                {
                    alert('Invalid Credentials!');
                }
           }
       });
    }

    var parent_id = $('#parent_id').val();
    getSlug('page',parent_id);

    $('#parent_id').on('change', function() {
        var parent_id = $(this).val();
        getSlug('page',parent_id);  
    })
	$('#menu_title').keyup(function() {
		var val = $(this).val();
        var slug = $('#slug').val();
		$('#page_title,#h1_title').val(val);
		
		val = val.toLowerCase();
		val = val.replace(/[^a-z0-9 ]+/g, '');
		val = val.replace('  ', ' ');

        slug = slug.toLowerCase();
        slug = slug.replace(/[^a-z0-9 \/]/, '');
        slug = slug.replace('  ', ' ');

		var url = slug + val.replace(/\s/g, '-');
        if (url == '/home') {
            url = '/';
        }
		$('#url').val(url);
	});
	 var bannerCount = <?php echo isset($bannerCount) ? $bannerCount : 0; ?>;
        
        $('#add-banners').click(function(){
            $('#photo-container').append('<input type="file" class="form-control" name="photo[' + bannerCount + ']" /><input type="text" class="form-control" name="links[' + bannerCount + ']" placeholder="Link" /><br />');
            bannerCount++;
        });
});
</script>
