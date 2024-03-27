<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/howtopay/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Tipe Pembayaran <span style="color:red">*</span></label>
                            <select class="form-control select2" name="payment_type" required>
                                <option value="">Pilih</option>
                                <?php foreach($payment_type as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Judul Cara Pembayaran <span style="color:red">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Judul" required>
                        </div>

                        <div class="col-sm-12 form-group">
                            
                            <button class="btn btn-md btn-warning add_field_button pull-right"><i class="fa fa-plus"></i></button>
                            <br>
                            <label>Cara Pembayaran <span style="color:red">*</span></label>
                            <input type="text" name="howtopay[0]" id="howtopay" class="form-control" placeholder="Cara Pembayaran" required>
                            
                        </div>

                        <div class="input_fields_wrap"></div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<script type="text/javascript">


    function action_remove(x)
    {
        $("#"+x).remove();   
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        var max_fields      = 500; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                var idparam="howtopay"+x
                $(wrapper).append("<div class='col-md-12 form-group' id='"+idparam+"'> <a class='remove_field pull-right btn btn-danger'  onClick='action_remove("+'"'+idparam+'"'+")'><i class='fa fa-trash'></i></a><br><label>Cara Pembayaran  <span style='color:red'>*</span> </label><input type='text' class='form-control' placeholder='Cara Pembayaran'  name='howtopay["+x+"]' required></div>"); 
                x++; 

            }

        });

        
        // $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        //     e.preventDefault(); $(this).parent('div').remove(); x--;
        // });

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