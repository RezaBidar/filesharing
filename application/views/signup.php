<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<div class="row">
        
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $this->lang->line('app_form_signup_formname')?></h3>
                    </div>
                    <div class="panel-body">
                        <form action="#" method="post" role="form">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?php echo $this->lang->line('app_form_email')?>" name="email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?php echo $this->lang->line('app_form_password')?>" name="password" type="password" value="">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?php echo $this->lang->line('app_form_password_conf')?>" name="password_conf" type="password" value="">
                                </div>
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="<?php echo $this->lang->line('app_form_btn_signup')?>" />
                            </fieldset>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>