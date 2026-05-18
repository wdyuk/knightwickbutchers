<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();

    if(isset($_POST['save']))
    {

        $target_dir = BASE_DIR.'uploads/category/';
        $target_file = $target_dir . time() . '.csv';
        $uploadOk = 1;

        move_uploaded_file($_FILES["import-categories"]["tmp_name"], $target_file);

        $categories = csv_to_array($target_file);
        // echo '<pre>'.print_r($categories, true).'</pre>';
        if (empty($categories)) {
            $uploadOk = 0;
              echo "<div class='alert alert-danger'>The file does not include any category information or is corrupt.</div>";
        }
        elseif (!array_key_exists('description',$categories[0])  || !array_key_exists('meta_description',$categories[0])  || !array_key_exists('image_filename',$categories[0])) {
            $uploadOk = 0;
              echo "<div class='alert alert-danger'>The file does not have the expected format or is corrupt.</div>";
        }

        if ($uploadOk == 1) {

            $errors = array();
            $fields = array('id','parent_id','name','description','meta_description','status','position');
            foreach($categories as $category) {
                if (!isset($category['id']) || !isset($category['name'])) {
                     $errors[] = "Couldn't import Category ".$category['name']. "(ID : ".$category['id'].") as ID or name not set"; 
                } else {
                    $checkcurrent = table_fetch_row('categories','id='.$category['id'].' OR name LIKE "'.$category['name'].'"');
                    if ($checkcurrent) {
                        $errors[] = "Couldn't import ".$category['name']. "(ID : ".$category['id'].") as ID or name already exists"; 
                    } else {
                        if (empty($category['parent_id']) || !isset($category['parent_id'])) {
                            $category['parent_id'] = NULL;
                        }
                        if (empty($category['status']) || !isset($category['status'])) {
                            $category['status'] = 1;
                        }
                        if (empty($category['position']) || !isset($category['position'])) {
                            $category['position'] = 1;
                        }
                        table_insert('categories',$fields,$category);
                        saveRewrite('category',$category['id'],'','/category/'.strtolower(str_replace(array(' ','_'),'-',$category['name'])));
                        $messages[] = $category['name']. "(ID : ".$category['id'].") imported!"; 
                        
  
                        if (strlen($category['image_filename']) > 0) {
                            if (file_exists($target_dir.'import/'.$category['image_filename'])) {
                                $image = new AdvancedSimpleImage();

                                $image->fromFile($target_dir.'import/'.$category['image_filename']);

                                $image->bestFit(500,500);

                                $imageFileType = strtolower(pathinfo($target_dir.'import/'.$category['image_filename'],PATHINFO_EXTENSION));

                                $image->toFile($target_dir.$category['id'].'.'.$imageFileType);
                                $messages[] = 'Image for '.$category['name']. "(ID : ".$category['id'].") imported!"; 
                            }
                        }
                        
                    }
                }
                
            }
        }

        unlink($target_file); 

        // $fields = array('parent_id','name','description','meta_description','homepage','status');
        
        // if($_POST['id'] == 0)
        // {
        //     $table_id = table_insert('categories', $fields, $_POST);
        //     $messages[] = 'Saved successfully.';
        // }
        // else
        // {
        //     table_update('categories', $fields, $_POST, 'id=' . get_id());
        //     $messages[] = 'Saved successfully.';
        // }

        // if( isset($_POST['delete']) ){
        //     $mask = '../uploads/category/'.$table_id.'*.*';
        //     array_map('unlink', glob($mask));
        // }

        // if(isset($_POST['image-base64']) && strlen($_POST['image-base64']) > 0) {

        //     $image = new SimpleImageAdmin();
        //     $image->load($file);
        //     $image->save($file);
            
        // }
       
        // saveRewrite('category',$table_id,'',$_POST['url']);
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
    <div class="card mb-4">
        <div class="card-header">
           <h1>Import Categories</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="import-categories">Category CSV:</label>
                    <input class="form-control" name="import-categories" id="import-categories" size="100" type="file" />
                </div>
            </div>

        
            <div class="form-group">
               
                <button href="#" class="save-form big btn-wdy btn-primary">Import</button>
                 <input type="hidden" name="save" />
            </div>
        </div>
    </div>
</form>