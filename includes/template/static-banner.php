<?php
$nobanner = 0;
$desktop_banner = get_image('page/'.$pageData['id'].'-large-banner');
$mobile_banner = get_image('page/'.$pageData['id'].'-mobile-banner');
if(!$mobile_banner){
    $mobile_banner = $desktop_banner;
}
if(!$desktop_banner){
    $no_dbanner = 1;
}
if(!$mobile_banner){
    $no_mbanner = 1;
}
// if (!$desktop_banner) { $desktop_banner = '/assets/img/page-banners/home.jpg';};
// if (!$mobile_banner) { $mobile_banner = '/assets/img/page-banners/home-mob.jpg';};
if($no_dbanner != 1){
?>
<!-- <div class="row">
    <div class="col-md-12">
        <div class="static-banner hidden-xs hidden-sm">
            <img class="static-banner-image" src="<?= $desktop_banner; ?>" alt="<?= $pageData['h1_title']; ?>" />
            <div class="probootstrap-heading dark centered">
                <h3 class="secondary-heading"><?= $pageData['h1_title']; ?></h3>
            </div>
        </div>
    </div>
</div> -->
<section class="probootstrap-section-bg overlay hidden-xs hidden-sm" style="background-image: url(<?= $desktop_banner; ?>);" data-stellar-background-ratio="0.5">
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
<?php }
if($no_mbanner != 1){ ?>
<!-- <div class="row">
    <div class="col-md-12">
        <div class="static-banner hidden-md hidden-lg">
            <img class="static-banner-image" src="<?= $mobile_banner; ?>" alt="<?= $pageData['h1_title']; ?>" />
            <div class="probootstrap-heading dark centered">
                <h3 class="secondary-heading"><?= $pageData['h1_title']; ?></h3>
            </div>
        </div>
    </div>
</div> -->
<section class="probootstrap-section-bg overlay hidden-md hidden-lg" style="background-image: url(<?= $mobile_banner; ?>);" data-stellar-background-ratio="0.5">
  <div class="container relative">
    <div class="dark-overlay"></div>
    <div class="row">
      <div class="col-md-12 text-center probootstrap-animate">
        <div class="probootstrap-heading mt-5">
          <h2 class="primary-heading"><?= $pageData['h1_title']; ?></h2>
          <!-- <h3 class="secondary-heading">Our Products</h3> -->
        </div>
      </div>
    </div>
  </div>
</section>
<?php } ?>