<?php
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    $table_id = get_id();
    if(isset($_POST['save']))
    {

        $fields = array('parent_id','name','description','meta_description','homepage','status');
        
        if($_POST['id'] == 0)
        {
            $table_id = table_insert('categories', $fields, $_POST);
            $messages[] = 'Saved successfully.';
        }
        else
        {
            table_update('categories', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }

        if( isset($_POST['delete']) ){
            $mask = '../uploads/category/'.$table_id.'*.*';
            array_map('unlink', glob($mask));
        }

        if(isset($_POST['image-base64']) && strlen($_POST['image-base64']) > 0) {

            $image_parts = explode(";base64,", urldecode($_POST['image-base64']));
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $file = UPLOADS_DIR . 'category/' .$table_id . '.' . $image_type;
            file_put_contents($file, $image_base64);

            $image = new SimpleImageAdmin();
            $image->load($file);
            $image->save($file);
            
        }
        if( isset($_POST['delete_desktop']) ){
            $mask = '../uploads/category/'.strtolower(str_replace(' ','-', $_POST['name'])).'-banner-'.$table_id.'*.*';
            array_map('unlink', glob($mask));
        }
        if(isset($_FILES['desktop-image']) && !empty($_FILES['desktop-image']['tmp_name'])) {

            $imgData = pathinfo($_FILES['desktop-image']['name']);
            $image = new AdvancedSimpleImage();
        
            $image->fromFile($_FILES['desktop-image']['tmp_name']);

            if ($image->getWidth() > 1920) {
                $image->resize(1920);
            }
            if ($image->getHeight() > 400) {
                $image->crop(0,0,1920,400);
            }
            
            $image->toFile(UPLOADS_DIR . 'category/'.strtolower(str_replace(' ','-', $_POST['name'])).'-banner-'.$table_id.'.' . $imgData['extension']);

        }
        if( isset($_POST['delete_mobile']) ){
            $mask = '../uploads/category/'.strtolower(str_replace(' ','-', $_POST['name'])).'-banner-mobile'.$table_id.'*.*';
            array_map('unlink', glob($mask));
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
            
            $image->toFile(UPLOADS_DIR . 'category/'.strtolower(str_replace(' ','-', $_POST['name'])).'-banner-mobile-'.$table_id.'.' . $imgData['extension']);

        }

        saveRewrite('category',$table_id,'',$_POST['url']);
    }

    $categories = table_fetch_rows('categories','status=1','name ASC');

    if(!empty($table_id)) {
        $data = table_fetch_row('categories', 'id=' . $table_id);
        if($data !== false) {
                $data['url'] = getRewriteUrl('category', $data['id']);
        }
    }
?>
<style type="text/css">
#image-to-crop {
  display: block;
  max-width: 100%;
}
.preview {
  overflow: hidden;
  width: 160px !important; 
  height: 160px !important;
  max-width: 160px !important; 
  max-height: 160px !important;
  margin: 10px;
  border: 1px solid red;
}

</style>
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
<input type="" name="id" hidden value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
<input name="url" id="url" hidden size="50" type="" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
<div class="card mb-4">
    <div class="card-header">
       <h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Categories</h1>
    </div>
    <div class="card-body">
        <?php if ($categories) : ?>
       
            <div class="form-group">
                <label for="parent_id">Parent Category:</label>
                <select name="parent_id" class="form-control">
                    <option value="-1" <?php echo (isset($data['parent_id']) && ($data['parent_id'] == '-1')) ? 'selected="selected"' : '';?>>None</option>
                <?php
                foreach ($categories as $category): ?>
                    <option value="<?= $category['id']; ?>" <?php echo (isset($data['parent_id']) && ($data['parent_id'] == $category['id'])) ? 'selected="selected"' : '';?>><?= $category['name']; ?></option>

                <?php endforeach;  ?>
                </select>
            </div>
        
                
        <?php endif; ?>
        <div class="form-group">
            <label for="name">Title:</label>
            <input class="required form-control" name="name" id="name" size="100" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <?php show_fckeditor('description', isset($data['description']) ? $data['description'] : '' ); ?>
        </div>
       
        <div class="form-group">
            <label for="image">Image: Minimum of 500px by 500px</label>
              
            <div class="row mb-2">
                <div class="col-12">
                    <input size="40" type="file" name="image" value="" class="image-upload" />
                    <input type="hidden" name="image-base64" id="imagebase64" value="" />
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <img src="" id="image-to-crop" />
                </div>
                <div class="col-12 col-md-4 offset-md-2">
                    <p>Preview</p><div class="preview"></div>
                </div>
                <div class="col-12 col-md-6">
                    <?php
                        if(isset($data['id'])) :
                            $path = get_image('category/' . $data['id'] . '');
                            if (strlen($path) > 0):
                                ?>
                                <p>Uploaded File</p>
                                <img src="<?php echo $path;?>?v=<?= rand(0,100000); ?>" width="200px" />
                                <br>
                               <input type="checkbox" name="delete[]" value="<?php echo $path; ?>" /><br><label>&nbsp;Delete</label>
                            <?php endif; 
                        endif; ?>

                </div>
            </div>
        <div>
       
        <div class="row">
            <div class="col-md-12">
                <label for="desktop-image">Large Desktop Banner Image (Top of category page): <span style="color: red;">(1920px by 400px)</span></label>
                <input class="form-control" name="desktop-image" id="desktop-image" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('category/' .strtolower(str_replace(' ','-', $data['name'])).'-banner-'.$table_id);

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('category/' .strtolower(str_replace(' ','-', $data['name'])).'-banner-'.$table_id); ?>
                        <label><input class="form-control" type="checkbox" name="delete_desktop" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>

            <?php endif; 
        endif; ?>
        <div class="row">
            <div class="col-md-12">
                <label for="mobile-image">Large Mobile Banner Image (Top of category page): <span style="color: red;">(1260px by 630px)</span></label>
                <input class="form-control" name="mobile-image" id="mobile-image" size="100" type="file" />
            </div>
        </div>
        <?php
        if(isset($data['id'])) :
                
            $path = get_image('category/' .strtolower(str_replace(' ','-', $data['name'])).'-banner-mobile-'.$table_id);

            if (strlen($path) > 0):
                ?>
                 <div class="row">
                    <div class="col-md-12">
                        <?php show_image('category/' .strtolower(str_replace(' ','-', $data['name'])).'-banner-mobile-'.$table_id); ?>
                        <label><input class="form-control" type="checkbox" name="delete_mobile" value="<?php echo $path; ?>" />&nbsp;Delete</label>
                    </div>
                </div>

            <?php endif; 
        endif; ?>
        <div class="form-group">
            <label for="meta_description">Meta Description:</label>
            <textarea cols="50" rows="3" class="form-control" name="meta_description" id="meta_description"><?php echo stripslashes($data['meta_description']); ?></textarea>
            
        </div>
        <div class="form-group">
            <label for="homepage">Featured on Homepage?:</label>
           
            <select name="homepage" class="form-control" id="homepage">
                <option value="1" <?php echo (isset($data['homepage']) && $data['homepage'] == 1) ? 'selected="selected"' : ''; ?> >Yes</option>
                <option value="0" <?php echo (isset($data['homepage']) && $data['homepage'] == 0) ? 'selected="selected"' : ''; ?> >No</option>
            </select>
        </div>
        <div class="form-group">
           <label for="status">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                <option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
            </select>
        </div>
    
        <div class="form-group">
           
            <button href="#" class="save-form big btn-wdy btn-primary">Save</button>
             <input type="hidden" name="save" />
        </div>
    </div>
</form>
<script type="text/javascript">
$(function() {
    $('#name').keyup(function() {
        var val = $(this).val();
       
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9 ]+/g, '');
        val = val.replace('  ', ' ');
        var url = '/category/' + val.replace(/\s/g, '-');
        $('#url').val(url);
    });
    var image = document.getElementById('image-to-crop');
    var cropper;
      
    $("body").on("change", ".image-upload", function(e){
        var files = e.target.files;
        var done = function (url) {
          image.src = url;
          
            cropper = new Cropper(image, {
                aspectRatio: 1,
                toggleDragModeOnDblclick: false,
                viewMode: 1,
                dragMode: 'move',
                preview: '.preview',
                cropBoxResizable: false
            });
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
          file = files[0];

          if (URL) {
            done(URL.createObjectURL(file));
          } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
              done(reader.result);
            };
            reader.readAsDataURL(file);
          }
        }
    });

    // $modal.on('shown.bs.modal', function () {
    //     cropper = new Cropper(image, {
    //     aspectRatio: 1,
    //     viewMode: 3,
    //     preview: '.preview'
    //     });
    // }).on('hidden.bs.modal', function () {
    //    cropper.destroy();
    //    cropper = null;
    // });

    $("button.save-form").click(function(e){
        e.preventDefault();
        e.stopPropagation();

        if (typeof cropper !== "undefined") {

            canvas = cropper.getCroppedCanvas({
              width: 500,
              height: 500,
            });

            canvas.toBlob(function(blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                 reader.readAsDataURL(blob); 
                 reader.onloadend = function() {
                    var base64data = reader.result;  
                    
                    $('#imagebase64').val(encodeURIComponent(base64data));
                    $('form.validate-form').submit();
                 }
            });
        } else {
            $('form.validate-form').submit();
        }
    });
});
    // var imageCount = <?php echo $imageCount; ?>;
    //     $('#add-images').click(function(){
    //         $('#photo-container').append('<input type="file" name="photo[' + imageCount + ']" />');
    //         imageCount++;
    //     });
</script>
