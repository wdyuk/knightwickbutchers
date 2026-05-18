<?php $slides = table_fetch_rows('homepage_slider','status = 1','position ASC');?>
<section class="flexslider hidden-xs hidden-sm" data-section="welcome To">
  <ul class="slides">
    <?php foreach ($slides as $slide) {
      $desktop_banner = get_image('homepage_slider/'.$slide['id'].'-desktop-slide');?>
      <li style="background-image: url(<?= $desktop_banner; ?>)" class="overlay" data-stellar-background-ratio="0.5">
        <div class="dark-overlay"></div>
        <div class="container">
          <div class="row">
            <div class="col-md-8 col-md-offset-2">
              <div class="probootstrap-slider-text text-center probootstrap-animate probootstrap-heading">
                <h1 class="primary-heading"><?= $slide['heading_line_1']; ?></h1>
                <h3 class="secondary-heading"><?= $slide['heading_line_2']; ?></h3>
                <p class="sub-heading"><?= $slide['content']; ?></p><p><a href="<?= $slide['link_url']?>" class="btn btn-warning" ><?= $slide['button_text']; ?></a></p>
              </div>
            </div>
          </div>
        </div>
      </li>
    <?php } ?>
  </ul>
</section>
<section class="flexslider hidden-md hidden-lg" data-section="welcome To">
  <ul class="slides">
    <?php foreach ($slides as $slide) {
      $mobile_banner = get_image('homepage_slider/'.$slide['id'].'-mobile-slide');?>
      <li style="background-image: url(<?= $mobile_banner; ?>)" class="overlay" data-stellar-background-ratio="0.5">
        <div class="dark-overlay"></div>
        <div class="container">
          <div class="row">
            <div class="col-md-8 col-md-offset-2">
              <div class="probootstrap-slider-text text-center probootstrap-animate probootstrap-heading">
                <h1 class="primary-heading"><?= $slide['heading_line_1']; ?></h1>
                <h3 class="secondary-heading"><?= $slide['heading_line_2']; ?></h3>
                <p class="sub-heading"><?= $slide['content']; ?></p><p><a href="<?= $slide['url_link']?>" class="btn btn-warning" data-nav-section="shop"><?= $slide['button_text']; ?></a></p>
              </div>
            </div>
          </div>
        </div>
      </li>
    <?php } ?>
  </ul>
</section>