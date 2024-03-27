<style type="text/css">
    .wajib {
        color: red;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('merchant/user/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Username <span class="wajib">*</span></label>
                            <!-- <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo $row->username ?>" required> -->
                            <select id="ship_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="username" required style="width:100%">
                                <?php
                                foreach ($option as $rows) {
                                    if ($rows->username == $row->username) {
                                        $select = 'selected';
                                    } else {
                                        $select = '';
                                    }
                                    echo '<option value="' . $rows->username . '" ' . $select . '>' . $rows->username . '</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <input type="hidden" name="old_username" value="<?php echo $row->username ?>">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Merchant Name <span class="wajib">*</span></label>
                            <input type="text" name="merchant_name" class="form-control" placeholder="Merchant Name" value="<?php echo $row->merchant_name ?>" required minlength="8" maxlength="100">
                        </div>

                        <div class="col-sm-12 form-group "></div>

                        <div class="col-sm-6 form-group">
                            <label>Merchant ID <span class="wajib">*</span></label>
                            <input type="text" name="merchant_id" class="form-control" placeholder="Merchant ID" value="<?php echo $row->merchant_id ?>" required maxlength="30" readonly >
                            <input type="hidden" name="old_merchant_id" value="<?php echo $row->merchant_id ?>" readonly>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Merchant Key <span class="wajib">*</span></label>
                            <input type="text" name="merchant_key" class="form-control" placeholder="Merchant Key" value="<?php echo $row->merchant_key ?>" required minlength="8" maxlength="12">
                        </div>
                        <div class="col-sm-12 form-group "></div>

                        <!-- <div class="col-sm-6 form-group">
                            <label>Merchant Prefix<span class="wajib">*</span></label>
                            <input type="text" name="merchantPrefix" class="form-control" placeholder="Merchant Prefix" required minlength="2" maxlength="2" value="<?php //echo $row->merchant_prefix ?>" >
                        </div> -->

                        <div class="col-sm-6 form-group">
                            <label>Kode Mitra<span class="wajib">*</span></label>
                            <input type="text" name="mitraCode" class="form-control" placeholder="Kode Mitra" required autocomplete="off" value="<?php echo $row->mitra_code ?>" >
                        </div>                                                
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

       messages= {
                    merchantPrefix:{
                                    maxlength:jQuery.validator.format("Max {0} characters"),
                                    minlength:jQuery.validator.format("Min {0} characters")
                                },
                    merchant_key:{
                                    maxlength:jQuery.validator.format("Max {0} characters"),
                                    minlength:jQuery.validator.format("Min {0} characters")
                                },
                    merchant_name:{
                                    maxlength:jQuery.validator.format("Max {0} characters"),
                                    minlength:jQuery.validator.format("Min {0} characters")
                                },
                    merchant_id:{
                                    maxlength:jQuery.validator.format("Max {0} characters")
                                },                                                                                                
                }
                        
        validateForm('#ff', function(url, data) {
            postData(url, data);
        });

        $('.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
        $(".select2").select2({
            placeholder: "Username"
        });
    })
</script>