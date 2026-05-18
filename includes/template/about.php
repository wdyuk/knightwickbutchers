<section class="probootstrap-section probootstrap-bg-white pb-2" data-section="about">
  <div class="container">
    <div class="row">
      <div class="col-md-5 text-center probootstrap-animate">
        <div class="probootstrap-heading dark">
          <h1 class="primary-heading">About</h1>
          <h3 class="secondary-heading"><?= SITE_NAME; ?></h3>
          <!-- <span class="seperator">* * *</span> -->
        </div>
        <?php echo $pageData['content']; ?>
        <p><a href="/about-us" class="probootstrap-custom-link">About Us</a></p>
      </div>
      <div class="col-md-6 col-md-push-1 probootstrap-animate">
        <p><img src="/assets/theme/img/about-us.jpg" alt="About <?= SITE_NAME; ?>" class="img-responsive"></p>
      </div>
    </div>
    <!-- END row -->
  </div>
</section>