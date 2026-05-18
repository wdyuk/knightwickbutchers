<?php

require_once("../cms_wdy/application.php");
if (isset($_SESSION['discount'])) {
    unset($_SESSION['discount']);
    $response = ['status' => 'success'];
}
echo json_encode($response, JSON_UNESCAPED_SLASHES);