 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{
        color:red;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/fare_passanger/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Route <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="route" id="route">
                                    <option value="">Pilih</option>
                                <?php foreach($route as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->route_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Penumpang <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="type" id="type">
                                    <option value="">Pilih</option>
                                <?php foreach($type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="ship_type" id="ship_type">
                                <option value="">Pilih</option>
                                <?php foreach ($ship_class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name)?></option>
                                <?php }?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif Masuk</label>
                            <input type="number" class="form-control" name="entry_fee" id="entry_fee"  placeholder="Tarif Masuk"  onkeypress="return isNumberKey(event)">
                        </div>   

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Jasa Deramaga</label>
                            <input type="number" class="form-control" name="dock_fee" id="dock_fee"   placeholder="Jasa Deramaga"  onkeypress="return isNumberKey(event) ">
                        </div>   

                        <div class="col-sm-6 form-group">
                            <label>Ifpro</label>
                            <input type="number" class="form-control" name="ifpro" onkeypress="return isNumberKey(event)"  placeholder="Ifpro" id="ifpro">
                        </div>   

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Tarif Jasa Penyeberangan</label>
                            <input type="number" class="form-control" name="trip_fee" onkeypress="return isNumberKey(event)"  placeholder="Tarif Jasa" id="trip_fee">
                        </div>   

                        <div class="col-sm-6 form-group">
                            <label>Biaya Bertanggung Jawab</label>
                            <input type="number" class="form-control" name="responsibility_fee" onkeypress="return isNumberKey(event)"  placeholder="Biaya Bertanggung Jawab" id="responsibility_fee">
                        </div>   

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Asuransi Jasa Raharja</label>
                            <input type="text" class="form-control" name="insurance_fee" id="insurance_fee" onkeypress="return isNumberKey(event)"  placeholder="Asuransi Jasa Raharja">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Harga <span class="wajib">*</span></label>
                            <input type="number" class="form-control" name="fare" id="fare" onkeypress="" required placeholder="Harga" readonly="">

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