 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color:red;}
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/ticket_sobek/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Rute <span class="wajib">*</span></label>
                            <select class="form-control select2"  name="route" id="route" required>
                                <option value="">Pilih</option>
                                <?php foreach($route as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->route_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2"  name="port" id="port" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Transaksi<span class="wajib">*</span></label>
                                <!-- <input type="text" class="form-control date"  name="trx_date" id="trx_date" data-date-format="mm-dd-yyyy" placeholder="DD-MM-YYYY" readonly required> -->

                                

                                <input type="text" class="form-control dateData"  name="trx_date" id="trx_date"  placeholder="DD-MM-YYYY" readonly required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift <span class="wajib">*</span></label>
                            <select class="form-control select2"  name="shift" id="shift" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Penjual<span class="wajib">*</span></label>
                            <select class="form-control select2"  name="ob_code" id="ob_code" required>
                                <option value="">Pilih</option>
                            </select>
                        </div> 

                        <div class="col-sm-4 form-group">
                            <label>Jenis Pengguna Jasa<span class="wajib">*</span></label>
                            <select class="form-control select2"  name="service" id="service" required>
                                <option value="">Pilih</option>
                                <?php foreach($service as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-12 "></div>
                        <!-- <div id="input_ticket"></div> -->

                        <div class="col-md-4">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <label>Pilih File xlsx <span class="wajib">*</span></label>
                                <div class="input-group ">

                                    <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                        <span class="fileinput-filename"> </span>
                                    </div>
                                    <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Pilih File </span>
                                        <span class="fileinput-exists"> Pilih File</span>
                                        <input type="hidden"><input type="hidden"><input type="file" name="excel"  ></span>
                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                                </div>
                            </div>
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
<?php include "fileJs.php"; ?>

<script type="text/javascript">

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.dateData').datepicker({
            // format: 'yyyy-mm-dd',
            format: 'dd-mm-yyyy',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: new Date(),
            startDate:"<?= $dateParameter ?>",
        }).on('change', function() {
                $(this).valid();  // triggers the validation test
                // '$(this)' refers to '$(".date")'
            });        

        $("#port").change(function(){
            getShift()
        });

        $("#shift").change(function(){
            getOb()
        });

        // $("#trx_date").change(function(){
        //     getOb()
        // });

        $("#trx_date").change(function(){
            // $("#trx_date").val(formatDate($("#trx_date2").val()));
            getOb()
        });

        // $("#service").change(function(){
        //     getService()
        // });

        $("#route").change(function(){
            getPort()
        });

        var postData2 = (url,data)=>{
            form = $('form')[0];
            formData = new FormData(form);

            $.ajax({
                url         : url,
                data        :formData,
                type        : 'POST',
                processData: false,  // Important!
                contentType: false,
                cache:false,
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1){
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        $('#dataTables').DataTable().ajax.reload( null, false );
                        $('#dataTables2').DataTable().ajax.reload( null, false );
                        // ambil_data();

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



    })
</script>