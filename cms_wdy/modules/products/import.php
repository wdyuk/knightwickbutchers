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

        $target_dir = BASE_DIR.'uploads/product/';
        $target_file = $target_dir . time() . '.csv';
        $uploadOk = 1;

        move_uploaded_file($_FILES["import-products"]["tmp_name"], $target_file);

        $products = csv_to_array($target_file);
        // echo '<pre>'.print_r($products, true).'</pre>';
        if (empty($products)) {
            $uploadOk = 0;
              echo "<div class='alert alert-danger'>The file does not include any product information or is corrupt.</div>";
        }
        elseif (!array_key_exists('description',$products[0])  || !array_key_exists('weights_grams',$products[0])  || !array_key_exists('image_filename',$products[0])) {
            $uploadOk = 0;
              echo "<div class='alert alert-danger'>The file does not have the expected format or is corrupt.</div>";
        }

        if ($uploadOk == 1) {

            $errors = array();
            $product_fields = array('SKU','type','name','price','price_per_kg','weights_grams','description','stock','stock_level','max_purchase','meta_description','status','position');

            foreach($products as $product) {
                $error = 0;
                if (!isset($product['SKU']) || !isset($product['name'])) {
                     $errors[] = "Couldn't import product ".$product['name']. "(SKU : ".$product['SKU'].") as SKU or name not set"; 
                     $error = 1;
                }
                $allowed_types = array('item','totalweight','weight');
                if(!isset($product['type']) || !(in_array($product['type'], $allowed_types))) {
                     $errors[] = "Couldn't import ".$product['name']. "(SKU : ".$product['SKU'].") as product type not set to one of 'item', 'totalweight' or 'weight'"; 
                    $error = 1;
                }
                if(isset($product['type']) && $product['type'] != 'item' && empty($product['price_per_kg'])) {
                     $errors[] = "Couldn't import ".$product['name']. "(SKU : ".$product['SKU'].") as price per kg not set and is required on this product type"; 
                    $error = 1;
                }
                $checkcurrent = table_fetch_row('products','SKU="'.$product['SKU'].'" OR name LIKE "'.$product['name'].'"');
                if ($checkcurrent) {
                    $errors[] = "Couldn't import ".$product['name']. "(SKU : ".$product['SKU'].") as SKU or name already exists"; 
                    $error = 1;
                } 

                if ($error == 0) {
                    if (empty($product['stock']) || !isset($product['stock'])) {
                        $product['stock'] = 1;
                    }
                    if (empty($product['stock_level']) || !isset($product['stock_level'])) {
                        $product['stock_level'] = 1;
                    }
                    if (empty($product['max_purchase']) || !isset($product['max_purchase'])) {
                        $product['max_purchase'] = 0;
                    }
                    if (empty($product['status']) || !isset($product['status'])) {
                        $product['status'] = 1;
                    }

                    if (empty($product['position']) || !isset($product['position'])) {
                        $product['position'] = 1;
                    }

                    if (empty($product['price']) || !isset($product['price'])) {
                        $product['price'] = $product['price_per_kg'];
                    }

                    echo '<pre>'.print_r($product, true).'</pre>';


                    if (!empty($product['weights_grams'])) {
                        $weights_grams = trim(str_replace(";",",",$product['weights_grams']));
                        $product['weights_grams'] = trim($weights_grams,',');

                    }

                    $product_id = table_insert('products',$product_fields,$product);


                    if (!empty($product['weights_grams'])) {
                
                        if ($product['type'] == 'weight') {
                            $weights = explode(',',$product['weights_grams']);
                            echo '<pre>'.print_r($weights, true).'</pre>';
                            if (!empty($weights)) {
                                foreach($weights as $weight) {
                                    $gram_weight = ($weight / 1000);
                                    $pack_price = number_format($gram_weight * $product['price_per_kg'],2, '.', ''); 
                                    table_insert('product_weights',array('product_id','weight','pack_price','status','updated_at'),array('product_id' => $product_id,'weight' => $gram_weight,'pack_price' => $pack_price,'status' => 'Available','updated_at' => date('Y-m-d H:i:s')));
                                }

                            }
                        }
                    }
                    if (!empty($product['categories'])) {
                        $categories = trim(str_replace(";",",",$product['categories']));
                        $categories = trim($categories,',');
                        $categories = explode(',',$categories);
                        if (!empty($categories)) {
                            foreach($categories as $category) {
                                table_insert('products_categories',array('product_id','category_id'),array('product_id' => $product_id,'category_id' => $category));
                            }
                        }
                    }
                    $rewriteurl = '/products/'.strtolower(str_replace(array(' ','_'),'-',$product['name']));
                    saveRewrite('product',$product_id,'',$rewriteurl);
                    $messages[] = $product['name']. "(SKU : ".$product['SKU'].") imported!"; 
                    

                    if (strlen($product['image_filename']) > 0) {
                        if (file_exists($target_dir.'import/'.$product['image_filename'])) {
                            $image = new AdvancedSimpleImage();

                            $image->fromFile($target_dir.'import/'.$product['image_filename']);


                            $imageFileType = strtolower(pathinfo($target_dir.'import/'.$product['image_filename'],PATHINFO_EXTENSION));

                            $image->bestFit(800,800);

                            $image->toFile($target_dir.$product_id.'.'.$imageFileType);

                            $image->bestFit(500,500);

                            $image->toFile($target_dir.$product_id.'-small.'.$imageFileType);

                            $messages[] = 'Image for '.$product['name']. "(SKU : ".$product_id.") imported!"; 
                        }
                    }
                    
                }
            
                
            }
        }

        unlink($target_file); 

        // $fields = array('parent_id','name','description','meta_description','homepage','status');
        
        // if($_POST['id'] == 0)
        // {
        //     $table_id = table_insert('products', $fields, $_POST);
        //     $messages[] = 'Saved successfully.';
        // }
        // else
        // {
        //     table_update('products', $fields, $_POST, 'id=' . get_id());
        //     $messages[] = 'Saved successfully.';
        // }

        // if( isset($_POST['delete']) ){
        //     $mask = '../uploads/product/'.$table_id.'*.*';
        //     array_map('unlink', glob($mask));
        // }

        // if(isset($_POST['image-base64']) && strlen($_POST['image-base64']) > 0) {

        //     $image = new SimpleImageAdmin();
        //     $image->load($file);
        //     $image->save($file);
            
        // }
       
        // saveRewrite('product',$table_id,'',$_POST['url']);
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
           <h1>Import Products</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="import-products">Product CSV:</label>
                    <input class="form-control" name="import-products" id="import-products" size="100" type="file" />
                </div>
            </div>

        
            <div class="form-group">
               
                <button href="#" class="save-form big btn-wdy btn-primary">Import</button>
                 <input type="hidden" name="save" />
            </div>
        </div>
    </div>
</form>