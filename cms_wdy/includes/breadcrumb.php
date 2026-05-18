<div class="row">
    <div class="col-md-12">
        <button type="button" id="sidebarCollapse" class="navbar-btn" style="float:left;">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <?php if (isset($_GET['module'])) : ?>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb" style="margin-left: 55px;">
                
            	<?php
        			
        			if (!isset($_GET['module'])) { ?>
        				<li class="breadcrumb-item active" aria-current="page">Home</li>
                        <?php 
        			} else {
                        ?>
                        <li class="breadcrumb-item" aria-current="page"><a href="/">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page"><?= ucfirst(str_replace('_', ' ', $_GET['module'])); ?></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= ucfirst(str_replace('_', ' ', $_GET['action'])); ?></li>
                        <?php
        			}
        		?>
           </ol>
        </nav>
    <?php endif; ?>
    </div>
</div>