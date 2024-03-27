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
                        <div class="col-sm-4">
                            <label>Nama Golongan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Golongan" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Kendaraan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="tipe">
                                <option value="">Pilih</option>
                                <?php foreach($class_type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?> 
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label>Panjang Minimum <span class="wajib">*</span></label>
                            <input type="text" name="min" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Minimum" required>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Panjang Maksimal <span class="wajib">*</span></label>
                            <input type="text" name="max" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Maximum" required>
                        </div>

                        <div class="col-sm-4">
                            <label>Kapasitas Maximum Penumpang <span class="wajib">*</span></label>
                            <input type="text" name="capacity_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Maximum Penumpang" required>
                        </div>

                        <div class="col-sm-4">
                            <label>Berat Default <span class="wajib">*</span></label>
                            <input type="text" name="weight_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Berat Default" required>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Deskripsi <span class="wajib">*</span></label>
                            <textarea class="form-control" name="description" required placeholder="Deskripsi"></textarea>
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
        // $('.angka').keyup(function(e){
        //     this.value = formatRupiah(this.value);
        // })
        validateForm('#ff',function(url,data){
            // data.adult = removeRupiah(data.adult);
            // data.child = removeRupiah(data.child);
            // data.infant = removeRupiah(data.infant);
            // data.min = removeRupiah(data.min);
            // data.max = removeRupiah(data.max);
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>