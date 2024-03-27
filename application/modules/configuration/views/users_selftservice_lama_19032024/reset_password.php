<div class="col-md-4 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('configuration/users/action_reset_password', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
               <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <label>Password Baru</label>
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password Baru" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="mt-checkbox mt-checkbox-outline"> Lihat Password
                            <input type="checkbox" onclick="showPassword()">
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <?php echo createBtnForm('Update') ?>
        <?php echo form_close(); ?>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });
    })

    function showPassword() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>