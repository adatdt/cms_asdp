<style type="text/css">
    .wajib{color: red}

     .switch {
        position: relative;
        display: block;
        width: 90px;
        height: 34px;
        }

        .switch input {display:none;}

        .slidertes {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /*#ca2222;*/
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px !important;
        }

        .slidertes:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50% !important;
        }

        input:checked + .slidertes {
        background-color: #3598dc;
        }

        input:focus + .slidertes {
        box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slidertes:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(55px);
        }

        /*------ ADDED CSS ---------*/
        .slidertes:after
        {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 12px;
        font-family: "Open Sans", sans-serif;
        }

        input:checked + .slidertes:after
        {  
        content:'ON';
        }

</style>

<div class="col-md-7 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/configPrintManifest/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,"",' class="form-control select2" required id="port" ') ?>
                        </div>
                        <div class="col-sm-6">
                            <label>Layanan <span class="wajib">*</span></label>
                            <?= form_dropdown("shipClass",$shipClass,"",' class="form-control select2" required id="shipClass" ') ?>
                        </div>
                        <div class="col-sm-12"></div>

                        <div class="col-sm-6 form-group">
                            <p></p>
                             <label>Status<span class="wajib">*</span></label>
                             <input type="hidden" name="value_param" class="form-control" placeholder="Value Param" required="" value="false" aria-required="false">
                             <label class="switch" style="">
                                <input type="hidden" name="checkbox_param" value="0">
                                <input type="checkbox" name="checkbox_param" id="togBtn" value="1" ><div class="slidertes round"></div>
                            </label>
                         </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">


    $(document).ready(function()
    {

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

    })


</script>