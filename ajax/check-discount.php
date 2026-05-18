<?php

require_once("../cms_wdy/application.php");

if(!isset($_POST['discountCode'])) {
    $response = ['status' => 'failed','message' => 'Something went wrong.'];
} else {
    $error = false;
    $discountCode =  sprintf("%s",$_POST['discountCode']);
    $where = 'code = "'.$discountCode.'" AND status = 1';
    $check = table_fetch_row('voucher_codes',$where);
    if ($check) {
        if (isset($check['date_from'])) {
            if (date('Y-m-d') < $check['date_from']) {
                $error = true;
                $error_reason = 'Discount code '.$discountCode.' does not exist or is no longer valid.';
            }
        }
        if (isset($check['date_to'])) {
            if (date('Y-m-d') > $check['date_from']) {
                $error = true;
                $error_reason = 'Discount code '.$discountCode.' does not exist or is no longer valid.a';
            }
        }
        if (isset($check['uses_per_customer'])) {
            //gonna lose this as no accounts so no way of checking this on cart page
        }
        if (!empty($check['total_uses_allowed'])) {
            $total_used = table_fetch_rows('orders','voucher_id="'.$check['id'].'"');
            if (count($total_used) > $check['total_uses_allowed']) {
                $error = true;
                $error_reason = 'Discount code '.$discountCode.' does not exist or is no longer valid.b';
            }
        }
        if (isset($check['fixed_discount'])) {
            $_SESSION['discount']['fixed_discount'] = $check['fixed_discount'];
            if (isset($_SESSION['discount']['percentage_discount'])) {
                unset($_SESSION['discount']['percentage_discount']);
            }
        }elseif (isset($check['percentage_discount'])) {
            $_SESSION['discount']['percentage_discount'] = $check['percentage_discount'];
            if (isset($_SESSION['discount']['fixed_discount'])) {
                unset($_SESSION['discount']['fixed_discount']);
            }
        }else {
            $error = true;
            $error_reason = 'Discount code '.$discountCode.' does not exist or is no longer valid.c';
        }
        if ($error == false) {
            $_SESSION['discount']['code'] = $discountCode;

            $response = ['status' => 'success','message' => 'Discount code '.$discountCode.' is applied. '];
        } else {
            $response = ['status' => 'failed','message' => $error_reason];
        }

    
    } else {
        $response = ['status' => 'failed','message' => 'Discount code '.$discountCode.' does not exist or is no longer valid.'];
    }
}
echo json_encode($response);