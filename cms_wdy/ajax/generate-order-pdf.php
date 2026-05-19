<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'init.php';
require_once("../../vendor/autoload.php");

use Mpdf\Mpdf;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('Invalid order ID.');
}

$orderId = (int) $_GET['id'];
$order = table_fetch_row('orders', 'id="'.$orderId.'"');

if (!$order) {
    http_response_code(404);
    exit('Order not found.');
}

$store_settings = table_fetch_row('store_settings', 'id=1');
$orderItems = table_fetch_rows('order_items', 'order_id="'.$orderId.'"');

function order_pdf_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function order_pdf_money($value) {
    return number_format((float) $value, 2);
}

function order_pdf_date($value, $format = 'd/m/Y H:i') {
    if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return '';
    }

    return date($format, $timestamp);
}

function order_pdf_comment_html($value) {
    $value = trim((string) $value);

    if ($value === '') {
        return '<p class="muted">None provided.</p>';
    }

    $allowedTags = '<p><br><strong><em><ul><ol><li><b><i>';

    return strip_tags($value, $allowedTags);
}

function order_pdf_two_column_row($leftHtml, $rightHtml) {
    return '
        <table class="two-column">
            <tr>
                <td class="column-left">'.$leftHtml.'</td>
                <td class="column-right">'.$rightHtml.'</td>
            </tr>
        </table>';
}

$itemsRowsHtml = '';
foreach ($orderItems as $orderItem) {
    $product = table_fetch_row('products', 'id="'.$orderItem['product_id'].'"');
    $itemName = $orderItem['name'];
    $itemMeta = array();

    if (!empty($orderItem['weight_text'])) {
        $itemMeta[] = $orderItem['weight_text'];
    }

    if ($product && !empty($product['SKU'])) {
        $itemMeta[] = 'SKU: '.$product['SKU'];
    }

    if (!empty($itemMeta)) {
        $itemName .= '<br><span class="meta">'.order_pdf_escape(implode(' | ', $itemMeta)).'</span>';
    }

    $itemsRowsHtml .= '
        <tr>
            <td>'.$itemName.'</td>
            <td class="number">'.(int) $orderItem['quantity'].'</td>
            <td class="number">£'.order_pdf_money($orderItem['price']).'</td>
            <td class="number">£'.order_pdf_money($orderItem['total']).'</td>
        </tr>';
}

if ($itemsRowsHtml === '') {
    $itemsRowsHtml = '
        <tr>
            <td colspan="4">No order items found.</td>
        </tr>';
}

$deliveryCollectionDate = preferred_fulfilment_date_display($order['delivery_collection_date']);
$isCollection = isset($order['shipping_option_id']) && (int) $order['shipping_option_id'] === 1;
$fulfilmentLabel = $isCollection ? 'Collection' : 'Delivery';

$fulfilmentHtml = '';
if ($isCollection) {
    $fulfilmentHtml .= '
        <div class="card">
            <h2>Collection Details</h2>
            <table class="details">
                <tr><th>Type</th><td>Collection</td></tr>';
    if ($deliveryCollectionDate !== '') {
        $fulfilmentHtml .= '<tr><th>Collection Date</th><td>'.order_pdf_escape($deliveryCollectionDate).'</td></tr>';
    }
    $fulfilmentHtml .= '
            </table>
        </div>';
} else {
    $fulfilmentHtml .= '
        <div class="card">
            <h2>Delivery Details</h2>
            <table class="details">
                <tr><th>Type</th><td>Delivery</td></tr>';
    if ($deliveryCollectionDate !== '') {
        $fulfilmentHtml .= '<tr><th>Shipping Date</th><td>'.order_pdf_escape($deliveryCollectionDate).'</td></tr>';
    }
    $fulfilmentHtml .= '
                <tr><th>Address Line 1</th><td>'.order_pdf_escape($order['shipping_address_line_1']).'</td></tr>
                <tr><th>Address Line 2</th><td>'.order_pdf_escape($order['shipping_address_line_2']).'</td></tr>
                <tr><th>Town</th><td>'.order_pdf_escape($order['shipping_town']).'</td></tr>
                <tr><th>Postcode</th><td>'.order_pdf_escape($order['shipping_postcode']).'</td></tr>
            </table>
        </div>';
}

$voucherRowsHtml = '';
if (isset($order['voucher_discount']) && $order['voucher_discount'] !== null && $order['voucher_discount'] !== '' && (float) $order['voucher_discount'] > 0) {
    $voucherRowsHtml = '
        <tr>
            <th colspan="3">Voucher'.(!empty($order['voucher_code']) ? ' ('.order_pdf_escape($order['voucher_code']).')' : '').'</th>
            <td class="number">-£'.order_pdf_money($order['voucher_discount']).'</td>
        </tr>';
}

$customerDetailsHtml = '
    <div class="card">
        <h2>Customer Details</h2>
        <table class="details">
            <tr><th>Name</th><td>'.order_pdf_escape(trim($order['title'].' '.$order['firstname'].' '.$order['lastname'])).'</td></tr>
            <tr><th>Email</th><td>'.order_pdf_escape($order['email']).'</td></tr>
            <tr><th>Mobile</th><td>'.order_pdf_escape($order['mobile_number']).'</td></tr>
            <tr><th>Telephone</th><td>'.order_pdf_escape($order['telephone_number']).'</td></tr>
            <tr><th>Payment Status</th><td>'.order_pdf_escape($order['status']).'</td></tr>
        </table>
    </div>';

$topDetailsHtml = order_pdf_two_column_row($customerDetailsHtml, $fulfilmentHtml);

$html = '
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            color: #222222;
        }
        h1, h2, h3 {
            color: #1f365c;
            margin: 0 0 10px 0;
        }
        h1 {
            font-size: 20pt;
            margin-bottom: 8px;
        }
        h2 {
            font-size: 14pt;
            margin-top: 20px;
        }
        p {
            margin: 0 0 8px 0;
        }
        .muted {
            color: #666666;
        }
        .header {
            border-bottom: 2px solid #1f365c;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: 0;
            padding: 0;
            vertical-align: bottom;
        }
        .header-table .header-title {
            text-align: left;
        }
        .header-table .header-fulfilment {
            text-align: right;
            font-size: 18pt;
            font-weight: bold;
            color: #1f365c;
        }
        .subheading {
            color: #666666;
            font-size: 10pt;
        }
        .card {
            margin-bottom: 16px;
        }
        table.two-column {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 16px;
        }
        table.two-column td {
            width: 50%;
            vertical-align: top;
        }
        table.two-column td.column-left {
            padding-right: 8px;
        }
        table.two-column td.column-right {
            padding-left: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.details th,
        table.details td,
        table.items th,
        table.items td,
        table.totals th,
        table.totals td {
            border: 1px solid #d8dce5;
            padding: 8px;
            vertical-align: top;
        }
        table.details th,
        table.items th,
        table.totals th {
            background: #f2f5fb;
            text-align: left;
            width: 28%;
        }
        table.items th,
        table.items td.number,
        table.totals td.number {
            text-align: right;
        }
        table.items th:first-child,
        table.items td:first-child {
            text-align: left;
        }
        .meta {
            color: #666666;
            font-size: 9pt;
        }
        .comments {
            border: 1px solid #d8dce5;
            padding: 10px;
            background: #fafbfd;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-title"><h1>Order #'.(int) $order['id'].'</h1></td>
                <td class="header-fulfilment">'.order_pdf_escape($fulfilmentLabel).'</td>
            </tr>
        </table>
        <p class="subheading">'.order_pdf_escape(SITE_NAME).'</p>
    </div>

    '.$topDetailsHtml.'

    <div class="card">
        <h2>Order Items</h2>
        <table class="items">
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Item Price</th>
                <th>Total</th>
            </tr>
            '.$itemsRowsHtml.'
        </table>
    </div>

    <div class="card">
        <h2>Totals</h2>
        <table class="totals">
            <tr>
                <th colspan="3">Sub Total</th>
                <td class="number">£'.order_pdf_money($order['subtotal']).'</td>
            </tr>
            <tr>
                <th colspan="3">Shipping</th>
                <td class="number">£'.order_pdf_money($order['shipping_total']).'</td>
            </tr>
            '.$voucherRowsHtml.'
            <tr>
                <th colspan="3">Grand Total</th>
                <td class="number">£'.order_pdf_money($order['total']).'</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Internal Comments</h2>
        <div class="comments">
            '.order_pdf_comment_html($order['comments']).'
        </div>
    </div>

    <div class="card">
        <h2>Customer Comments</h2>
        <div class="comments">
            '.order_pdf_comment_html($order['customer_comments']).'
        </div>
    </div>
</body>
</html>';

$mpdf = new Mpdf(array(
    'format' => 'A4',
    'margin_top' => 12,
    'margin_bottom' => 12,
    'margin_left' => 12,
    'margin_right' => 12,
));

$mpdf->SetTitle('Order #'.$orderId);
$mpdf->WriteHTML($html);
$mpdf->Output('order-'.$orderId.'.pdf', \Mpdf\Output\Destination::INLINE);
exit;

?>
