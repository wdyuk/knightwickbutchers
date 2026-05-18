<?php

$categories = table_fetch_rows('categories', 'status = 1 AND homepage = 1 AND parent_id = -1', 'position ASC');
                    
?>
<section class="probootstrap-section-bg overlay" style="background-image: url(assets/theme/img/banners/our_products_bg_2.jpg);" data-stellar-background-ratio="0.5" data-section="shop" id="shop">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center probootstrap-animate">
          <div class="probootstrap-heading mt-2">
            <h2 class="primary-heading">Discover</h2>
            <h3 class="secondary-heading">Our Products</h3>
          </div>
        </div>
      </div>
    </div>
  </section>
   <!-- probootstrap-bg-white -->
  <section class="probootstrap-section">
    <div class="container">

      <?php if ($categories) { ?>
        <div class="row row-eq-height">
          <?php
        
            foreach ($categories as $category) { 
              $path = get_image('category/' . $category['id']);
              if (!$path) {
                $path = '/assets/theme/img/placeholder.jpg';
              }
              $path .= '?v='.date('Y-m');
             
              ?>

              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-30 probootstrap-animate" data-animate-effect="fadeIn">
                <div class="panel">
                  <div class="panel-header">
                    <div class="card-img-container">
                      <a href="<?= getRewriteUrl('category',$category['id']); ?>">
                        <img class="img-responsive" src="<?= $path; ?>" alt="<?php echo $category['name']; ?>">
                      </a>
                    </div>
                  </div>
                  <div class="panel-body">
                     <h3><?php echo $category['name']; ?></h3>
                  </div>
                  <div class="panel-footer text-center">
                   <a href="<?= getRewriteUrl('category',$category['id']); ?>" class="btn btn-warning">View Products<i class="icon-forward pl-1"></i></a>
                  </div>
                </div>
              </div>
              <?php 
                if ($catcount == 4 || $totalcount == $totalcats) { ?>
                
              <?php $catcount = 0;  } 
            } ?>
            </div>
        <?php } ?>
      </div>
    </div>
  </section>