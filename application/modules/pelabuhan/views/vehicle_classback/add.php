<style type="text/css">
    .wajib{color:red;}
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/vehicle_class/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nama Golongan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Golongan" required>
                        </div>

                        <div class="col-sm-6 ">
                            <label>Tipe Kendaraan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="tipe">
                                <option value="">Pilih</option>
                                <?php foreach($class_type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?> 
                            </select>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 ">
                            <label>Grup Kendaraan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("groupVehicle",$groupVehicle,""," class='form-control select2' required name='groupVehicle' id='groupVehicle' ") ?>
                        </div>

                        <div class="col-sm-6 ">
                            <label>Tipe Grup Kendaraan <span class="wajib">*</span></label>
                            <?php echo form_dropdown("groupVehicleType",$groupVehicleType,""," class='form-control select2' required name='groupVehicleType' id='groupVehicleType' ") ?>
                        </div>

                    </div>
                </div>                

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Panjang Minimum <span class="wajib">*</span></label>
                            <input type="text" name="min" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Minimum" required>
                        </div>

                        <div class="col-sm-6">
                            <label>Panjang Maksimal <span class="wajib">*</span></label>
                            <input type="text" name="max" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Maximum" required>
                        </div>
                    </div>
                </div>    

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Kapasitas Maximum Penumpang <span class="wajib">*</span></label>
                            <input type="text" name="capacity_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Maximum Penumpang" required>
                        </div>

                        <div class="col-sm-6">
                            <label>Berat Default <span class="wajib">*</span></label>
                            <input type="text" name="weight_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Berat Default" required>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Panjang Linemeter <span class="wajib" style="font-style: italic; font-size: 10px">(Gunakan ( <b>.</b> ) sebagai koma)</span> <span class="wajib"> *</span>  </label>
                            <input type="text" name="length_lm" id="length_lm" class="form-control " onkeypress="return myData.numSparator(event)" placeholder="Panjang Linemeter" required autocomplete="off">
                        </div>

                        <div class="col-sm-6">
                            <label>Lebar Linemeter <span class="wajib" style="font-style: italic; font-size: 10px">(Gunakan ( <b>.</b> ) sebagai koma)</span> <span class="wajib"> *</span></label>
                            <input type="text" name="wide_lm" id="wide_lm" class="form-control " onkeypress="return myData.numSparator(event)" placeholder="Berat Default" required autocomplete="off">
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Luas Linemeter ( Panjang * Lebar )<span class="wajib">*</span></label>
                            <input type="text" name="total_lm" id="total_lm" class="form-control "  placeholder="Luas Linemeter" required readonly onkeypress="return myData.numSparator(event)">
                        </div>

                        <div class="col-sm-6">
                            <label>Deskripsi <span class="wajib">*</span></label>
                            <textarea class="form-control" name="description" required placeholder="Deskripsi"></textarea>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="fileinput fileinput-new col-sm-6" data-provides="fileinput">
                            <label>Pilih File jpg <span class="wajib">*</span></label>
                            <div class="input-group ">

                                <div class="form-control uneditable-input  input-fix" data-trigger="fileinput">
                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                    <span class="fileinput-filename"> </span>
                                </div>
                                <span class="input-group-addon btn green-jungle btn-file ">
                                    <span class="fileinput-new"> Pilih File </span>
                                    <span class="fileinput-exists"> Pilih File</span>
                                    <input type="hidden"><input type="file" name="input_gambar" > </span>
                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                            </div>
                        
                        </div>     



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
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

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
                    if(json.code == 1)
                    {
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');

                        $('#dataTables').DataTable().ajax.reload( null, false );

                    }
                    else
                    {
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

        $("#wide_lm").on("keyup mouseup",function(){
            
            var wideLm=$(this).val();
            var lengthLm=$("#length_lm").val();

            var data={wideLm:wideLm, lengthLm:lengthLm}
            
            var getData=myData.countLinemeter(data);

            $("#total_lm").val(getData);
        })        

        $("#length_lm").on("keyup mouseup",function(){
            
            var wideLm=$("#wide_lm").val();
            var lengthLm=$(this).val();

            var data={wideLm:wideLm, lengthLm:lengthLm}
            
            var getData=myData.countLinemeter(data);

            $("#total_lm").val(getData);
        })                
    })
</script>