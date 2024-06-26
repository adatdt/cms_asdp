<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/payment_type/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nama" value="<?php echo $detail->name ?>" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Pembayaran</label>
                            <input type="text" name="payment_type" id="payment_type" class="form-control" placeholder="Tipe Pembayaran" value="<?php echo $detail->payment_type ?>" required>
                            <input type="hidden" value="<?php echo $this->enc->encode($detail->id); ?>" name="payment_type_id">
                        </div>


                        <div class="col-sm-4 form-group">
                            <label>Metode Pembayaran</label>
                            <select class="form-control select2" required name="payment_method">
                                <option value="">Pilih</option>
                                <?php foreach($method as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->payment_method_id==$value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Biaya Tambahan</label>
                            <input type="number" name="extra_fee" id="extra_fee" class="form-control" placeholder="Biaya Tambahan" required onkeypress="return isNumberKey(event)" value="<?php echo $detail->extra_fee ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Bank</label>
                            <select class="form-control select2"  name="bank">
                                <option value="">Pilih</option>
                                <?php foreach($bank as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"  <?php echo $detail->bank_id==$value->id?"selected":""; ?>> <?php echo strtoupper($value->bank_name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>order</label>
                            <input type="number" name="order" id="order" class="form-control" placeholder="Order" required onkeypress="return isNumberKey(event)" value="<?php echo $detail->order ?>">
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-4 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='status_web' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->status_web==1?"checked":""; ?> > Status Web
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>
                                        <input type="checkbox" class="allow" name='status_mobile' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->status_mobile==1?"checked":""; ?> > Status Mobile
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">

                                    <label>
                                        <input type="checkbox" class="allow" name='status_mpos' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->status_mpos==1?"checked":""; ?>> Status Mpos
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   
                                    <label>
                                        <input type="checkbox" class="allow" name='status_vm' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->status_vm==1?"checked":""; ?>> Status VM
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='status_pos_passanger' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->status_pos_passanger==1?"checked":""; ?>> Status POS Penumpang
                                    </label>

                                    <label>
                                        <input type="checkbox" class="allow" name='status_pos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes"  <?php echo $detail->status_pos_vehicle==1?"checked":""; ?> > Status POS Kendaraan
                                    </label>

                                </div>
                            </div>
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