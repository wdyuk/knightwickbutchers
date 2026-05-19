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
        <li class="site-search-nav-item">
          <a href="#" class="site-search-toggle" id="site-search-toggle" aria-expanded="false" aria-controls="site-search-panel" aria-label="Open search" role="button">
            <i class="fa fa-search" aria-hidden="true"></i>
            <span class="sr-only">Search</span>
          </a>
          <div class="site-search-panel" id="site-search-panel">
            <form class="site-search-form" method="get" action="/search" role="search">
              <label class="sr-only" for="site-search">Search</label>
              <input type="search" class="form-control site-search-input" id="site-search" name="q" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search products or info" />
              <button type="submit" class="btn btn-warning site-search-button">Search</button>
            </form>
          </div>
        </li>
    </ul>
     

  </div>
</nav>

<style>
  .site-search-nav-item {
    position: relative;
  }

  .probootstrap-navbar .navbar-nav > li.site-search-nav-item {
    height: auto;
  }

  .probootstrap-navbar .navbar-nav > li.site-search-nav-item > .site-search-toggle {
    display: block;
    padding-top: 54px;
    padding-bottom: 54px;
    padding-left: 0;
    padding-right: 0;
    margin-left: 15px;
    margin-right: 15px;
    background: transparent;
    color: #ffffff;
    line-height: 24px;
    font-size: 15px;
    text-align: center;
    text-decoration: none;
    opacity: 1;
    visibility: visible;
  }

  .probootstrap-navbar.scrolled .navbar-nav > li.site-search-nav-item > .site-search-toggle {
    padding-top: 38px;
    padding-bottom: 20px;
    color: rgba(0, 0, 0, 0.7);
    opacity: 1;
    visibility: visible;
  }

  .probootstrap-navbar.scrolled .navbar-nav > li.site-search-nav-item {
    height: auto;
  }

  .site-search-panel {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    z-index: 1000;
    width: 320px;
    padding: 14px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
  }

  .site-search-toggle .fa {
    font-size: 18px;
    line-height: 24px;
    vertical-align: middle;
  }

  .site-search-nav-item.is-open .site-search-panel {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .site-search-form {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .site-search-input {
    min-width: 0;
    flex: 1 1 auto;
    border-radius: 999px;
    border: 1px solid rgba(0, 0, 0, 0.12);
    box-shadow: none;
  }

  .site-search-button {
    border-radius: 999px;
    margin-left: 6px;
    font-weight: 700;
  }

  .probootstrap-navbar .site-search-toggle:hover,
  .probootstrap-navbar .site-search-toggle:focus {
    color: rgba(255, 255, 255, 0.7);
    outline: none;
    background: none;
    text-decoration: none;
  }

  .probootstrap-navbar.scrolled .site-search-toggle:hover,
  .probootstrap-navbar.scrolled .site-search-toggle:focus {
    color: rgba(0, 0, 0, 0.9);
  }

  @media (max-width: 991px) {
    .probootstrap-navbar.scrolled .navbar-nav > li.site-search-nav-item {
      height: auto;
    }

    .probootstrap-navbar .navbar-collapse {
      max-height: calc(100vh - 70px);
      overflow-y: auto;
    }

    .site-search-nav-item {
      display: block;
      padding: 0 15px 18px;
    }

    .site-search-toggle {
      padding-top: 15px !important;
      padding-bottom: 15px !important;
      padding-left: 0;
      padding-right: 0;
      margin-left: 0;
      margin-right: 0;
      color: #222222;
      text-align: left;
      line-height: 24px;
    }

    .site-search-toggle .fa {
      font-size: 18px;
      line-height: 24px;
    }

    .site-search-panel {
      position: static;
      width: 100%;
      margin-top: 12px;
      padding: 0;
      box-shadow: none;
      border-radius: 0;
      background: transparent;
      display: none;
      opacity: 1;
      visibility: visible;
      transform: none;
      transition: none;
    }

    .site-search-nav-item.is-open .site-search-panel {
      display: block;
    }

    .site-search-form {
      display: block;
    }

    .site-search-input {
      width: 100%;
    }

    .site-search-button {
      margin-top: 8px;
      margin-left: 0;
      width: 100%;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var searchNavItem = document.querySelector('.site-search-nav-item');
    var searchToggle = document.getElementById('site-search-toggle');
    var searchInput = document.getElementById('site-search');

    if (!searchNavItem || !searchToggle || !searchInput) {
      return;
    }

    function openSearch() {
      searchNavItem.classList.add('is-open');
      searchToggle.setAttribute('aria-expanded', 'true');
      window.setTimeout(function() {
        searchInput.focus();
      }, 30);
    }

    function closeSearch() {
      searchNavItem.classList.remove('is-open');
      searchToggle.setAttribute('aria-expanded', 'false');
    }

    searchToggle.addEventListener('click', function(event) {
      event.preventDefault();
      event.stopPropagation();
      if (searchNavItem.classList.contains('is-open')) {
        closeSearch();
      } else {
        openSearch();
      }
    });

    document.addEventListener('click', function(event) {
      if (!searchNavItem.contains(event.target)) {
        closeSearch();
      }
    });

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeSearch();
      }
    });

    if (searchInput.value.trim() !== '') {
      openSearch();
    }
  });
</script>


