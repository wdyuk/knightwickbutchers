<?php $params = array_map("sanitize_sql_string",$_GET); ?>
        <div class="container">
    <div class="col-md-12">
        <?php if (isset($_GET['account_activated'])) { ?>
        <div class="alert alert-success">Your account has been activated successfully. Please login below.</div>
        <?php } ?>
        <div class="col-md-12">
            <?php if (!empty($messages)) {
                echo '<div class="alert alert-warning"><ul>';
                foreach($messages as $message) {
                    echo '<li>'.$message.'</li>';
                };
                echo '</ul></div>';
            }; ?>

        </div>
        <form id="login-form" class="validate-form form" method="post" action="/login">
            <div class="form-group">
                <label>Email Address:</label>
                <input class="required email form-control" size="35" maxlength="100" type="text" name="email" id="email" value="" />
            </div> 
            <div class="form-group">
                <label>Password:</label>
                <input class="required form-control" size="35" maxlength="35" type="password" name="password" id="password" value="" />
            </div> 
            <div class="form-group">
              <p>  <a  class="text-left green" href="/forgot-password">Forgot Password?</a> </p> <p> Don't have an account?  Register <a  class="text-left green" href="join">  Here </a> </p> <br /> 

                <input name="login" class="red-btn  btn-more-details-green-square" type="submit" value="Login">
            </div>
        </form>
    </div>
