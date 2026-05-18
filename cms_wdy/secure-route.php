<?php 
  require 'application.php';
  if (!isset($_SESSION['admin'])) {
    redirect('login.php');
  }
 
$selected_module = $_GET['module'];
$selected_action = $_GET['action'];
if (isset($_GET['module'])) {
  $path = sprintf('modules/%s/%s.php', $selected_module, $selected_action);
  require $path;
}