
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">


            <div class="box-body">
                 <div class="form-group">

                    <div class="row">
                        <div class="col-sm-12 form-inline">

                            <div class="input-group select2-bootstrap-prepend pad-top">
                                <div class="input-group-addon">Cari</div>
                                <input type="text" class="form-control date input-large" name="search" id="search" placeholder="Nomer Invoice">
                                <!-- <button class="btn btn-danger my-button" id="cari">Cari</button> -->

                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button my-data" data-style="zoom-in" id="cari">cari</button>                                
                            </div>
                        </div>
                    </div>

                    <p></p>
                    <?php echo form_open('transaction2/dueDateExtends/action_add', 'id="ff" autocomplete="off"'); ?>                                       
                        <p id="myForm"></p>
                    <?php echo form_close(); ?> 
                </div>
            </div>
            
            
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>

<script type="text/javascript">
    $(document).ready(function(){

        rules   = {extends: {number: true}}
        messages= {extends: {number: "Format Harus Angka"}}

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#cari").click(()=>{

            // var l = Ladda.create(this);
            var number=$("input[name='search']").val();
            var param={number:number}

            myData.searchData(param);
        })
    })
</script>