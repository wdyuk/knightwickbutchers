<?php

	$limit = 15;
	$page = 1;
	$conditions = [];

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	if (isset($_GET['filter-products'])) {
        $conditions[] = 'name LIKE "%'.$_GET['filter-products'].'%"';
        $conditions[] = 'SKU LIKE "%'.$_GET['filter-products'].'%"';
    }

	$total_rows = table_row_count('products', implode(' OR ', $conditions));
	$total_pages = ceil($total_rows / $limit);
	
	$rows = table_fetch_rows('products',  implode(' OR ', $conditions),  'position ASC', ($page-1) * $limit, $limit);
    foreach($rows as $key => $row) {
        
        $path = get_image('product/' . $row['id'] . '');
        if (strlen($path) > 0):
            $row['image'] = '<img style="width: 60px; height: auto;" src='.$path.' />';
        else:
            $image['image'] = '';
        endif;
        if ($row['stock'] == 1) {

            $row['stock'] = '<span style="color: #2a2;"><i class="fa fa-check-circle"></i>  ';
            
            if ($row['type'] == 'item') {
                if (strlen($row['stock_level']) > 0 && ($row['stock_level'] > 0)) {
               
                    $row['stock'] .= $row['stock_level'].'</span>';

                } else {
                    $row['stock'] = '<span style="color: #d22;"><i class="fa fa-times-circle"></i> Out of Stock</d>';
                    
                }    
            } elseif ($row['type'] == 'weight') {
                $count = table_row_count('product_weights','product_id="'.$row['id'].'" and status="Available"');
                if ($count > 0) {
                    $row['stock'] .= $count.'</span>';
                } else {
                    $row['stock'] = '<span style="color: #d22;"><i class="fa fa-times-circle"></i> Out of Stock</d>';
                    
                }
            }
            
        } else {
            $row['stock'] = '<span style="color: #d22;"><i class="fa fa-times-circle"></i> Out of Stock</d>';
        }
        if ($row['type'] != 'item') {
            $row['price'] = $row['price_per_kg'].' per kg';
        }
        $rows[$key] = $row;
    }
 ?>
 <h1>Filter</h1>
<form class="form form-inline" action="control-panel.php">
    <input type="hidden" name="module" value="products" />
    <input type="hidden" name="action" value="list" />
    <div class="form-group">
        <label class="pr-4">Search (SKU or Name)</label>
        <input type="text" name="filter-products" class="form-control mr-4" /><input type="submit" class="form-control btn btn-primary" value="filter" />
    </div>
</form>
<table class="list">
<thead>
<tr>
	<td colspan="3"><h1>Products</h1></td>
</tr>

<thead>
<tr>
	<th>&nbsp;</th>
	<th>Name</th>
    <th>SKU</th>
    <th>Image</th>
    <th>Price (£)</th>
    <th>Stock Level</th>
</tr>

</thead>
	
<tbody class="cursor-move">
<?php 
	$operations = array('form', 'delete');
	show_rows($rows, 'products', array('name','SKU','image','price','stock'), $operations, false);
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

                                                var params = 'table=products&' + arr.join('&');
                                                $.post('ajax/sort-order.php', params, function() {

                                                });
                                            }
					});
	
	$('.operation').live('click', function() {
        
		var elm_class = $(this).attr('class'), value = $(this).attr('value');
		
		if (elm_class.indexOf('operation-form') > -1) {
			var location = 'control-panel.php?module=products&action=form&id=' + value;
			window.location = location;
		} else if (elm_class.indexOf('operation-delete') > -1) {
			var $li = $(this).parents('tr');
			
			if (confirm('Are you sure you want to delete this?')) {
				$.post('ajax/delete.php', 'id=' + value + '&table=products', function() {
					$li.remove();
				});
			}
		}
		
		return false;
	});
	
});
</script>

