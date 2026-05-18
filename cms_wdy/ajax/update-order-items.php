<?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    require 'init.php';

    $params = $_POST;
    $order_id = (int)$params['order_id'];

    if(isset($_POST['newPrices'])) {
        try {
            foreach($_POST['newPrices'] as $newPrice) {

                $newPrice = json_decode($newPrice, true);
                $fields = ['price','total'];
                $values = ['price' => $newPrice['price'],'total' => $newPrice['total']];
                table_update('order_items', $fields, $values, 'id="'.$newPrice['id'].'"');

            }
            
            $orderTotal = 0;
            $order_items = table_fetch_rows('order_items', 'order_id="'.$order_id.'"');
            
            foreach($order_items as $order_item) {
                $orderTotal += $order_item['total'];
            }

            $thisorder = table_fetch_row('orders','id="'.$order_id.'"');

            if (isset($thisorder['voucher_discount']) && is_numeric($thisorder['voucher_discount'])) {
                $thisorder['total'] -= $this_order['voucher_discount'];
            }

            $newtotal = number_format($orderTotal + $thisorder['shipping_total'],2);

            $orderfields = ['subtotal','total'];
            $ordervalues = ['subtotal' => $orderTotal,'total' => $newtotal];
            table_update('orders',$orderfields, $ordervalues, 'id="'.$order_id.'"');
            $subtotal_with_shipping = number_format(($thisorder['shipping_total'] + $orderTotal),2);
            $response = ['status' => 'ok', 'message' => 'updated', 'grandtotal' => $newtotal,'subtotal' => $subtotal_with_shipping];

        } catch (Exception $e) {
            $response = ['status' => 'notok', 'message' => 'Not updated'];
        }
    } else {
        $response = ['status' => 'notok', 'message' => 'Not updated - incorrect params provided.'];
    }

echo json_encode($response);
    
?>
