<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/ship/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nama Kapal <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Kapal" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Kapasitas Penumpang <span class="wajib">*</span></label>
                            <input type="text" name="passenger_cap" class="form-control angka" placeholder="Kapasitas Penumpang" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Kapasitas Kendaraaan <span class="wajib">*</span></label>
                            <input type="text" name="vehicle_cap" class="form-control angka" placeholder="Kapasitas Kendaraaan" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_class" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_class as $key=>$value) {?>

                                <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name )?></option>
                                <?php } ?>
                            </select> 

                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>GRT <span class="wajib">*</span></label>
                            <input type="number" onkeypress="return isNumberKey(event)" name="grt" class="form-control" placeholder="Kapasitas Penumpang" required >
                        </div>

                        <div class="col-sm-6">
                            <label>Perusahaan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_company" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_company as $key=>$value) {?>

                                <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo strtoupper($value->name )?></option>
                                <?php } ?>
                            </select> 

                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Kode Kapal</label>
                            <input type="text" name="ship_code" class="form-control" placeholder="Kode Kapal" >
                        </div>

                    </div>
                </div>
<!--                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6">
                            <label>Kode Kapal</label>
                            <input type="text" name="code" class="form-control angka" placeholder="Kode Kapal" required>
                        </div>
                    </div>
                </div> -->
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.angka').keyup(function(e){
            this.value = formatRupiah(this.value);
        })
        validateForm('#ff',function(url,data){
            data.passenger_cap = removeRupiah(data.passenger_cap);
            data.vehicle_cap = removeRupiah(data.vehicle_cap);
            postData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

    })


</script>