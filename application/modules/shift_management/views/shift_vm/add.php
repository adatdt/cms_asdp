 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/shift_vm/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select22" required name="port" id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Perangkat</label>
                            <select class="form-control select22" required name="device_code" id="device_code">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Shift </label>
                            <select class="form-control select22" required name="shift" id="shift">
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Shift Login</label>
                            <div class="form-inline">
                                <select name="jam_login" class="form-control select-time shift-time-login" id="jam_login" required>
                                    <option value="">Pilih Jam</option>
                                    <?php foreach ($jam as $key => $value) { ?>
                                        <option value="<?= $value ?>"><?= $value ?></option>
                                    <?php }?>
                                </select>

                                <select name="menit_login" class="form-control select-time shift-time-login" id="menit_login" required>
                                    <option value="">Pilih Menit</option>
                                    <?php foreach ($menit as $key => $value) { ?>
                                        <option value="<?= $value ?>"><?= $value ?></option>
                                    <?php }?>
                                </select>
                                <input type="hidden"  name="shift_login" id="shift_login"required >                                    
                            </div>
                            <div id="err-jam-login"  ></div>                                                    
                        </div>        

                        <div class="col-sm-12 form-group"></div>


                        <div class="col-sm-6 form-group">
                            <label>Shift Logout</label>
                            <div class="form-inline">
                                <select name="jam_logout" class="form-control select-time shift-time-logout" id="jam_logout" required>
                                    <option value="">Pilih Jam</option>
                                    <?php foreach ($jam as $key => $value) { ?>
                                        <option value="<?= $value ?>"><?= $value ?></option>
                                    <?php }?>
                                </select>

                                <select name="menit_logout" class="form-control select-time shift-time-logout" id="menit_logout" required>
                                    <option value="">Pilih Menit</option>
                                    <?php foreach ($menit as $key => $value) { ?>
                                        <option value="<?= $value ?>"><?= $value ?></option>
                                    <?php }?>
                                </select>
                                <input type="hidden" name="shift_logout" id="shift_logout"  required >


                            </div>                            
                            <div id="err-jam-logout"  ></div>                            
                        </div>   

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    function getDeviceCode()
    {

        $.ajax({
            type:"post",
            data:{"port":$("#port").val(),
                    "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()},
            url:"<?php echo site_url()?>shift_management/shift_vm/get_device_code",
            dataType:"json",
            success:function(x){

                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length;i++)
                {
                    html +="<option value='"+x[i].terminal_code+"'>"+x[i].full_code+" "+x[i].terminal_type_name+"</option>";                    
                }

                $("#device_code").html(html);
                $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(x[0].tokenHash);
                // console.log(html);   
            }
        });
    }


    $(document).ready(function(){
        validateForm2('#ff',function(url,data){
            postData(url,data);
        });

        $('.select22:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width:"100%"
            });
        });

        $('.select-time:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
            });
        });        

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $("#port").on("change",function(){
            getDeviceCode();
        });

        $(`#jam_login`).on("change", function(){
            
            let jam = $(this).val();
            let menit = $(`#menit_login`).val()

            if(jam == "" || menit == "")
            {
                $(`#shift_login`).val("");
            }
            else
            {
                $(`#shift_login`).val(`${jam}:${menit}`);
            }
        })

        $(`#menit_login`).on("change", function(){
            let menit = $(this).val();
            let jam= $(`#jam_login`).val();

            if(jam == "" || menit == "")
            {
                $(`#shift_login`).val("");
            }
            else
            {
                $(`#shift_login`).val(`${jam}:${menit}`);
            }            
        })  
        
        $(`#jam_logout`).on("change", function(){
            
            let jam = $(this).val();
            let menit = $(`#menit_logout`).val()

            if(jam == "" || menit == "")
            {
                $(`#shift_logout`).val("");
            }
            else
            {
                $(`#shift_logout`).val(`${jam}:${menit}`);
            }
        })

        $(`#menit_logout`).on("change", function(){
            let menit = $(this).val();
            let jam= $(`#jam_logout`).val();

            if(jam == "" || menit == "")
            {
                $(`#shift_logout`).val("");
            }
            else
            {
                $(`#shift_logout`).val(`${jam}:${menit}`);
            }            
        })                

    })
    
    function validateForm2(id, callback) {
        $(id).validate({
            ignore: 'input[type=hidden], .select2-search__field',
            errorClass: 'validation-error-label',
            successClass: 'validation-valid-label',
            rules: rules,
            messages: messages,

            highlight: function (element, errorClass) {
                $(element).addClass('val-error');
            },

            unhighlight: function (element, errorClass) {
                $(element).removeClass('val-error');
            },

            errorPlacement: function (error, element) {
                if (element.parents('div').hasClass('has-feedback')) {
                    error.appendTo(element.parent());
                }

                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select22')) {
                    error.appendTo(element.parent());
                }
                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('shift-time-login')) {
                    // error.html($(`#err-jam-login`));
                    $(`#err-jam-login`).html($(error).prop('outerHTML'))
                }                
                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('shift-time-logout')) {
                    // error.html($(`#err-jam-login`));
                    $(`#err-jam-logout`).html($(error).prop('outerHTML'))
                }                                

                else {
                    error.insertAfter(element);         
                }

                // console.log($(error).prop('outerHTML'))
            },

            submitHandler: function (form) {
                if (typeof callback != 'undefined' && typeof callback == 'function') {
                    callback(form.action, getFormData($(form)));
                }
            }
        });
    }
</script>