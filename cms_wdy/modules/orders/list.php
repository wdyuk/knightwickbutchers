<?php

	$limit = 30;
	$page = 1;
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}

	$conditions = array();


	if (isset($_GET['submitfilter'])):
		$text = $_GET['textfilter'];
        $status = $_GET['statusfilter'];
        $orderstatus = $_GET['orderstatusfilter'];
		$conditions = array();
		$conditions[] = '(firstname LIKE "%'.$text.'%")';
		$conditions[] = '(lastname LIKE "%'.$text.'%")';
		$conditions[] = '(email LIKE "%'.$text.'%")';
		$conditions[] = '(id LIKE "%'.$text.'%")';

		$conditions = '('.implode(' OR ', $conditions).' )';

        if ($_GET['statusfilter'] != "") {
            if ($_GET['statusfilter'] == 'ALL_INCLUDING_PENDING') {

            } else {
                if ($_GET['statusfilter'] != "PENDING") {
                    $conditions .= ' AND ( status="'.$status.'" AND status != "PENDING")';
                } else {
                    $conditions .= ' AND status="'.$status.'"';
                }
                
            }
            
        } else {
            $conditions .= ' AND status != "PENDING"';
        }

        if ($_GET['orderstatusfilter'] != "ALL") {
            $conditions .= ' AND order_status="'.$orderstatus.'"';
        };

        if (isset($_GET['dateFrom']) && strlen($_GET['dateFrom']) > 0) {
            $dateFrom = $_GET['dateFrom'];
            $date = explode('/',$_GET['dateFrom']);
            if(stripos($_GET['dateFrom'],'/') !== false) {
	            $date = explode('/',$_GET['dateFrom']);
	            $_GET['dateFrom'] = $date[2].'-'.$date[1].'-'.$date[0];
	        }
       
            $conditions .= ' AND created_at >= "'.$_GET['dateFrom'].'"';
        };
        if (isset($_GET['dateTo']) && strlen($_GET['dateTo']) > 0) {
            $dateTo = $_GET['dateTo'];
            if(stripos($_GET['dateTo'],'/') !== false) {
	            $date = explode('/',$_GET['dateTo']);
	            $_GET['dateTo'] = $date[2].'-'.$date[1].'-'.$date[0];
	        }
       
            $conditions .= ' AND created_at <= "'.$_GET['dateTo'].'"';
        };
        if (isset($_GET['delDate']) && strlen($_GET['delDate']) > 0) {
            $delDate = $_GET['delDate'];
            if(stripos($_GET['delDate'],'/') !== false) {
	            $date = explode('/',$_GET['delDate']);
	            $_GET['delDate'] = $date[2].'-'.$date[1].'-'.$date[0];
	        }
            $conditions .= ' AND delivery_collection_date = "'.$_GET['delDate'].'"';
        };

	else:
		$conditions = ' status != "PENDING"';

	endif;
   
    // if (empty($conditions))
	$total_rows = table_row_count('orders',$conditions);
	$total_pages = ceil($total_rows / $limit);
    $all_rows = table_fetch_rows('orders','','');
	$rows = table_fetch_rows('orders', $conditions, 'created_at DESC', ($page-1) * $limit, $limit);

    $orders_to_prepare = 0;
    $orders_to_fulfill = 0;
    $orders_to_ship = 0;
    $orders_to_collect = 0;

    foreach($all_rows as $all_row) {
        if ($all_row['status'] == 'CARD AUTHORISED') {
            $orders_to_prepare++;
        }
        if ($all_row['status'] == 'PAID' && $all_row['order_status'] == 'AWAITING') {
            $orders_to_fulfill++;
        }
        if ($all_row['status'] == 'PAID' && $all_row['order_status'] == 'FULFILLED' && $all_row['shipping_option_id'] == 0) {
            $orders_to_ship++;
        }
        if ($all_row['status'] == 'PLACED FOR COLLECTION' && $all_row['order_status'] == 'FULFILLED' && $all_row['shipping_option_id'] == 1) {
            $orders_to_collect++;
        }

    }

    foreach($rows as $key => $row) {

        $row['created_at'] = date('d/m/Y H:i',strtotime($row['created_at']));
        $row['updated_at'] = date('d/m/Y H:i',strtotime($row['updated_at']));
        if (isset($row['delivery_collection_date']) && strlen($row['delivery_collection_date']) > 0) {
            $row['delivery_collection_date'] = date('d/m/Y',strtotime($row['delivery_collection_date']));
            
        }
       
       
        $rows[$key] = $row;
    }
	
?>

<form>
	<h2>Filter Orders</h2>
	<input type="hidden" name="module" value="orders" />
	<input type="hidden" name="action" value="list" />
    
    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="textfilter">Filter by name, email or order id</label>
                <input name="textfilter" class="form-control" id="textfilter" <?php echo isset($text) ? 'value="'.$text.'"':null;?>/>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="statusfilter">Payment Status</label>
                <select name="statusfilter" class="form-control" id="statusfilter">
                    <option value="">ALL (except unpaid/incomplete)</option>
                    <option value="ALL_INCLUDING_PENDING" <?php echo isset($status) && $status == "ALL_INCLUDING_PENDING" ? 'selected="selected"' : "";?>>ALL (including unpaid/incomplete)</option>
                    <option value="PAID" <?php echo isset($status) && $status == "PAID" ? 'selected="selected"' : "";?>>PAID</option>
                    <option value="CANCELLED" <?php echo isset($status) && $status == "CANCELLED" ? 'selected="selected"' : "";?>>CANCELLED</option>
                    <option value="REFUNDED" <?php echo isset($status) && $status == "REFUNDED" ? 'selected="selected"' : "";?>>REFUNDED</option>
                    <option value="PLACED FOR COLLECTION" <?php echo isset($status) && $status == "PLACED FOR COLLECTION" ? 'selected="selected"' : "";?>>PLACED FOR COLLECTION</option>
                    <option value="CARD AUTHORISED" <?php echo isset($status) && $status == "CARD AUTHORISED" ? 'selected="selected"' : "";?>>CARD AUTHORISED</option>
                    <option value="PENDING" <?php echo isset($status) && $status == "PENDING" ? 'selected="selected"' : "";?>>PENDING (Unpaid or Incomplete)</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="orderstatusfilter">Order Status</label>
                <select name="orderstatusfilter" class="form-control" id="orderstatusfilter">
                    <option value="ALL">ALL</option>
                    
                    <option value="AWAITING" <?php echo isset($orderstatus) && $orderstatus == "AWAITING" ? 'selected="selected"' : "";?>>AWAITING</option>
                    <option value="FULFILLED" <?php echo isset($orderstatus) && $orderstatus == "FULFILLED" ? 'selected="selected"' : "";?>>FULFILLED</option>
                    <option value="SHIPPED" <?php echo isset($orderstatus) && $orderstatus == "SHIPPED" ? 'selected="selected"' : "";?>>SHIPPED</option>
                    <option value="COLLECTED" <?php echo isset($orderstatus) && $orderstatus == "COLLECTED" ? 'selected="selected"' : "";?>>COLLECTED</option>
                   
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="dateFrom">Order Date From</label>
               <input name="dateFrom" class="form-control datepicker" id="dateFrom" <?php echo isset($dateFrom) ? 'value="'.$dateFrom.'"':null;?>/>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="dateTo">Order Date To</label>
               <input name="dateTo" class="form-control datepicker" id="dateTo" <?php echo isset($dateTo) ? 'value="'.$dateTo.'"':null;?>/>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="form-group">
                <label for="delDate">Delivery/Collection Date</label>
               <input name="delDate" class="form-control datepicker" id="delDate" <?php echo isset($delDate) ? 'value="'.$delDate.'"':null;?>/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="form-group my-2">
                <input type="submit" class="btn btn-primary my-4" name="submitfilter" value="Filter Orders" />
            </div>
        </div>
    </div>
</form>

    <div class="row">
        <div class="col-12 my-2 text-left">
            <?php if ($orders_to_prepare > 0) {
                ?>
                
                    <div class="badge badge-warning mr-3 py-2 px-2" style="background: #fff3cd;">
                        <i class="fa fa-exclamation pr-2"></i><?= $orders_to_prepare; ?><small class="pl-2">Need payment setting/collecting</small>  
                    </div>
                
                <?php
            } ?>
            <?php if ($orders_to_fulfill > 0) {
                ?>
                
                    <div class="badge badge-success mr-3 py-2 px-2" style="background: #cdffcd; color: #222;">
                        <i class="fa fa-exclamation pr-2"></i><?= $orders_to_fulfill; ?><small class="pl-2">Need fulfilling</small>  
                    </div>
                
                <?php
            } ?>
            <?php if ($orders_to_ship > 0) {
                ?>
                
                    <div class="badge badge-success mr-3 py-2 px-2"  style="background: #77ff55; color: #222;">
                        <i class="fa fa-exclamation pr-2"></i><?= $orders_to_ship; ?><small class="pl-2">Need shipping</small>  
                    </div>
               
                <?php
            } ?>
            <?php if ($orders_to_collect > 0) {
                ?>
                
                    <div class="badge badge-success mr-3 py-2 px-2" style="background: #ccffaa; color: #222;">
                        <i class="fa fa-exclamation pr-2"></i><?= $orders_to_collect; ?><small class="pl-2">Awaiting collection</small>  
                    </div>
                
                <?php
            } ?>
       </div>
    </div>

<table class="list">
<thead>
<tr>
	<td colspan="3"><h1>Orders</h1></td>
</tr>
<thead>
<tr>
	<th>&nbsp;</th>
	<th>Order ID #</th>
	<th>Email</th>
	<th>Created At</th>
    <th>Last Updated</th>
	<th>Payment Status</th>
    <th>Fulfillment/Shipping Status</th>
    <th>Del/Col Date</th>
</tr>
</thead>
<tbody class="cursor-move">
<?php 
	$operations = array('form','delete');
	show_rows($rows, 'orders', array('id','email','created_at','updated_at','status','order_status','delivery_collection_date'), $operations);
?>
</tbody>
<tfoot>
<tr>
	<td colspan="3"><?php show_pagination($total_pages, $page); ?></td>
</tr>
</tfoot>
</table>
<?php require 'modules/delete.php'; ?>
<script type="text/javascript">
$(function() {
	$('table.list > tbody').sortable({
                                            axis: 'y',
                                            opacity: 0.5,
                                            stop: function (evt, ui) {
                                                var arr = [];
                                                $('table.list > tbody tr').each(function(i) {
                                                        var row_id = $(this).attr('row_id');
                                                        arr[arr.length] = 'positions[' + row_id + ']=' + i;
                                                });
                                                var params = 'table=orders&' + arr.join('&');
                                                $.post('ajax/sort-order.php', params, function() {
                                                });
                                            }
					});
	$('.operation').on('click', function() {
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=orders&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parents('tr');
			if (confirm('Are you sure you want to delete this?')) {
				$.post('ajax/delete.php', 'id=' + value + '&table=orders', function() {
					$li.remove();
				});
			}
		}
		return false;
	});
    $('.datepicker').datepicker({
        dateFormat : 'dd/mm/yy'
    });
});
</script>
