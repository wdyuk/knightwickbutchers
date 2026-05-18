<?php 
    
	require 'application.php';
    require_once("../vendor/autoload.php");
    if (!isset($_SESSION['admin'])) {
        redirect('login.php');
    }
    
?>
<!DOCTYPE html>
<html>
<?php 
$title = "WDY CMS : ".APP_TITLE;
require 'includes/head.php'; ?>

<body class="dashboard">
	<?php require 'includes/header.php'; ?>
    <div class="wrapper">
        <?php require 'includes/menu.php'; ?>
        <!-- Page Content Holder -->
        <div id="content">
           
       
  
            <div id="accordion">
            <?php
                if(!isset($_SESSION['sector'])):

                else:
                    require 'includes/breadcrumb.php';
                endif;
                    if (isset($_GET['module'])) {

                        $path = sprintf('modules/%s/%s.php', $_GET['module'], $_GET['action']);
                        
                        require $path;
                    } else {
                        require 'modules/page/list.php';
                    }
                ?>
            </div>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $(this).toggleClass('active');
            });
        });
    </script>
</body>
</html>