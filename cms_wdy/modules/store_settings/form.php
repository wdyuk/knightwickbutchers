<?php

    /*
    * To change this template, choose Tools | Templates
    * and open the template in the editor.
    */
    $messages = array();

    
    if(isset($_POST['save']))
    {
        $fields = array('company_email','company_from_email','company_to_emails','company_postal_address','company_collect_address','company_collect_text','company_contact_no','company_postcode','delivery_zone1_radius_miles','free_delivery_zone_1','free_delivery_zone1_minimum_spend','free_delivery_zone_2','delivery_zone2_radius_miles','free_delivery_zone2_minimum_spend','free_delivery_zone_3','delivery_zone3_radius_miles','free_delivery_zone3_minimum_spend','delivery_zone1_cost','delivery_zone2_cost','delivery_zone3_cost','company_facebook','company_twitter','company_pinterest','company_instagram','company_youtube');

        foreach($_POST as $key => $post) {
            if ($post == ''){
                if (strpos($key, 'cost') !== false || strpos($key, 'spend') !== false || strpos($key, 'miles') !== false) {
                    $_POST[$key] = "0.00";
                }
            }
        }
        
        table_update('store_settings', $fields, $_POST, 'id=1');
        $messages[] = 'Saved successfully.';

    }
     
    $data = table_fetch_row('store_settings', 'id=1'); 

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
            <h1>Edit Store Settings</h1>
        </div>
        <div class="card-body">
           
            <div class="form-group">
                <label for="title">Company Email:</label>
                <input class="required form-control" name="company_email" id="company_email" size="50" type="email" value="<?php echo isset($data['company_email']) ? $data['company_email'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="title">Company From Email: (send system emails from eg) noreply@companyname.com)</label>
                <input class="required form-control" name="company_from_email" id="company_from_email" size="50" type="email" value="<?php echo isset($data['company_from_email']) ? $data['company_from_email'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="title">Company To Emails: (send system emails to - can be comma separated if multiple) eg)info@companyname.com, name@companyname.com)</label>
                <input class="required form-control" name="company_to_emails" id="company_to_emails" size="50" type="text" value="<?php echo isset($data['company_to_emails']) ? $data['company_to_emails'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="company_postal_address">Company Address: (To display on website)</label>
                <input class="required form-control" name="company_postal_address" id="company_postal_address" size="50" type="text" value="<?php echo isset($data['company_postal_address']) ? $data['company_postal_address'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="company_collect_address">Company Collection Address: (where customers go to collect orders)</label>
                <input class="required form-control" name="company_collect_address" id="company_collect_address" size="50" type="text" value="<?php echo isset($data['company_collect_address']) ? $data['company_collect_address'] : ''; ?>" />
            </div>
             <div class="form-group">
                <label for="content">Collection Information:</label>
               
                <?php show_fckeditor('company_collect_text', isset($data['company_collect_text']) ? $data['company_collect_text'] : '' ); ?>
                   
            </div>
             <div class="form-group">
                <label for="company_contact_no">Company Contact Number: (To display on website)</label>
                <input class="required form-control" name="company_contact_no" id="company_contact_no" size="50" type="text" value="<?php echo isset($data['company_contact_no']) ? $data['company_contact_no'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="company_postcode">Company Postcode: (To calculate delivery radius)</label>
                <input class="required form-control" name="company_postcode" id="company_postcode" size="50" type="text" value="<?php echo isset($data['company_postcode']) ? $data['company_postcode'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="company_facebook">Company Facebook Link: (Leave blank if don't have one)</label>
                <input class="required form-control" name="company_facebook" id="company_facebook" size="50" type="text" value="<?php echo isset($data['company_facebook']) ? $data['company_facebook'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="company_twitter">Company Twitter Link: (Leave blank if don't have one)</label>
                <input class="required form-control" name="company_twitter" id="company_twitter" size="50" type="text" value="<?php echo isset($data['company_twitter']) ? $data['company_twitter'] : ''; ?>" />
            </div> <div class="form-group">
                <label for="company_instagram">Company Instagram Link: (Leave blank if don't have one)</label>
                <input class="required form-control" name="company_instagram" id="company_instagram" size="50" type="text" value="<?php echo isset($data['company_instagram']) ? $data['company_instagram'] : ''; ?>" />
            </div> <div class="form-group">
                <label for="company_pinterest">Company Pinterest Link: (Leave blank if don't have one)</label>
                <input class="required form-control" name="company_pinterest" id="company_pinterest" size="50" type="text" value="<?php echo isset($data['company_pinterest']) ? $data['company_pinterest'] : ''; ?>" />
            </div> <div class="form-group">
                <label for="company_youtube">Company Youtube Link: (Leave blank if don't have one)</label>
                <input class="required form-control" name="company_youtube" id="company_youtube" size="50" type="text" value="<?php echo isset($data['company_youtube']) ? $data['company_youtube'] : ''; ?>" />
            </div>
            
            <div class="form-group">
                <label for="delivery_zone1_radius_miles">Delivery zone 1 radius in miles. </label>
                <input class="required form-control" name="delivery_zone1_radius_miles" id="delivery_zone1_radius_miles" size="50" type="text" value="<?php echo isset($data['delivery_zone1_radius_miles']) ? $data['delivery_zone1_radius_miles'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="free_delivery_zone_1">Free delivery zone 1</label>
                <select name="free_delivery_zone_1" class="form-control">
                    <option value="1" <?php echo (isset($data['free_delivery_zone_1']) && $data['free_delivery_zone_1'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                    <option value="0" <?php echo (isset($data['free_delivery_zone_1']) && $data['free_delivery_zone_1'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
                </select>
            </div>             
            <div class="form-group">
                <label for="free_delivery_zone1_minimum_spend">Free delivery zone 1 minimum spend. </label>
                <input class="required form-control" name="free_delivery_zone1_minimum_spend" id="free_delivery_zone1_minimum_spend" size="50" type="text" value="<?php echo isset($data['free_delivery_zone1_minimum_spend']) ? $data['free_delivery_zone1_minimum_spend'] : ''; ?>" />
            </div> 
            <div class="form-group">
                <label for="delivery_zone1_cost">Delivery zone 1 cost.</label>
                <input class="required form-control" name="delivery_zone1_cost" id="delivery_zone1_cost" size="50" type="text" value="<?php echo isset($data['delivery_zone1_cost']) ? $data['delivery_zone1_cost'] : ''; ?>" />
            </div>
                   
            <div class="form-group">
                <label for="delivery_zone2_radius_miles">Delivery zone 2 radius in miles. (Match to zone 1 radius if you dont want a zone 2)</label>
                <input class="required form-control" name="delivery_zone2_radius_miles" id="delivery_zone2_radius_miles" size="50" type="text" value="<?php echo isset($data['delivery_zone2_radius_miles']) ? $data['delivery_zone2_radius_miles'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="free_delivery_zone_2">Free delivery zone 2</label>
                <select name="free_delivery_zone_2" class="form-control">
                    <option value="1" <?php echo (isset($data['free_delivery_zone_2']) && $data['free_delivery_zone_2'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                    <option value="0" <?php echo (isset($data['free_delivery_zone_2']) && $data['free_delivery_zone_2'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
                </select>
            </div>     
            <div class="form-group">
                <label for="free_delivery_zone2_minimum_spend">Free delivery zone 2 minimum spend.</label>
                <input class="required form-control" name="free_delivery_zone2_minimum_spend" id="free_delivery_zone2_minimum_spend" size="50" type="text" value="<?php echo isset($data['free_delivery_zone2_minimum_spend']) ? $data['free_delivery_zone2_minimum_spend'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="delivery_zone2_cost">Delivery zone 2 cost.</label>
                <input class="required form-control" name="delivery_zone2_cost" id="delivery_zone2_cost" size="50" type="text" value="<?php echo isset($data['delivery_zone2_cost']) ? $data['delivery_zone2_cost'] : ''; ?>" />
            </div>
              
            <div class="form-group">
                <label for="delivery_zone3_radius_miles">Delivery zone 3 radius in miles. (This is your maximum delivery radius you offer. Match to highest value of zone 2 or zone 1 radius ONLY if you have no maximum delivery radius)</label>
                <input class="required form-control" name="delivery_zone3_radius_miles" id="delivery_zone3_radius_miles" size="50" type="text" value="<?php echo isset($data['delivery_zone3_radius_miles']) ? $data['delivery_zone3_radius_miles'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="free_delivery_zone_3">Free delivery zone 3</label>
                <select name="free_delivery_zone_3" class="form-control">
                    <option value="1" <?php echo (isset($data['free_delivery_zone_3']) && $data['free_delivery_zone_3'] == 1) ? 'selected="selected"' : ''; ?> >Enable</option>
                    <option value="0" <?php echo (isset($data['free_delivery_zone_3']) && $data['free_delivery_zone_3'] == 0) ? 'selected="selected"' : ''; ?> >Disable</option>
                </select>
            </div>    
            <div class="form-group">
                <label for="free_delivery_zone3_minimum_spend">Free delivery zone 3 minimum spend. </label>
                <input class="required form-control" name="free_delivery_zone3_minimum_spend" id="free_delivery_zone3_minimum_spend" size="50" type="text" value="<?php echo isset($data['free_delivery_zone3_minimum_spend']) ? $data['free_delivery_zone3_minimum_spend'] : ''; ?>" />
            </div>
            <div class="form-group">
                <label for="delivery_zone3_cost">Delivery zone 3 cost.</label>
                <input class="required form-control" name="delivery_zone3_cost" id="delivery_zone3_cost" size="50" type="text" value="<?php echo isset($data['delivery_zone3_cost']) ? $data['delivery_zone3_cost'] : ''; ?>" />
            </div>
            
            <div class="form-group">
            <?php show_big_button('save', 'Save'); ?>
            </div>
        </div>
    </div>
</form>