<header>
    <?php if (isset($_SESSION['admin'])) { ?>
      
            <div class="col-md-12 status-bar">
                <div class="row">
                    <div class="col-md-7 col-sm-12 col-xs-12"><!-- col-md-5 -->
                        <div class="logo-container-small text-center text-md-left">
                            <a href="<?php echo ADMIN_URL; ?>/"><img src="resources/images/wdy-logo.png" /></a>
                            <?php if(isset($_SESSION['sector'])) {?>
	                        	<button type="button" class="btn btn-info disable header-tabs">You Are Currently in <?php echo $_SESSION['sectortext'];?></button>
	                        	<a href="/control-panel.php?sector=none" class="btn btn-danger header-tabs">Back to Main Dashboard</a>
                                <a href="/control-panel.php?sector=<?= strtolower($_SESSION['sectortext']);?>" class="btn btn-success header-tabs">Back to <?php echo $_SESSION['sectortext'];?> Dashboard</a>
	                    	<?php } ?>
                        </div>
                    </div>
                     
                    <div class="col-md-5 col-sm-12 col-xs-12 text-center text-md-right"><!-- col-md-3 -->
                        <?php require 'logout-box.php'; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 notification-bar">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12"> <!-- col-md-4 -->
                        <div id="new-notification-center" class="text-center"></div>
                    </div>
                </div>
            </div>
        
    <?php }  ?>
</header>