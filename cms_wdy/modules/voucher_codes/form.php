<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    $errors = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('code','description','date_from','date_to','uses_per_customer','total_uses_allowed','percentage_discount','fixed_discount','status');

        if (isset($_POST['date_from']) && strlen($_POST['date_from']) > 0) {
            $date_from = explode('/', $_POST['date_from']);
            $_POST['date_from'] = $date_from[2].'-'.$date_from[1].'-'.$date_from[0];
        } else {
            $_POST['date_from'] = NULL;
        }
        if (isset($_POST['date_to']) && strlen($_POST['date_to']) > 0) {
            $date_to = explode('/', $_POST['date_to']);
            $_POST['date_to'] = $date_to[2].'-'.$date_to[1].'-'.$date_to[0];
        } else {
            $_POST['date_to'] = NULL;
        }
        if (!isset($_POST['uses_per_customer']) || strlen(trim($_POST['uses_per_customer'])) == 0) {
            $_POST['uses_per_customer'] = NULL;
        }
        if (!isset($_POST['total_uses_allowed']) || strlen(trim($_POST['total_uses_allowed'])) == 0) {
            $_POST['total_uses_allowed'] = NULL;
        }
        if (!isset($_POST['percentage_discount']) || strlen(trim($_POST['percentage_discount'])) == 0) {
            $_POST['percentage_discount'] = NULL;
        }
        if (!isset($_POST['fixed_discount']) || strlen(trim($_POST['fixed_discount'])) == 0) {
            $_POST['fixed_discount'] = NULL;
        }
        $check = table_fetch_row('voucher_codes','code ="'.$_POST['code'].'" AND id !="'.$table_id.'"');

        if ($check) {
            $errors[] = 'Code already in use. Please try a different code';
        } else {
            if($_POST['id'] == 0) 
            {

                table_insert('voucher_codes', $fields, $_POST);
                $messages[] = 'Saved successfully.';
                $table_id = mysqli_insert_id($db);
            }
            else 
            {
                table_update('voucher_codes', $fields, $_POST, 'id=' . get_id());
                $messages[] = 'Saved successfully.';
            }   
        }
        
  
        // saveRewrite('voucher_codes',$table_id,'',$_POST['url']);
  
    }
     
    if(!empty($table_id)) {
        if ($check) {
            $data = $_POST;
        } else {
            $data = table_fetch_row('voucher_codes', 'id=' . $table_id); 
        }
        if($data !== false) {
            if (isset($data['date_from']) && strlen($data['date_from']) > 0) {
                $date_from = explode('-', $data['date_from']);
                $data['date_from'] = $date_from[2].'/'.$date_from[1].'/'.$date_from[0];
            }
            if (isset($data['date_to']) && strlen($data['date_to']) > 0) {
                $date_to = explode('-', $data['date_to']);
                $data['date_to'] = $date_to[2].'/'.$date_to[1].'/'.$date_to[0];
            }
        }
    }

    
?>
<form class="validate-form" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
<table>
    <tr>
        <td colspan="2"><h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Voucher Code</h1></td>
    </tr>
    <tr>
        <td colspan="2"><?php show_messages($messages); ?><?php show_errors($errors); ?></td>
    </tr>
    <tr>
        <td>Code:</td>
        <td><input name="code" id="code" size="50" type="text" value="<?php echo isset($data['code']) ? $data['code'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Description:</td>
        <td><?php show_fckeditor('description', isset($data['description']) ? $data['description'] : '' ); ?></td>
    </tr>
    <tr>
        <td>Date From:</td>
        <td><input name="date_from" class="datepicker" id="date_from" autocomplete="off" size="50" type="text" value="<?php echo isset($data['date_from']) ? $data['date_from'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Date To:</td>
        <td><input name="date_to" class="datepicker" id="date_to" autocomplete="off" size="50" type="text" value="<?php echo isset($data['date_to']) ? $data['date_to'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Uses per email address: <small>Leave blank if no limit</small></td>
        <td><input name="uses_per_customer"  id="uses_per_customer" size="50" type="text" value="<?php echo isset($data['uses_per_customer']) ? $data['uses_per_customer'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Total uses allowed:  <small>Leave blank if no limit</small></td>
        <td><input name="total_uses_allowed"  id="total_uses_allowed" size="50" type="text" value="<?php echo isset($data['total_uses_allowed']) ? $data['total_uses_allowed'] : ''; ?>" /></td>
    </tr>
    <tr><td colspan="2">Set only one of the below amounts.  If both are set then the fixed discount will take precedence</td></tr>
    <tr>
        <td>%age discount (before shipping):</td>
        <td><input name="percentage_discount"  id="percentage_discount" size="50" type="text" value="<?php echo isset($data['percentage_discount']) ? $data['percentage_discount'] : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Fixed discount amount £ (before shipping):</td>
        <td><input name="fixed_discount"  id="fixed_discount" size="50" type="text" value="<?php echo isset($data['fixed_discount']) ? $data['fixed_discount'] : ''; ?>" /></td>
    </tr>

    <tr>
        <td>Status:</td>
        <td>
        <select name="status">
            <option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
            <option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
        </select>
    </td>
    </tr>
    <tr>
    <td></td>
        <td><?php show_big_button('save', 'Save'); ?></td>
    </tr>
</table>
</form>

<script>
    $( ".datepicker" ).datepicker({
      dateFormat: "dd/mm/yy"
    });
</script>