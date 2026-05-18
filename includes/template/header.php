<!-- Header -->
<!-- <header id="header" class="alt">
    <a href="/" style="position: relative; z-index: 99999;"><img src="/assets/theme/img/logo-small.png" class="topleftlogo" style="margin-top:10px;"></a>
    <div class="middletop"><p>For all enquiries please contact us on <a href="tel:+44123456789">01234 567890</a></p></div>

    <nav>
        <?php if ($cartTotalItems > 0) { ?>
        <div id="mini-basket" class="float-left mr-3"><a href="/basket" style="color: #fff; text-decoration: none;"><i class="fa fa-shopping-cart pr-2"></i><?= '('.$cartTotalItems .') &pound;'. number_format($cartTotal,2); ?></a></div>
    <?php }  ?>
        <a href="#menu">Menu</a>
    </nav>
</header> -->
 <!-- Fixed navbar -->
    
<nav class="navbar navbar-default navbar-fixed-top probootstrap-navbar">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/" title="uiCookies:FineOak"><img src="<?= SITE_LOGO; ?>" alt="<?= SITE_NAME; ?>" class="img-responsive logo-normal" /><img src="<?= SITE_LOGO_ALT; ?>" alt="<?= SITE_NAME; ?>" style="display: none;" class="img-responsive logo-reversed" /></a>
    </div>

    <div id="navbar-collapse" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/">Home</a></li>
        <li><a href="/#delivery" <?php if ($rewriteData['url'] != '/') { echo 'data-home-link="true"'; }; ?> data-nav-section="delivery">Delivery/Collection</a></li>
<!--         <li><a href="#" data-nav-section="gallery">Gallery</a></li> -->
        <li><a href="/#shop" <?php if ($rewriteData['url'] != '/') { echo 'data-home-link="true"'; }; ?> data-nav-section="shop">Shop</a></li>
        <li><a href="/faqs">FAQs</a></li>
        <li><a href="/testimonials">Testimonials</a></li>
        <li><a href="/#contact" <?php if ($rewriteData['url'] != '/') { echo 'data-home-link="true"'; }; ?> data-nav-section="contact">Contact Us</a></li>
        <!-- <?php $parents = table_fetch_rows('page', 'status = 1 AND top_nav = 1 AND parent_id < 0', 'position ASC'); ?>
        <?php if(count($parents) > 0): ?>
        <?php foreach($parents as $key => $parent): ?>
        <li><a href="<?php echo getRewriteUrl('page', $parent['id']); ?>"><?php echo $parent['menu_title']; ?></a></li>
        
        <?php endforeach; ?> -->
        <?php endif; ?>
        <?php if ($cartTotalItems > 0) { 
            if (isset($_SESSION['discount']['grandTotal'])) {
                $miniCartTotal = $_SESSION['discount']['grandTotal']; 
            } else {
                $miniCartTotal = $cartTotal;
            }
        ?>
        <li id="mini-basket" class="float-left mr-3"><a href="/basket" style="text-decoration: none;"><i class="icon-shopping-cart pr-1" style="position: relative; top: 1px;"></i><?= '('.$cartTotalItems .') &pound;'. number_format($miniCartTotal,2); ?></a></li>
    <?php }  ?>
    </ul>
     

  </div>
</nav>


