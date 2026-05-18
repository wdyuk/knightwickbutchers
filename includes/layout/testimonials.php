<?php
$testimonials = table_fetch_rows('testimonials','status=1','position ASC');
?>
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

          <span class="seperator">* * *</span>
        </div>
        <?= $pageData['content']; ?>
        <?php if($testimonials) {
            foreach($testimonials as $testimonial) {
                echo "<blockquote class='text-left'><p class='heading'><em>&ldquo;&nbsp;".strip_tags($testimonial['content'])."&nbsp;&rdquo;</em><h4 class='text-left'>".$testimonial['title']."</h4></blockquote>";
            }
        } ?>
      </div>
      
    </div>
    <!-- END row -->
  </div>
</section>
