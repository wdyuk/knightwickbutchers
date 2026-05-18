<?php
        
    require_once(__DIR__.DIRECTORY_SEPARATOR.'/bootstrap.php');

    $domain = parse_url($_SERVER['SERVER_NAME']);

    $url = isset($_GET['url']) ? $_GET['url'] : '/';
    $url = ($url == '/index') ? '/' : $url;

    $rewriteData = getRewriteByURL($url);

    if (isset($rewriteData['url']) && $rewriteData['url'] == '/') {
        $bodyclass = 'cms-home';
    } else {
        $bodyclass = '';
    }
    
    use Cart\Cart;
    use Cart\Storage\SessionStore;
    use Cart\CartItem;

    $cartid = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", SITE_NAME)).'-wdy-cart';

    $cartSessionStore = new SessionStore();

    $pageData = array();
    $pageData['meta_keywords'] = '';
    $pageData['meta_description'] = '';
    $pageData['title'] = '';
    $pageData['content'] = '';
    $pageData['blocks'] = '';
    $pageData['javascript'] = '';

    $cart = new Cart($cartid, $cartSessionStore);
    if(isset($_SESSION[$cartid])) {
        $cart->restore();
    }
    if (isset($_GET['remove-item'])) {
        $cart->remove($_GET['remove-item']);
        $cart->save();
        $cart->restore();
    }
    $cartTotal = $cart->total();
    $cartTotalItems = $cart->totalItems();

    $store_settings = table_fetch_row('store_settings','id=1');
    if (!$store_settings) {
        die('Store settings have not been declared.  Please set these up in the admin area to remove this message.');
    }
    $params = $_POST;

    if ($_SERVER['REMOTE_ADDR'] == '86.185.64.125') {
        echo '<pre>'.print_r( $params, true).'</pre>';
    }
    if(isset($params['product-id'])) {

        $product_id = sanitize_sql_string((int)$params['product-id']);

        $product = table_fetch_row('products','id="'. $product_id.'"');
        $qtyok = true;

        //product that has set weight packages
        if (isset($params['product-weight'])) {
            $product_weight = sanitize_sql_string((int)$params['product-weight']);
            $qty = 1;
            $product_weight_option = table_fetch_row('product_weights','id="'.$product_weight.'" AND product_id="'.$product_id.'" AND status="Available"');
            if ($product_weight_option) {
                $product['price'] = $product_weight_option['pack_price'];
                $product['name'] .= ' ( '.$product_weight_option['weight'].' kg )';
                $product['weight_text'] = ' ( '.$product_weight_option['weight'].' kg )';
                $product['weight-id'] = $product_weight_option['id'];
            } else {
                $qtyok = false;
            }
            // $checkItems = $cart->all();

            // foreach ($checkItems as $check) {
            //     if ($check->sku == $product['id'] && $check->weight_id == $product['weight-id']) {
            //         $qtyok = false;
            //         $_GET['stocklevel'] = 'none';
            //     }
            // }
           
        } else {

            $qty = (int)$params['qty'];
            //product that is bought by weight and quantity
            if ($product['type'] == 'totalweight') {
                if ($product['max_purchase'] > 0 && ($product['max_purchase'] < $qty)) {
                    $qty = $product['max_purchase'];
                    $_GET['stocklevel'] = 'notenough';
                    
                }
            } else {
                //product that is bought by simple pack price
                if ($product['stock_level'] < $qty) {
                    $qtyok = false;
                    if ($product['stock_level'] > 0) {
                        $qty = $product['stock_level'];
                        $_GET['stocklevel'] = 'notenough';

                    } else {
                        $_GET['stocklevel'] = 'none';
                        $qty = 0;
                    }
                    if ($product['max_purchase'] > 0 && ($product['max_purchase'] < $qty)) {
                        $qty = $product['max_purchase'];
                        $_GET['stocklevel'] = 'notenough';
                        
                    }
                }
            }
   
        }
        

        if ($qty > 0 && $qtyok == true) {
            $item = new CartItem;
            $item->name = $product['name'];
            if (!isset($product['price'])) {
                $item->price = $product['price_per_kg'];
            } else {
                $item->price = $product['price'];
            }
            $item->sku = $product['id'];
            $item->quantity = $qty;
            if(isset($product['weight-id'])) {
                $item->weight_id = $product['weight-id'];
                $item->weight_text = $product['weight_text'];
                $now = date('Y-m-d H:i:s');
                table_update('product_weights',['status','updated_at'], ['status' => "Pending Purchase", 'updated_at' => $now], 'id="'.$product['weight-id'].'"');
            }
            if (isset($product['type']) && $product['type'] == 'totalweight') {
                
                if (isset($params['weight']) && strlen($params['weight']) > 0) {
                    $item->weight = $params['weight'];
                    $item->price = ($item->weight / 1000) * $product['price_per_kg'];
                    if ($item->weight < 1000) {
                        $weightSuffix = 'g';
                        $weightDisplay = $item->weight; 
                    } else {
                        $weightSuffix = 'kg';
                        $weightDisplay = ($item->weight / 1000);
                    } 
                    $item->weightText = $weightDisplay.$weightSuffix;

                    $item->name .=  ' ( '.$item->weightText.' )';

                }
            }
            if(isset($params['update-product-quantity'])) {
                if ($cart->has($item->id)) {
                    $current_item = $cart->get($item->id);
                    $item->quantity = $qty - $current_item->quantity;
                    $test = $cart->add($item);
                    $notification = ''.$product['name'].' was updated';
                   

                }
                else {
                    
                    $notification = ''.$product['name'].' was not found in basket';
                }
                $cart->save();
                
            } else {
                $cart->add($item);
                $cart->save();
                $notification = ''.$product['name'].' was added to the basket';
            }
        } 
    }

    $cartItems = $cart->all();
    $cartWeights = [];
    foreach($cartItems as $cartItem) {
        if (isset($cartItem->weight_id)) {
            $cartWeights[] = $cartItem->weight_id;
        }
    }
    $cartTotal = $cart->total();
    $cartTotalItems = $cart->totalItems();
    $pre_auth_required = order_requires_preauth($cartItems);

    

    if($rewriteData !== false && $rewriteData['table_name'] == 'page')
    {
        $data = table_fetch_row('page', 'status = 1 AND id = ' . $rewriteData['table_id']);

        if($data !== false)
        {
            $pageData = $data;
            $pageData['title'] = $data['page_title'];
        }
        else {
            header('Location: /');
        }
    }


    else if($rewriteData !== false && $rewriteData['table_name'] == 'category')
    {
        $data = table_fetch_row('categories', 'id = ' . $rewriteData['table_id']);

        if($data !== false)
        {
            $pageData = $data;
            $pageData['title'] = $data['name'];
            $pageData['h1_title'] = $data['name'];
            $subcategories = table_fetch_rows('categories','parent_id="'.$data['id'].'"');

          } else {
          header('Location: /');
          }
    }
     else if($rewriteData !== false && $rewriteData['table_name'] == 'product')
    {
        $data = table_fetch_row('products', 'id = ' . $rewriteData['table_id'] .' AND status = 1');

        if($data !== false)
        {
            $pageData = $data;
            $pageData['title'] = $data['name'];
            $pageData['h1_title'] = $data['name'];

          } else {
          header('Location: /');
          }
      }



        else if($rewriteData !== false && $rewriteData['table_name'] == 'news')
        {
            $data = table_fetch_row('news', 'id = ' . $rewriteData['table_id']);

            if($data !== false)
            {
                $pageData = $data;
                $pageData['title'] = $data['title'];
                $pageData['h1_title'] = $data['title'];
                $pageData['date'] = date('j M, Y', strtotime($data['news_date']));

            }
            else {
                header('Location: /');
            }


    }else if($rewriteData !== false && $rewriteData['table_name'] == 'event_diary')
    {
        $data = table_fetch_row('event_diary', 'id = ' . $rewriteData['table_id']);

        if($data !== false)
        {
            $pageData = $data;
            $pageData['h1_title'] = $data['title'];
            $pageData['price'] = $data['price'];
            $pageData['description'] = $data['description'];
            $pageData['location_name'] = $data['location_name'];
            $pageData['address'] = $data['address'];
            $pageData['city'] = $data['city'];
            $pageData['postcode'] = $data['postcode'];
            $pageData['status'] = $data['status'];
            // $pageData['start_date'] = date('j M, Y', strtotime($data['start_date']));
            // $pageData['end_date'] = date('j M, Y', strtotime($data['end_date']));
            // $pageData['start_time'] = time('H M, S', ($data['start_time']));
            // $pageData['end_time'] = time('H M, S', ($data['end_time']));

        }
        else {
            header('Location: /');
        }
    }
    else {
    http_response_code(404);
    $pageData = table_fetch_row('page','page_title="Page Not Found"');
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <?php include("includes/template/head.php"); ?>
        <body>
            <main>
                
                <?php 
                include("includes/template/header.php"); 
                ?>
                    
                <section id="maincontent" class="light-light-grey-bg">
                    <?php
                    include('layout/inner.php');
                    ?>
                </section>
                <?php 
                include('template/footer.php');
                ?>
            </main>
        </body>
    </html>
    <?php
    die();
    
}

?>
