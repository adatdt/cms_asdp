 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style>
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/schema_discount/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Kode Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="code" id="code" required placeholder="Kode Schema" value="<?php echo $detail->schema_code; ?>" disabled >

                            <input type="hidden" value="<?php echo $id ?>" name="id">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" required placeholder="Nama Schema" value="<?php echo $detail->description; ?>">

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Slug <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="slug" id="slug" required placeholder="Slug" value="<?php echo $detail->slug; ?>">

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

    function getData()
    {
        if( $("#entry_fee").val()=='')
        {
            var entry_fee=0;

        }
        else
        {
            var entry_fee=parseInt($("#entry_fee").val());            
        }

        if($("#dock_fee").val()=='')
        {
            var dock_fee=0;
        }
        else
        {
            var dock_fee=parseInt($("#dock_fee").val());      
        }

        if($("#ifpro").val()=='')
        {
            var ifpro =0;   
        }
        else
        {
            var ifpro = parseInt($("#ifpro").val());     
        }

        if($("#responsibility_fee").val()=='')
        {
            var responsibility_fee=0;
        }
        else
        {
            var responsibility_fee = parseInt($("#responsibility_fee").val());    
        }

        if($("#trip_fee").val()=='')
        {
            var trip_fee=0;
        }
        else
        {
            var trip_fee = parseInt($("#trip_fee").val());    
        }

        if($("#insurance_fee").val()=='')
        {
            var insurance_fee=0;
        }
        else
        {
            var insurance_fee=parseInt($("#insurance_fee").val()); 
        }

        harga=entry_fee+dock_fee+ifpro+responsibility_fee+insurance_fee+trip_fee;

        $("#fare").val(harga);

        // console.log(harga);

    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#entry_fee").on("keyup mouseup",function(){
            getData()
        });

        $("#dock_fee").on("keyup mouseup",function(){
            getData()
        });

        $("#ifpro").on("keyup mouseup",function(){
            getData()
        });

        $("#trip_fee").on("keyup mouseup",function(){
            getData()
        });

        $("#responsibility_fee").on("keyup mouseup",function(){
            getData()
        });

        $("#insurance_fee").on("keyup mouseup",function(){
            getData();
        });
    })
</script>