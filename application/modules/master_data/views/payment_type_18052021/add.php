<style type="text/css">
    .wajib{
        color:red;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/payment_type/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Nama <span class="wajib">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nama" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Pembayaran <span class="wajib">*</span></label>
                            <input type="text" name="payment_type" id="payment_type" class="form-control" placeholder="Tipe Pembayaran" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Metode Pembayaran <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="payment_method">
                                <option value="">Pilih</option>
                                <?php foreach($method as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Biaya Tambahan <span class="wajib">*</span></label>
                            <input type="number" name="extra_fee" id="extra_fee" class="form-control" placeholder="Biaya Tambahan" required onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Bank</label>
                            <select class="form-control select2"  name="bank">
                                <option value="">Pilih</option>
                                <?php foreach($bank as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->bank_name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Pembayaran <span class="wajib">*</span></label>
                            <select class="form-control select2"  name="pay_type" required>
                                <option value="">Pilih</option>
                                <?php foreach($pay_type as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-4 form-group">
                            <label>order <span class="wajib">*</span></label>
                            <input type="number" name="order" id="order" class="form-control" placeholder="Order" required onkeypress="return isNumberKey(event)">
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-3 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='status_web' data-checkbox="icheckbox_flat-grey"  value="yes"> Status Web
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>
                                        <input type="checkbox" class="allow" name='status_mobile' data-checkbox="icheckbox_flat-grey"  value="yes"> Status Mobile
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">

                                    <label>
                                        <input type="checkbox" class="allow" name='status_mpos' data-checkbox="icheckbox_flat-grey"  value="yes"> Status Mpos
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>
                                        <input type="checkbox" class="allow" name='status_vm' data-checkbox="icheckbox_flat-grey"  value="yes"> Status VM
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">

                                    <label>
                                        <input type="checkbox" class="allow" name='status_pos_passanger' data-checkbox="icheckbox_flat-grey"  value="yes"> Status POS Penumpang
                                    </label>

                                    <label>
                                        <input type="checkbox" class="allow" name='status_pos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes"> Status POS Kendaraan
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">

                                    <label>
                                        <input type="checkbox" class="allow" name='status_ifcs' data-checkbox="icheckbox_flat-grey"  value="yes"> Status IFCS
                                    </label>

                                </div>
                            </div>
                        </div>

<!--                         <div class="col-sm-12 form-group">
                            
                            <button class="btn btn-sm btn-warning add_field_button pull-right"><i class="fa fa-plus"></i></button>
                            <p></p>
                            <label>Judul Pembayaran</label>

                            <input type="text" name="title" id="title" class="form-control" placeholder="Judul Pembayaran" required>
                            <p></p>
                            
                        </div> -->

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<script type="text/javascript">



    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var data_input ="<div class='col-md-12 form-group'><a class='remove_field pull-right btn btn-danger' style='margin-bottom:10px;'><i class='fa fa-trash'></i></a><input type='text' class='form-control' placeholder='Langkah Pembayaran'><div>"

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                $(wrapper).append(data_input); 
                x++; 

            }

        });
        
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
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