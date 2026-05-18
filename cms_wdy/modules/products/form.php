<?php
    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = [];
    $errors = [];
    $table_id = get_id();
    if(isset($_POST['save']))
    {

        $fields = array('SKU','name','description','meta_description','type','price','price_per_kg','weights_grams','stock','stock_level','max_purchase','status');
        if (strlen($_POST['price']) == 0) {
            $_POST['price'] = NULL;
        }
        if (strlen($_POST['price_per_kg']) == 0) {
            $_POST['price_per_kg'] = NULL;
        }
        if (strlen($_POST['SKU']) > 0) {
            if($_POST['stock'] == 1 && strlen($_POST['stock_level']) == 0) {
                $_POST['stock_level'] = 1;
            }

            if($_POST['id'] == 0)
            {
                $check_sku = table_fetch_row('products','SKU="'.$_POST['SKU'].'"');

                if ($check_sku) {
                    $errors[] = 'SKU exists already on '.$check_sku['name'];
                } else {
                    $table_id = table_insert('products', $fields, $_POST);
                    $messages[] = 'Saved successfully.';
                }
                
            }
            else
            {
                $check_sku = table_fetch_row('products','SKU="'.$_POST['SKU'].'" AND SKU !="'.$_POST['SKU'].'"');
                if ($check_sku) {
                    $errors[] = 'SKU exists already on '.$check_sku['name'];
                } else {
                    table_update('products', $fields, $_POST, 'id=' . get_id());
                    $messages[] = 'Saved successfully.';
                }
            }


            if ($_POST['type'] == 'weight') {
                if (isset($_POST['new_weight'])) {
                    
                    foreach($_POST['new_weight'] as $key => $value) {
                        $pack_weight = $value;
                        $pack_price = $_POST['new_price'][$key];
                        $pack_status = 'Available';

                        table_insert('product_weights', ['product_id','weight','pack_price','status'],  ['product_id' => $table_id,'weight' => $pack_weight,'pack_price' => $pack_price,'status' => $pack_status]);

                    }
                    $messages[] = 'Packs added successfully.';
                }
            } else {
                table_delete_row('product_weights','product_id="'.$table_id.'"');
            }

            table_delete_row('products_categories','product_id="'.$table_id.'"');

            if (isset($_POST['categoryids'])) {
                foreach($_POST['categoryids'] as $catid) {
                    table_insert('products_categories', ['product_id','category_id'], ['product_id' => $table_id, 'category_id' => $catid]);
                }
            }

            if( isset($_POST['delete']) ){
                $mask = '../uploads/product/'.$table_id.'*.*';
                array_map('unlink', glob($mask));
            }

            if(isset($_POST['image-base64']) && strlen($_POST['image-base64']) > 0) {

                $image_parts = explode(";base64,", urldecode($_POST['image-base64']));
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $file = UPLOADS_DIR . 'product/' .$table_id . '.' . $image_type;
                file_put_contents($file, $image_base64);

                $image = new SimpleImageAdmin();
                $image->load($file);
                $image->save($file);
                
            }
            
            saveRewrite('product',$table_id,'',$_POST['url']);
        } else {
            $errors[] = 'SKU cannot be blank';
        }
        
    }

    $categories = table_fetch_rows('categories','status=1 and parent_id = -1','name ASC');

    $category_array = [];
    

    if(!empty($table_id)) {
        $data = table_fetch_row('products', 'id=' . $table_id);
        if($data !== false) {
            $data['url'] = getRewriteUrl('product', $data['id']);
            if($data['type'] == 'weight') {
                $pack_weights = table_fetch_rows('product_weights','product_id="'.$table_id.'" and status !="Sold"');
            }
        }
        $prod_categories = table_fetch_rows('products_categories','product_id="'.$table_id.'"','id ASC');

        if ($prod_categories) {
            foreach($prod_categories as $prodcat) { 
                $category_array[] = $prodcat['category_id'];
            }    
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
    <div class="col-12">
    <form class="validate-form" method="post" enctype="multipart/form-data">
    <input type="" name="id" hidden value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
    <input name="url" id="url" hidden size="50" type="" value="<?php echo isset($data['url']) ? $data['url'] : ''; ?>" />
    <table>
        <tr>
            <td colspan="2"><?php show_errors($errors); ?><?php show_messages($messages); ?></td>
        </tr>
        <tr>
            <td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Products</h1></td>
        </tr>
        <tr>
            <td>Title:</td>
            <td><input class="form-control" name="name" id="name" size="50" type="text" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" /></td>
        </tr>
        <tr>
            <td>SKU * (Must be Unique):</td>
            <td><input class="form-control" name="SKU" id="SKU" size="50" type="text" value="<?php echo isset($data['SKU']) ? $data['SKU'] : ''; ?>" required/></td>
        </tr>
        <tr>
            <td>Description:</td>
            <td><?php show_fckeditor('description', isset($data['description']) ? $data['description'] : '' ); ?></td>
        </tr>

        <tr>
            <td valign="top">Meta Description:</td>
            <td><textarea class="form-control" cols="50" rows="3" name="meta_description" id="meta_description"><?php echo stripslashes($data['meta_description']); ?></textarea></td>
        </tr>

        <tr>
                <td>Image:</td>
                <td>
                    <p>Minimum of 500px by 500px</p>
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
                                    $path = get_image('product/' . $data['id'] . '');
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
                </td>
        </tr>
       

        <tr>
        </tr>
        <!-- <tr>
            <td>Delivery & Returns (if different to default):</td>
            <td><?php show_fckeditor('delivery_returns_text', isset($data['delivery_returns_text']) ? $data['delivery_returns_text'] : '' ); ?></td>
        </tr> -->
        <?php if ($categories) : ?>
            <tr>
                <td valign="top">Categories:</td><td>
                <?php
                foreach ($categories as $category): ?>
                    <input type="checkbox" name="categoryids[]" value="<?= $category['id']; ?>" <?php if (in_array($category['id'], $category_array)) { echo 'checked="checked"';}; ?> />&nbsp;&nbsp;<label><?= $category['name'];?></label><br>
                    <?php 
                    $indent = 0;
                    $subcategories = table_fetch_rows('categories','status=1 and parent_id = '.$category['id'],'name ASC');
                    while (!empty($subcategories)) {
                        $indent+= 20;
                        foreach($subcategories as $subcategory) {
                            ?>
                            <input style="margin-left: <?= $indent;?>px;" type="checkbox" name="categoryids[]" value="<?= $subcategory['id']; ?>" <?php if (in_array($subcategory['id'], $category_array)) { echo 'checked="checked"';}; ?> />&nbsp;&nbsp;<label><?= $subcategory['name'];?></label><br>
                            <?php
                        }
                        $subcategories = table_fetch_rows('categories','status=1 and parent_id = '.$subcategory['id'],'name ASC');
                       
                    } ?>
                <?php endforeach;  ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Product Price Type:</td>
            <td>
                <p><small><strong>Per Item/Pack</strong> - Set a price that doesn't change for this product. eg) 1 pack of sausages = £5.50<br><strong>By Individual Pack Weight</strong> - Allows you to add individually pre-priced packs of varying weights/prices which go out of stock when that individual pack is purchased.<br><strong>By Total Weight</strong> - Uses the price per kg and allows customer to purchase by weight and have the price calculated</small></p>
            <select class="form-control" name="type" id="type">
                <option value="item" <?php echo (isset($data['type']) && $data['type'] == 'item') ? 'selected="selected"' : ''; ?> >Per Item/Pack</option>
                <option value="weight" <?php echo (isset($data['type']) && $data['type'] == 'weight') ? 'selected="selected"' : ''; ?> >By Individual Pack Weight</option>
                <option value="totalweight" <?php echo (isset($data['type']) && $data['type'] == 'totalweight') ? 'selected="selected"' : ''; ?> >By Total Weight</option>
            </select>
        </td>
        </tr>
        <?php if (!isset($data['type']) || $data['type'] == 'item') { ?>
        <tr>
            <td>Pack Price:</td>
            <td><input class="form-control" name="price" id="price" size="50" type="text" value="<?php echo isset($data['price']) ? $data['price'] : ''; ?>" /></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Price per kg:</td>
            <td><input class="form-control" name="price_per_kg" id="price_per_kg" size="50" type="text" value="<?php echo isset($data['price_per_kg']) ? $data['price_per_kg'] : ''; ?>" /></td>
        </tr>
        <?php if (!isset($data['type']) || $data['type'] == 'totalweight') { ?>
        <tr>
            <td>Weights available in grams:</td>
            <td><p><small>Enter weights in grams separated by a comma.  eg) 200,400,600,1000,1500,2000,3000,4000,5000</small></p><input class="form-control" name="weights_grams" id="weights_grams" size="150" type="text" value="<?php echo isset($data['weights_grams']) ? $data['weights_grams'] : ''; ?>" /></td>
        </tr>
        <?php } ?>
        <tr>
            <td>In Stock?:</td>
            <td>
                <select class="form-control" name="stock" id="stock">
                    <option value="1" <?php echo (isset($data['stock']) && $data['stock'] == 1) ? 'selected="selected"' : ''; ?> >Yes</option>
                    <option value="0" <?php echo (isset($data['stock']) && $data['stock'] == 0) ? 'selected="selected"' : ''; ?> >No</option>
                </select>
            </td>
        </tr>
        <?php if (!isset($data['type']) || $data['type'] == 'item') { ?>
            <tr id="stock-level">
                <td valign="top">Stock Level:</td>
                <td><input class="form-control" name="stock_level" id="stock_level" size="50" type="text" value="<?php echo isset($data['stock_level']) ? $data['stock_level'] : ''; ?>" /></td>
            </tr>
        <?php } elseif ($data['type'] == 'weight') { ?>
            <tr id="stock-weights">
                <td valign="top">Pack Weights:</td></td></tr>
                <tr><td colspan="2"><p style="font-size: 12px; color: #d22;">Note: A status of "Pending Purchase" means that this item has been added to an order but has not yet been paid for. This will also show the order ID so you know whether it is safe to delete or not.</p></td></tr><tr><td>
                <?php
                    echo '<td><table><tr><th>Weight (kg)</th><th>Pack Price</th><th>Status</th><th>Action</th>';

                    if ($pack_weights) {
                        foreach($pack_weights as $pack) {
                            if ($pack['status'] == 'Available') {
                                $statustext = '<span style="color: #2a2">Available</span>';
                            }elseif($pack['status'] == 'Pending Purchase') {
                                $order = table_fetch_row('order_items','product_id="'.$table_id.'" AND product_weight_id = "'.$pack['id'].'"','id DESC');
                                if (!$order) {
                                    $order['order_id'] = 'Customer didn\'t checkout yet since '.date('d/m/Y H:i',strtotime($pack['updated_at']));
                                }
                                $statustext = '<span style="color: #aa2">Pending Purchase</span><span style="color: #222; font-size: 12px;"><br>( Order #'.$order['order_id'].' )</span>';
                            };
                            ?>
                            <tr><td><?= $pack['weight']; ?></td><td>&pound;<?= $pack['pack_price']; ?></td><td  class="status-cell"><?= $statustext; ?></td><td style="text-align: center; "><?php if($pack['status'] == 'Pending Purchase') { ?> <a href="#" class="release-pack" style="color: #2a2;" data-id="<?= $pack['id']; ?>"><i class="fa fa-check-circle"></i> Make Pack Available</a> <?php } else { ?><a href="#" class="delete-pack" style="color: #d22;" data-id="<?= $pack['id']; ?>"><i class="fa fa-times-circle"></i></a><?php } ?></td></tr>
                            <?php
                    
                        } 
                    };

                    echo '<tr><td><a href="#" id="add-pack"><i class="fa fa-plus-circle"></i> Add Pack</a></td></tr>';


                    echo '</table>';
                    echo '<table id="new-pack-area"><tr class="message" style="display: none;"><td colspan="2"><p style="font-size: 12px;">Enter weight in kg and press tab to calculate price from the price per kg automatically.<br>To override this price just type over the price</p><td></table>';
                    echo '<td>'; ?>

               
            </tr>
        <?php } ?>    
       <tr>
            <td>Max Purchasable quantity per order:</td><td><p style="font-size: 12px;">Set to 0 if there is no limit</p></td>
        </tr>
        <tr>
            <td></td>
            <td><input class="form-control" name="max_purchase" id="max_purchase" size="50" type="text" value="<?php echo isset($data['max_purchase']) ? $data['max_purchase'] : '0'; ?>" /></td>
        </tr>
        
        <tr>
            <td>Status:</td>
            <td>
            <select class="form-control" name="status">
                <option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                <option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
            </select>
        </td>
        </tr>
        <tr>

        <td></td>
            <td><button href="#" class="save-form big btn-wdy btn-primary">Save</button></td>
        </tr>
    </table>
    <input type="hidden" name="save" />
    </form>

</div>
</div>
<script type="text/javascript">
$(function() {
    $('#name').keyup(function() {
        var val = $(this).val();
       
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9 ]+/g, '');
        val = val.replace('  ', ' ');
        var url = '/products/' + val.replace(/\s/g, '-');
        $('#url').val(url);
    });
    $('#add-pack').on('click', function(e) {
        e.preventDefault();
        $('#new-pack-area .message').show();
        $('#new-pack-area').append('<tr><td><input type="text" class="new-weight" placeholder="Weight (kg)" name="new_weight[]"</td><td><input class="new-price" type="text" placeholder="Price £ (eg 1.99)" name="new_price[]"</td></tr>');
    });
    <?php if ($table_id > 0) { 
        if (!isset($data['price_per_kg']) || strlen($data['price_per_kg']) == 0) {
            $data['price_per_kg'] = 1;
        } ?>
        $('body').on('blur', '.new-weight', function() {
        var weight = $(this).val();
        var price = parseFloat(weight * <?= $data['price_per_kg']; ?>);
        var new_price = price.toFixed(2);
        console.log(new_price);
        console.log($(this));
        var element = $(this).closest('td').next().find('.new-price');
        console.log(element);
        element.val(new_price);
        });
    <?php } ?>
    
    $('#type').on('change', function() {
        var type = $(this).val();

        if (type == 'item') {
            Swal.fire({
              title: 'Warning!',
              text: 'Changing the type to per item/pack or total weight and saving the product will delete any associated individual pack weights for this product.\n If you don\'t want to do this then change the type back before saving.',
              icon: 'warning',
              confirmButtonText: 'Ok Got It'
            })
        }
    }); 
    $('.delete-pack').on('click', function(e) {
        e.preventDefault();
        var pack_id = $(this).data('id');
        $(this).closest('tr').hide();
        $.ajax({
            type: "POST",
            url: './ajax/delete.php',
            data: { 
                    id : pack_id,
                    table : 'product_weights' },
            success: function(response)
            {
                Swal.fire({
                  title: 'Success!',
                  text: 'The individual pack has been deleted.',
                  icon: 'success'
                })
            }
        });

    });
    $('.release-pack').on('click', function(e) {
        e.preventDefault();
        var pack_id = $(this).data('id');
        var action_element = $(this).closest('td');
        var status_element = $(this).closest('tr').find('.status-cell');
        
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this! The pack will go back on sale and will be able to be added to a new order.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, make it available!'
        }).then((result) => {
          if (result.value) {
            action_element.html('<a href="#" class="delete-pack" style="color: #d22;" data-id="<?= $pack['id']; ?>"><i class="fa fa-times-circle"></i></a>');
            status_element.html('<span style="color: #2a2">Available</span>');
            $.ajax({
                type: "POST",
                url: './ajax/release-pack.php',
                data: { 
                        id : pack_id,
                        table : 'product_weights' },
                success: function(response)
                {
                    Swal.fire(
                      'Released!',
                      'The pack has been made available again.',
                      'success'
                    )
                }
            });
            
          }
        })
    });

    $('#name').keyup(function() {
        var val = $(this).val();
       
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9 ]+/g, '');
        val = val.replace('  ', ' ');
        var url = '/product/' + val.replace(/\s/g, '-');
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
