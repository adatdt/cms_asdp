
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('ifcs/corporate/action_edit_contract', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Kode Corporate<span class="wajib">*</span></label>
                            <input type="text" name="corporate_code" class="form-control" value="<?php echo $detail->corporate_code ?>"  placeholder="Kode Corporate" required readonly>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Corporate <span class="wajib">*</span></label>
                            <input type="text" name="corporate" class="form-control" value="<?php echo $data_corporate->corporate_name ?>" placeholder="Nama Corporate" required readonly>
                        </div>
                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Nomer Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="contract_number" class="form-control "  placeholder="Kode Kontrak" required value="<?php echo $detail->agreement_number ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Awal Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="start_date" class="form-control date"  placeholder="YYYY-MM-DD" required id="start_date" value="<?php echo $detail->start_date ?>"  readonly>
                        </div>

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Akhir Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="end_date2" class="form-control date"  placeholder="YYYY-MM-DD" required id="end_date2" value="<?php echo $detail->end_date ?>" disabled>

                            <input type="hidden" name="end_date" required id="end_date" value="<?php echo $detail->end_date ?>" >

                            <input type="hidden" name="id" required  value="<?php echo $this->enc->encode($detail->id); ?>" >
                            <input type="hidden" name="last_agreement_code" required  value="<?php echo $detail->last_agreement_code; ?>" >
                            <input type="hidden" name="agreement_code" required  value="<?php echo $detail->agreement_code; ?>" >
                        </div> 

<!--                         <div class="col-sm-6 form-group">
                            <label>AKtifkan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="activation" required>
                                <option value="">Pilih</option>
                                <option value="1" <?php echo $detail->is_active==1?"selected":"" ?> >IYA</option>
                                <option value="0" <?php echo $detail->is_active==0?"selected":"" ?> >TIDAK</option>
                            </select>
                        </div>  -->

                        <div class="col-sm-6 form-group">
                            <label>Urutan Kontrak <span class="wajib">*</span></label>
                            <input type="text" name="order_number" class="form-control "  placeholder="Urutan Kontrak" required id="order_number" onkeypress="return isNumberKey(event)" value="<?php echo $detail->order_number ?>" >
                        </div>                                                 


                        <div class="col-sm-12 "></div>

                        <div class="col-sm-12 form-group">
                            <label>File PDF</label><br>
                            <div>
                                <?php echo $filename ?>
                            </div>
                            
                        </div>                             

                        <div class="col-sm-12">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <label>Pilih File PDF</label>
                                <div class="input-group ">

                                    <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                        <span class="fileinput-filename"> </span>
                                    </div>
                                    <span class="input-group-addon btn green-jungle btn-file">
                                        <span class="fileinput-new"> Pilih File </span>
                                        <span class="fileinput-exists"> Pilih File</span>
                                        <input type="hidden"><input type="file" name="input_gambar" > </span>
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

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        $("#start_date").change(function(){
             var x = 12; //or whatever offset
             var CurrentDate = new Date($(this).val());
             myDate=CurrentDate.setMonth(CurrentDate.getMonth() + x);
             var newDate = new Date(myDate);

             newDate.setDate(newDate.getDate() - 1);

             // console.log(convert(newDate));

             $("[name='end_date']").val(convert(newDate))
             $("[name='end_date2']").val(convert(newDate));
        })
        function postData2(url,data,y){

            form = $('form')[0];
            formData = new FormData(form);

            $.ajax({
                url         : url,
                data        :formData,
                type        : 'POST',
                // enctype: 'multipart/form-data',
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
                        if(y){
                            $('#grid').treegrid('reload');
                            // ambil_data();
                        }
                        else
                        {
                            $('#dataTables').DataTable().ajax.reload( null, false );
                            // ambil_data();

                        }
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