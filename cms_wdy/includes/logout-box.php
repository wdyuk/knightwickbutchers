<?php if (isset($_SESSION['admin'])) { ?>
<div class="logout-box">
    	You are logged in as:
    	<a href="?module=admin&action=edit&id=<?php echo $_SESSION['admin']['id']; ?>"><?php echo $_SESSION['admin']['name']; ?></a>
        |
		Last Login:
        <?php echo date('jS F Y H:i', strtotime($_SESSION['admin']['last_login'])); ?>
        &nbsp;
        <?php show_link_btn_arrow('logout.php', 'Logout'); ?>
</div>
<?php } ?>