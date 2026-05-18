
<section class="probootstrap-footer pt-2 pb-2">
  <div class="container">
    <div class="row">
      <div class="col-md-6 probootstrap-animate">
        <div class="probootstrap-footer-widget">
          <h3>Location</h3>
          <div class="row">
            <div class="col-md-6">
              <p><?= $store_settings['company_postal_address']; ?></p>
            </div>
            
          </div>
        </div>
      </div>
      <div class="col-md-6 probootstrap-animate">
        <div class="probootstrap-footer-widget">
          <h3>Information</h3>

          <div class="row">
            <div class="col-md-12">
                <ul class="footer-links">
                    <?php $parents = table_fetch_rows('page', 'status = 1 AND footer_nav = 1 AND parent_id < 0', 'position ASC'); ?>
                    <?php if(count($parents) > 0): ?>
                        <?php foreach($parents as $key => $parent): ?>
                            <li><a href="<?php echo getRewriteUrl('page', $parent['id']); ?>"><?php echo $parent['menu_title']; ?></a></li>
                        <?php endforeach;
                    endif; ?>
                </ul>
              <!-- <p>Monday - Thursday <br> 5:30pm - 10:00pm</p> -->
            </div>
            <!-- <div class="col-md-4">
              <p>Friday - Sunday <br> 5:30pm - 10:00pm</p>
            </div>
            <div class="col-md-4">
              <p>Available for Catering <br> Email or Call Us</p>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="probootstrap-copyright">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <a class="footer-logo" href="/"><img src="<?= SITE_LOGO; ?>"></a>
        <!-- <p class="copyright-text">&copy; <?= date('Y'); ?> <a href="https://uicookies.com/">uiCookies:Resto</a>. All Rights Reserved. Images by <a href="https://graphicburger.com/">GraphicBurger</a> &amp; <a href="https://unsplash.com/">Unsplash</a></p> -->
      </div>
      <div class="col-md-4">
        <ul class="probootstrap-footer-social right">
            <?php if(isset($store_settings['company_facebook']) && strlen($store_settings['company_facebook']) > 0) {
                ?>
                <li><a href="<?= $store_settings['company_facebook']; ?>" target="_blank" rel="noopener noreferrer"><i class="icon-facebook"></i></a></li>
                <?php 
            } ?>
            <?php if(isset($store_settings['company_twitter']) && strlen($store_settings['company_twitter']) > 0) {
                ?>
                <li><a href="<?= $store_settings['company_twitter']; ?>" target="_blank" rel="noopener noreferrer"><i class="icon-twitter"></i></a></li>
                <?php 
            } ?>
            <?php if(isset($store_settings['company_instagram']) && strlen($store_settings['company_instagram']) > 0) {
                ?>
                <li><a href="<?= $store_settings['company_instagram']; ?>" target="_blank" rel="noopener noreferrer"><i class="icon-instagram"></i></a></li>
                <?php 
            } ?>
            <?php if(isset($store_settings['company_pinterest']) && strlen($store_settings['company_pinterest']) > 0) {
                ?>
                <li><a href="<?= $store_settings['company_pinterest']; ?>" target="_blank" rel="noopener noreferrer"><i class="icon-pinterest"></i></a></li>
                <?php 
            } ?>
            <?php if(isset($store_settings['company_youtube']) && strlen($store_settings['company_youtube']) > 0) {
                ?>
                <li><a href="<?= $store_settings['company_youtube']; ?>" target="_blank" rel="noopener noreferrer"><i class="icon-youtube"></i></a></li>
                <?php 
            } ?>
          <!-- <li><a href="#"><i class="icon-twitter"></i><i class="icon-twitter"></i></a></li>
          <li><a href="#"><i class="icon-facebook"></i></a></li>
          <li><a href="#"><i class="icon-instagram"></i></a></li> -->
        </ul>
      </div>
    </div>
  </div>
</section>
<!-- <section id="footer-bottom" class="wrapper alt style1">
    <div class="inner pt-0">
        <div class="row">
            <div class="col-md-12">
                
                <ul class="copyright">

                    <li>&copy; <?= SITE_NAME;?>  All rights reserved.</li>
                    
                    
                </ul>
                <a class="footer-logo" href="/"><img src="/assets/theme/img/logo-small.png" width="70px" ></a>
            </div>
        </div>
    </div>
</section> -->


<script src="https://cdn.jsdelivr.net/gh/igorlino/elevatezoom-plus@1.2.3/src/jquery.ez-plus.js"></script>
<script>
  grecaptcha.ready(function() {
    grecaptcha.execute('<?= GOOGLE_RECAPTCHA_SITE_V3 ?>', {action: 'contact'}).then(function(token) {
      // Add the token to a hidden input field in your form
      var recaptchaResponse = document.getElementById('recaptchaResponse');
      recaptchaResponse.value = token;
    });
  });
</script>
<script>
    $(function() {
        $('#delivery-zoom').ezPlus({
            zoomType: 'lens',
            lensShape: 'square',
            lensSize: 600
        });

        $('#apply_discount').on('click', function(e) {
          e.preventDefault();
          var discountCode = $('#voucher_code').val();
          $.ajax({
            type: 'post',
            url: '/ajax/check-discount.php',
            data: { discountCode : discountCode},
            dataType: 'json',
            success: function (response) {
                if (response.status ==  'success') {
                    window.location.href = "/basket";
                } else {
                    $('#discount-applied').html(response.message);
                   $('#discount-applied').css("color", "#dd2222");
                }
            }
          });
        });
        $('#remove_discount').on('click', function(e) {
          e.preventDefault();
          var discountCode = $('#voucher_code').val();
          $.get( "ajax/remove-discount.php", function( response ) {
            var data = JSON.parse(response);
            console.log(data.status);
            if (data.status == 'success') {
                    window.location.href = "/basket";
            } else {
               alert('Something went wrong');
            }
          });
          
        });
    });
    <?php if (isset($scroll_to_form) && $scroll_to_form == true) {
        ?>
         $('html, body').animate({
                scrollTop: $("section[data-section='contact']").offset().top-100
            }, 1000);
        <?php
    } ?>
    $(document).on('click', 'a[href^="#"]', function (event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top
        }, 1000);
    });
        function displayPopup(){
          $.colorbox({
            html:"<img src='/images/christmas-delivery-2019.png'/>",
            className: "cta",
            width: 550,
            height: 580,
            onClosed: onPopupClose
          });
        }
        function onPopupClose(){
          localStorage.setItem('myPopup','true');
        }
      
</script>
