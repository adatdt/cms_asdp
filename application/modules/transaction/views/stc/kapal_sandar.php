 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/stc/action_kapal_sandar', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Kapal</label>
                            <select class="form-control select2" name="ship" required="" id="ship">
                                <?php foreach($ship as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id);?>" <?php echo $value->id==$ship_id?"selected":"" ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" class="form-control" name="code" value="<?php echo $this->enc->encode($schedule_code) ?>">
                        </div>

<!--                         <div class="col-sm-6 form-group">
                            <label>Kode Jadwal</label> -->

                            <!-- <input type="text"  name="ship_id" id="ship_id" value="<?php echo $this->enc->encode($ship_id) ?>"> -->
                        <!-- </div> -->



                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-2 form-group">
                                <label>Jam : <?php echo $ploting_date; ?></label>
                                <button  class="btn btn-default form-control" disabled style="background-color: #11c211;"><font color="white">Masuk Alur</font></button>
                        </div>

                        <div class="col-sm-2 form-group">

                            <label>Jam : <?php echo $docking_date; ?></label>
                            <button  class="btn btn-primary form-control" >sandar</button>
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Jam : <?php echo $open_boarding_date; ?></label>
                            <button type="button" class="btn btn-default form-control" disabled >Mulai Pelayanan</button>
                        </div>

                        <div class="col-sm-2 form-group">
                            <label>Jam : <?php echo $close_boarding_date; ?></label>
                            <button type="button" class="btn btn-default form-control" disabled >Selesai Pelayanan</button>
                        </div>

                        <div class="col-sm-2 form-group">
                                <label>Jam : <?php echo $close_ramp_door_date; ?></label>
                                <button type="button" class="btn btn-default form-control" disabled >Tutup Ramdor</button>
                        </div>

                        <div class="col-sm-2 form-group">
                                <label>Jam : <?php echo $sail_date; ?></label>
                                <button type="button" class="btn btn-default form-control" disabled >Berayar</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- <?php echo createBtnForm('Update') ?> -->
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    function paramData(url,data){
        $("#ff").submit(function(e){
            e.preventDefault();
            e.stopPropagation();

            $.ajax({
                url         : url,
                data        : data,
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        location.reload();

                        
                    }else{
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $('#box').unblock(); 
                }
            });
        }

    }
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            paramData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

    })
</script>