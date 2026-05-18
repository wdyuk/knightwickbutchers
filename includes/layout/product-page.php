<?php //include("includes/template/productbanner.php"); ?> 
<?php 
  $product_description = str_replace('<p>','<p class="text-left">', $pageData['description']);
?>
<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<section class="probootstrap-section  probootstrap-bg-white product-page">
  <div class="container">
    <div class="row">
      <div class="col-md-5 text-center probootstrap-animate">
        <?php $image = get_image('product/' . $pageData['id'] . '');  ?>
        <?php if (strlen($image) > 0): ?>
        <img src="<?php echo $image ?>" class="my-20 img-responsive">
        <?php else: ?>
        <img src="/assets/theme/img/placeholder.jpg"  class="my-20 img-responsive">
      <?php endif; ?>
        
      </div>
      <div class="col-md-6 col-md-push-1 probootstrap-animate">
        <div class="probootstrap-heading dark">
          <h3><?= $pageData['name'] ;?></h3>
          <?php echo $product_description; ?>
          <?php if ($pageData['type'] == 'item') { 

            if ($pageData['stock'] == 1 && $pageData['stock_level'] > 0){ 
              if ($pageData['max_purchase'] > $pageData['stock_level']) {
                $max_purchase = $pageData['stock_level'];
              } else {
                if ($pageData['max_purchase'] > 0) {
                  $max_purchase = $pageData['max_purchase'];
                }else{
                  $max_purchase = $pageData['stock_level'];
                }
              }
              if ($max_purchase > 20) {
                $max_purchase = 20;
              } ?>            
              <p class="price">£<?= number_format($pageData['price'],2);?></p>
              <?php if (isset($pageData['price_per_kg']) && $pageData['price_per_kg'] > 0) { ?>
                <p>£<?= number_format($pageData['price_per_kg'],2);?> per kg</p>
              <?php }  ?>
              
              <form method="POST" id="product-form" action="/basket">
                <div class="fields"> 
                  <div class="field">
                    <label class="text-left" >Choose Quantity</label>
                    <select name="qty" id="qty" class="add-basket">
                      <?php 

                      for ($i=1; $i <= $max_purchase; $i++) { ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                      <?php } ?>
                    </select><br />
                  </div>
                  <div class="field">
                    <input type="submit" name="add_to_basket" class="btn btn-warning btn-lg" value="ADD TO BASKET" />
                  </div>
                  
                  <input type="hidden" name="product-id" value="<?= $pageData['id']; ?>" />
                  <input type="hidden" name="product-price" value="<?= $pageData['price']; ?>" />
                </div>
              </form>
          
            <?php  } else { ?>
              

              <p> This Product is currently out of stock </p>
              
            <?php } ?>
            
          <?php } elseif ($pageData['type'] == 'totalweight') { 
            $weightsavailable = $pageData['weights_grams'];
            if (strlen($weightsavailable) == 0) {
              $weightsavailable = '100,200,300,400,500,600,700,800,900,1000,1250,1500,1750,2000,2250,2500,2750,3000,3250,3500,3750,4000,4250,4500,4750,5000';
            }
            $weights = explode(',',$weightsavailable);
            if (!empty($weights)) {
              array_map(function($weight) { 
                $weight = trim($weight);
                $weight = (float) $weight;
                if ($weight == 0) { $weight = null;}
               return $weight;}, $weights);
            }
           
            
           
            if ($pageData['stock'] == 1){ 
              if ($pageData['max_purchase'] > 0) {
                  $max_purchase = $pageData['max_purchase'];
                }else{
                  $max_purchase = 100;
                }
              ?>     
             
              <?php if (isset($pageData['price_per_kg']) && $pageData['price_per_kg'] > 0) { ?>
                <h3 class="basket-block-price mt-2 ">£<?= number_format($pageData['price_per_kg'],2);?> per kg</h3><br />
              <?php } ?>
          
              <form method="POST" id="product-form" action="/basket">
                <div class="fields"> 
                  <div class="field">
                    <label class="text-left" >Choose weight</label>
                   
                    <select name="weight" id="weight" class="add-basket">
                      <?php

                       foreach($weights as $weight) { 
                        if ($weight < 1000) {
                          $weightSuffix = 'g';
                          $weightDisplay = $weight; 
                        } else {
                          $weightSuffix = 'kg';
                          $weightDisplay = ($weight / 1000);
                        } ?>
                        <option value="<?= $weight; ?>"><?= $weightDisplay.$weightSuffix; ?></option>
                      <?php } ?>
                    </select><br />
                  </div>
                  <div class="field">
                    <label class="text-left" >Choose Quantity</label>
                    <select name="qty" id="qty" class="add-basket">
                      <?php for ($i=1; $i <= $max_purchase; $i++) { ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                      <?php } ?>
                    </select><br />
                  </div>
                  <div class="field">
                    <input type="submit" name="add_to_basket" class="btn btn-primary btn-lg" value="ADD TO BASKET" />
                  </div>
                  <input type="hidden" name="product-id" value="<?= $pageData['id']; ?>" />
                  <input type="hidden" name="product-price" value="<?= $pageData['price']; ?>" />
                </div>
              </form>
          
            <?php } else { ?>
            
              <p> This Product is currently out of stock </p>
              <?php echo $product_description; ?>
            <?php }
            } else { 
            $prod_weights = table_fetch_rows('product_weights','product_id="'.$pageData['id'].'" and status="Available"','pack_price ASC');
            if (($pageData['stock'] == 1 && count($prod_weights) > 0)) { ?>
              
              <h3 class="basket-block-price mt-2 ">From £<?= number_format($prod_weights[0]['pack_price'],2);?></h3>
              <p>&pound;<?= $pageData['price'];?> per kg</p>
              <form method="POST" id="product-form" action="/basket">
                <div class="fields"> 
                  <div class="field">
                    <label class="text-left" >Select your pack</label>
                    <select name="product-weight" required>
                    <?php foreach($prod_weights as $prod_weight) { 
                      if (in_array($prod_weight['id'], $cartWeights)) { continue; }?>
                      <option value="<?= $prod_weight['id']; ?>">&pound;<?= $prod_weight['pack_price']; ?> (<?= $prod_weight['weight']; ?>kg )</option>
                    <?php } ?>
                    </select>
                    
                  </div>
                  <div class="field">
                    <input type="submit" name="add_to_basket" class="btn btn-primary btn-lg" value="ADD TO BASKET" />
                  </div>
                  <input type="hidden" name="qty" id="qty" value="1" />
                  <input type="hidden" name="product-id" value="<?= $pageData['id']; ?>" />
                  <input type="hidden" name="product-price" value="<?= $pageData['price']; ?>" />
                </div>
              </form>
            <?php } else { ?>
              <p> This Product is currently out of stock </p>
              <?php echo $product_description; ?>
            <?php } ?>
          <?php } ?>

        </div>
        
      </div>
    </div>
    <!-- END row -->
  </div>
</section>

<script>

    $('.tab-button').click(function(e){
        e.preventDefault();
        $('.tab-button').removeClass('active');
        $(this).addClass('active');

        $('.p-tab-content').removeClass('active');
        $($(this).attr('href')).addClass('active');
    });

 </script>
