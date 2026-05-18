<?php $parentAbout = table_fetch_row('page',sprintf('id="%d"',$pageData['parent_id']));?>
<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<section class="probootstrap-section-bg overlay" style="background-image: url(/assets/theme/img/banners/our_products_bg_2.jpg);" data-stellar-background-ratio="0.5">
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
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <a href="/" class="header4">  <p class="green lora">Back to full listing</p> </a>
            <br/>
            <div class="col-md-5 col-sm-5 col-xs-12 tyre-border">

              <?php $image = get_img('product/' . $pageData['id']);  ?>
                <?php if (strlen($image) > 0): ?>
                <img src="<?php echo $image ?>" class="mt-20 mb-20"  width="100%">
                <?php else: ?>
                <img src="assets/theme/img/placeholder.jpg"  class=" mt-20 mb-20" width="100%" height="100%" >
              <?php endif; ?>
            </div> 

            <div class="container">
                <div class="col-md-7 col-sm-7 col-xs-12 mt-59 basket-block">
                <div class="col-md-8 col-sm-8 col-xs-8">
                  <h2 class="basket-block-price"><?= $pageData['name'] ;?></h2> <br />
                  <h2 class="basket-block-price mt-20 ">£<?= number_format($pageData['price'],2);?></h2><br />
                    <?php  if ($pageData['stock'] > 0) { ?>
                      <form method="POST" id="product-form" action="/basket">
                        <input type="number" name="qty" id="qty" value="1" class="add-basket" min="1"> <br />
                        <input type="submit" name="add_to_basket" class="blue-btn pull-left btn-more-details-green-square mt-10" value="ADD TO BASKET" />
                          <input type="hidden" name="product-id" value="<?= $pageData['id']; ?>" />
                          <input type="hidden" name="product-price" value="<?= $pageData['price']; ?>" />
                      </form>
                    </div>
                    <?php } else { ?>
                 <p class="text-danger"> This Product is currently out of stock </p>


                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <h2 class="text-left"> Product Description  </h2>
          <?php echo $pageData['description']; ?>

                </div>
            </div>
        </div>
    </div>
</div>
  <script>

      $('.tab-button').click(function(e){
          e.preventDefault();
          $('.tab-button').removeClass('active');
          $(this).addClass('active');

          $('.p-tab-content').removeClass('active');
          $($(this).attr('href')).addClass('active');
      });

   </script>
