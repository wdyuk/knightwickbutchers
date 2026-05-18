<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<?php $category_banner_image = get_image('category/'.strtolower(str_replace(' ','-', $pageData['name'])).'-banner-'.$pageData['id']); 

  if (!$category_banner_image) {
    $category_banner_image = '/assets/theme/img/banners/category_background.jpg';
  } 

  $category_banner_mobile_image = get_image('category/'.strtolower(str_replace(' ','-', $pageData['name'])).'-banner-mobile-'.$pageData['id']); 

  if (!$category_banner_mobile_image) {
    $category_banner_mobile_image = '/assets/theme/img/banners/category_background_mobile.jpg';
  } 
?>
<section class="probootstrap-section-bg hidden-xs hidden-sm overlay category-banner" style="background-image: url(<?= $category_banner_image; ?>);" data-stellar-background-ratio="0.5">
  <div class="container relative">
    <div class="dark-overlay"></div>
    <div class="row">
      <div class="col-md-12 text-center probootstrap-animate">
        <div class="probootstrap-heading mt-2">
          <h2 class="primary-heading"><?= $pageData['h1_title']; ?></h2>
          <!-- <h3 class="secondary-heading">Our Products</h3> -->
        </div>
      </div>
    </div>
  </div>
</section>
<section class="probootstrap-section-bg hidden-md hidden-lg overlay category-banner" style="background-image: url(<?= $category_banner_mobile_image; ?>);" data-stellar-background-ratio="0.5">
  <div class="container relative">
    <div class="dark-overlay"></div>
    <div class="row">
      <div class="col-md-12 text-center probootstrap-animate">
        <div class="probootstrap-heading mt-2">
          <h2 class="primary-heading"><?= $pageData['h1_title']; ?></h2>
          <!-- <h3 class="secondary-heading">Our Products</h3> -->
        </div>
      </div>
    </div>
  </div>
</section>
 <!-- probootstrap-bg-white -->
<section class="probootstrap-section">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 mb-4 probootstrap-animate" data-animate-effect="fadeIn">
        <div class="heading"><?= $pageData['description']; ?></div>
      </div>
    </div>
    &nbsp; <!-- Keep as otherwise layout breaks on firefox" -->
    <?php $products = table_fetch_rows('products_categories', 'category_id="'.$pageData['id'].'"', 'id ASC'); 

    if ($products || $subcategories) {

      if ($products) {
        $prodids = [];
        
        foreach ($products as $product) {
          $prodids[] = $product['product_id'];
        }
        
        $prods = table_fetch_rows('products','id IN ('.implode(',',$prodids).') AND status = 1','position ASC'); 
        
      } else { $prods = []; }
      if ($subcategories) {
        $prods = array_merge($subcategories, $prods);
      }  
     
      if ($prods) {
        ?>
        <div class="row row-eq-height">
        <?php
        foreach($prods as $prodData) { 
          if (isset($prodData['parent_id'])) {
            $type = 'category';
          } else {
            $type = 'product';
          }
          $path = get_image($type.'/' . $prodData['id']);
          if (!$path) {
            $path = '/assets/theme/img/placeholder.jpg';
          }
          $path .= '?v='.date('Y-m');
          $description = $prodData['description'];

          ?>

          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-30 probootstrap-animate" data-animate-effect="fadeIn">
            <div class="panel">
              <div class="panel-header">
                <div class="card-img-container">
                  <a href="<?= getRewriteUrl($type,$prodData['id']); ?>">
                    <img class="img-responsive" src="<?= $path; ?>" alt="<?php echo $prodData['name']; ?>">
                  </a>
                </div>
              </div>
              
                      
                <?php if ($prodData['type'] == 'item') { ?>
                  <div class="panel-body">
                    <h3><?php echo $prodData['name']; ?></h3>
                    <p class="price pb-0 mb-0">&pound;<?php echo number_format($prodData['price'],2); ?> each</p>
                    <?php if (strlen($prodData['price_per_kg']) > 0 && $prodData['price_per_kg'] > 0) { ?>
                      <small class="price">(&pound;<?= number_format($prodData['price_per_kg'],2); ?> per kg)</small>
                    <?php } ?>
                    
                  
                    <?php // echo '<div class="mt-2">'.$description.'</div>'; ?> 
                  </div>
                  <div class="panel-footer text-center">
                    <?php if ($prodData['stock'] == 1 && $prodData['stock_level'] > 0) { ?>
                    
                      <a href="<?= getRewriteUrl('product',$prodData['id']); ?>" class="btn btn-warning mt-2">Buy Now<i class="icon-forward pl-1"></i></a>

                    <?php } else { ?>
                     
                        <p>Out of Stock</p>
                    
                    <?php } ?> 
                  </div>
                
               
                <?php } elseif ($prodData['type'] == 'totalweight') { ?>
                  <div class="panel-body">
                    <h3><?php echo $prodData['name']; ?></h3>
                   
                    <?php if (strlen($prodData['price_per_kg']) > 0 && $prodData['price_per_kg'] > 0) { ?>
                      <p class="price">&pound;<?= number_format($prodData['price_per_kg'],2); ?> per kg</p>
                    <?php } ?>
                    <?php // echo  '<div class="mt-2">'.$description.'</div>'; ?> 
                  </div>
                  <div class="panel-footer text-center">
                    <?php if ($prodData['stock'] == 1 && $prodData['stock_level'] > 0) { ?>
                     
                        <a href="<?= getRewriteUrl('product',$prodData['id']); ?>" class="btn btn-warning mt-2">Buy Now<i class="icon-forward pl-1"></i></a>
                      
                    <?php } else { ?>
                      
                        <p>Out of Stock</p>
                      
                    <?php } ?> 
                  </div>
                
               
              <?php } else { ?>
                <div class="panel-body">
                  <h3><?php echo $prodData['name']; ?></h3>
                  <?php
                    $prod_weights = table_fetch_rows('product_weights','product_id="'.$prodData['id'].'" AND status="Available"','pack_price ASC');
                    if ($type == 'product')  {
                      if ($prod_weights) { ?>
                      <p class="price pb-0 mb-0">From £<?= number_format($prod_weights[0]['pack_price'],2); ?></p>
                      
      
                      <?php if (strlen($prodData['price_per_kg']) > 0 && $prodData['price_per_kg'] > 0) { ?>
                        <small class="price">(&pound;<?= number_format($prodData['price_per_kg'],2); ?> per kg)</small>
                      <?php } 
                      }  else { ?>
                        
                          <p>Out of Stock</p>
                        
                      <?php } 
                    } ?> 
                  
                  <?php // echo '<div class="mt-2">'.$description.'</div>'; ?> 
                </div>
                <div class="panel-footer text-center">

                    <?php if ($type == 'category') { ?>

                      <a href="<?= getRewriteUrl('category',$prodData['id']); ?>" class="btn btn-warning">View Products<i class="icon-forward pl-1"></i></a>

                    <?php } else {

                      if ($prodData['stock'] == 1 && count($prod_weights) > 0){ ?>  
                      <a href="<?= getRewriteUrl('product',$prodData['id']); ?>" class="btn btn-warning mt-2">Buy Now<i class="icon-forward pl-1"></i></a>
                   
                      <?php } else { ?>
                        
                          <p>Out of Stock</p>
                        
                      <?php } 


                    }  ?>
                      
                </div>
            
            <?php 
             } ?>
          </div>
        </div>
          <?php
        
    
        } ?>
      
      </div>
    <?php
    } else {
        echo '<p>There are no products in this category.</p>';
      }
    } else {
        echo '<p>There are no products in this category.</p>';
    }
    ?>
  
  </div>
</section>

