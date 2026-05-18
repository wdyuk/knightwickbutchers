<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('question','answer','status');

        if($_POST['id'] == 0) 
        {
            table_insert('faqs', $fields, $_POST);
            $messages[] = 'Saved successfully.';
            $table_id = mysqli_insert_id($db);
        }
        else 
        {
            table_update('faqs', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }   

    }
     
    if(!empty($table_id)) {
        $data = table_fetch_row('faqs', 'id=' . $table_id); 
    }
    
?>
<div class="row">
    <div class="col-md-12">
        <?php if(!empty($messages)) {
           show_messages($messages);
        };
        if(!empty($errors)) {
           show_errors($errors);
        };
        ?>
    </div>
</div>
<form class="validate-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo isset($data['id']) ? $data['id'] : 0 ; ?>" />
    <div class="card mb-4">
        <div class="card-header">
            <h1><?php echo ($table_id == 0) ? 'Add' : 'Edit'; ?> Faq</h1>
        </div>
        <div class="card-body">
           
            <div class="form-group">
                <label for="title">Question:</label>
                <input class="required form-control" name="question" id="question" size="50" type="text" value="<?php echo isset($data['question']) ? $data['question'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="title">Answer:</label>
                <input class="required form-control" name="answer" id="answer" size="50" type="text" value="<?php echo isset($data['answer']) ? $data['answer'] : ''; ?>" />
            </div>
            
           
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" class="form-control">
                    <option value="1" <?php echo (isset($data['status']) && $data['status'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                    <option value="0" <?php echo (isset($data['status']) && $data['status'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
                </select>
            </div>
            <div class="form-group">
            <?php show_big_button('save', 'Save'); ?>
            </div>
        </div>
    </div>
</form>