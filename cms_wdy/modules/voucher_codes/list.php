<?php

    $limit = 15;
    $page = 1;
    
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    }
    
    $total_rows = table_row_count('voucher_codes');
    $total_pages = ceil($total_rows / $limit);
    
    $rows = table_fetch_rows('voucher_codes', '', 'id DESC', ($page-1) * $limit, $limit);
    foreach($rows as $key => $row) {
        if ($row['status'] == 1) {
            $row['status'] = 'Enabled';
        } else {
            $row['status'] = 'Disabled';
        }
        $uses = table_fetch_rows('orders','voucher_id="'.$row['id'].'" AND voucher_code="'.$row['code'].'"');
        if ($uses) {
            $row['uses'] = count($uses);
        } else {
            $row['uses'] = 0;
        }
        if (isset($row['fixed_discount']) && strlen($row['fixed_discount']) > 0) {
            $row['discount'] = '£'.number_format($row['fixed_discount'],2);
        } elseif (isset($row['percentage_discount']) && strlen($row['percentage_discount']) > 0) {
            $row['discount'] = number_format($row['percentage_discount'],2).'%';
        } else {
            $row['discount'] = 'Needs setting';
        }
        $row['description'] = strip_tags($row['description']);
        if (!empty($row['date_from'])) {
            $row['date_from'] = date('d/m/Y', strtotime($row['date_from']));
        }
        if (!empty($row['date_to'])) {
            $row['date_to'] = date('d/m/Y', strtotime($row['date_to']));
        }
        $rows[$key] = $row;
    }
?>

<table class="list">
<thead>
<tr>
    <td colspan="3"><h1>Voucher Codes</h1></td>
</tr>

<thead>
<tr>
    <th>&nbsp;</th>
    <th>Code</th>
    <th>Description</th>
    <th>Discount</th>
    <th>Valid From</th>
    <th>Valid To</th>
    <th># Uses</th>
    <th>Status</th>
</tr>

</thead>
    
<tbody class="cursor-move">
<?php 
    $operations = array('form', 'delete');
    show_rows($rows, 'voucher_codes', array('code','description','discount','date_from','date_to','uses','status'), $operations, true);
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

                                    var params = 'table=voucher_codes&' + arr.join('&');
                                    $.post('ajax/sort-order.php', params, function() {

                                    });
                                }
        });

    $('.operation').live('click', function() {
        
        var elm_class = $(this).attr('class'), value = $(this).attr('value');
        
        if (elm_class.indexOf('operation-form') > -1) {
            var location = 'control-panel.php?module=voucher_codes&action=form&id=' + value;
            window.location = location;
        } else if (elm_class.indexOf('operation-delete') > -1) {
            var $li = $(this).parents('tr');
            
            if (confirm('Are you sure you want to delete this?')) {
                $.post('ajax/delete.php', 'id=' + value + '&table=voucher_codes', function() {
                    $li.remove();
                });
            }
        }
        
        return false;
    });
    
});
</script>

