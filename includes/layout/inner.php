<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
	<div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<?php include("includes/template/static-banner.php"); ?> 
<section class="probootstrap-section probootstrap-bg-white">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center probootstrap-animate">
        <div class="probootstrap-heading dark">
          <?php
          if(($no_dbanner == 1) || ($no_mbanner == 1)){
            $pagetitle = explode(' ',$pageData['h1_title']);
            if (count($pagetitle) == 1) {
                $pagetitle[0] = '';
            }
            $restofpagetitle = str_replace($pagetitle[0], '', $pageData['h1_title']); ?>
            <h1 class="primary-heading"><?= $pagetitle[0] ;?></h1>
            <h3 class="secondary-heading"><?= $restofpagetitle; ?></h3>
          <?php } ?>
          <span class="seperator">* * *</span>
        </div>
        <?= $pageData['content']; ?>
        <?= $pageData['content_2']; ?>
        <?= $pageData['content_3']; ?>
        <?= $pageData['content_4']; ?>
			<?php if ($rewriteData['url'] == '/faqs') {
				include('includes/template/faqs.php');
			} ?>
        
      </div>
      
    </div>
    <!-- END row -->
  </div>
</section>