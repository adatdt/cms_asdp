<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/ship/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nama Pelabuhan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Kapal" required value="<?php echo $row->name ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>

                        <div class="col-sm-6">
                            <label>Kapasitas Kendaraaan <span class="wajib">*</span></label>
                            <input type="text" name="vehicle_cap" class="form-control angka" placeholder="Kapasitas Kendaraaan" required value="<?php echo idr_currency($row->vehicle_capacity) ?>">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Kapasitas Penumpang <span class="wajib">*</span></label>
                            <input type="text" name="passenger_cap" class="form-control angka" placeholder="Kapasitas Penumpang" required  value="<?php echo idr_currency($row->people_capacity) ?>">
                        </div>

                        <div class="col-sm-6">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_class" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_class as $key=>$value) {?>

                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $row->ship_class==$value->id?"selected":"";?> ><?php echo strtoupper($value->name )?></option>
                                <?php } ?>
                            </select> 

                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>GRT <span class="wajib">*</span></label>
                            <input type="number" onkeypress="return isNumberKey(event)" name="grt" class="form-control" placeholder="Kapasitas Penumpang" required  value="<?php echo $row->grt ?>">
                        </div>

                        <div class="col-sm-6">
                            <label>Perusahaan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="ship_company" required>
                                <option value="">Pilih</option>
                                <?php foreach ($ship_company as $key=>$value) {?>

                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $row->ship_company_id==$value->id?"selected":"";?> ><?php echo strtoupper($value->name )?></option>
                                <?php } ?>
                            </select> 

                        </div>
                    </div>
                </div>

            </div>
            <?php echo createBtnForm('Edit') ?>
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