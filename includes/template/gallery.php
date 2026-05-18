<section class="probootstrap-section probootstrap-bg-white pb-2" data-section="gallery">
  <div class="container">
    <div class="row">
      <div class="col-md-5 text-center probootstrap-animate pt-0">
        <div class="probootstrap-heading dark">
          <h1 class="primary-heading">Photo</h1>
          <h3 class="secondary-heading">Gallery</h3>   
        </div>
      </div>
      <div class="col-md-6 col-md-push-1 probootstrap-animate">
        <?php echo $pageData['content_3']; 

        $photos = get_table_photos('gallery', 1, 'FULL');
        $counter = 0;
        if($photos != NULL): 
            foreach($photos as $key => $photo):
                $counter++;
            ?>  
            <p class="text-center">
                <a class="btn btn-primary" href="<?= $photo['file']; ?>" data-lightbox="gallery-set">VIEW GALLERY</a>
            </p>                                                            
        <?php 
        if ($counter == 1) { break; };
            endforeach;
        endif; ?>
        
        <?php 
        $counter = 0;
        if($photos != NULL): 
            foreach($photos as $key => $photo):
                $counter++;
                if ($counter == 1) {
                    continue;
                }
            ?>                                                          
            <a style="display: none;"  href="<?= $photo['file']; ?>" data-lightbox="gallery-set"></a>
            <?php if ($counter == 1) { continue; };
             endforeach;
        endif; ?>
      </div>
    </div>
    <!-- END row -->
  </div>
</section>    