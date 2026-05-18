<?php
    
    require 'init.php';

    $id = (int)$_REQUEST['id'];
    $table = $_REQUEST['table'];

    $where = sprintf('id = %d', $id);

    
    table_update($table,['status'], ['status' => 'Available'], $where);

    
?>
