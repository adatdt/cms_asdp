<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/howtopay/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Tipe Pembayaran</label>
                            <select class="form-control select2" name="payment_type" required >
                                <option value="">Pilih</option>
                                <?php foreach($payment_type as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->payment_type_id?"selected":""; ?> ><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Judul Cara Pembayaran</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Judul" required value="<?php echo $detail->title; ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kode Cara Pembayaran</label>
                            <input type="text" name="pay_code" id="pay_code" readonly class="form-control" placeholder="Judul" required value="<?php echo $detail->code; ?>">

                            <input type="hidden" name="howtopay_id" id="howtopay_id"  required value="<?php echo $this->enc->encode($detail->id); ?>">

                            <input type="hidden" name="code" id="id"  required value="<?php echo $detail->code; ?>">
                        </div>

                        <div class="col-sm-12 form-group">
                            
                            <button class="btn btn-sm btn-warning add_field_button pull-right"><i class="fa fa-plus"></i></button>
                            <p></p>
                            <label>Tambah Langkah Cara Pembayaran</label>
                            
                        </div>

                        <?php if (!empty($detail2)) { 
                            $index=0; foreach ($detail2 as $key => $value) {
                                $id_howtopay_detail="howtopay_detail".$index;

                        ?>
                            
                            <div class="col-sm-12 form-group group-cara" id="<?php echo $id_howtopay_detail ?>">
                                <a class='remove_field pull-right btn btn-danger'  onClick='action_remove2("<?php echo $id_howtopay_detail ?>")'><i class='fa fa-trash'></i></a>
                                <br>
                                <label>Cara Pembayaran</label>
                                <input type="text" name='howtopay_detail[<?php echo $index; ?>]'  class="form-control" placeholder="Cara Pembayaran"  value="<?php echo $value->detail; ?>" >

                                <input type="hidden" name='id_detail[<?php echo $index; ?>]' required value="<?php echo $this->enc->encode($value->id); ?>" >
                            </div>

                        <?php $index++; } }?>


                        <div class="input_fields_wrap"></div>

                    </div>

                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    function action_remove(x)
    {
        $("#"+x).remove();   
    }

    function action_remove2(param)
    {
        let countData = $('.group-cara').length
        if(countData > 1)
        {
            $("#"+param).remove();
        }
        else
        {
            toastr.error("Minimal Harus ada Satu Cara Bayar", 'Gagal');
            
        }
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        var max_fields      = 500; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = 1; //initlal text box count
        var y =0;
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                var idparam="howtopay"+y;
                $(wrapper).append("<div class='col-md-12 form-group group-cara' id='"+idparam+"'> <a class='remove_field pull-right btn btn-danger'  onClick='action_remove("+'"'+idparam+'"'+")'><i class='fa fa-trash'></i></a><br><label>Cara Pembayaran</label><input type='text' class='form-control' placeholder='Cara Pembayaran'  name='howtopay["+y+"]'></div>"); 
                x++;
                y++; 

            }

        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

    })
</script>