<style type="text/css">
    .wajib{ color:red; }
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelabuhan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pelabuhan" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Nama Kota <span class="wajib">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="Nama Kota" required>
                        </div>
                        <div class="col-sm-12 "></div>
                        <div class="col-sm-6 form-group">
                            <label>Profit Center<span class="wajib">*</span></label>
                            <input type="text" name="profit_center" class="form-control" placeholder="Kode Profit Center" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Maximum Berat<span class="wajib">*</span></label>
                            <input type="text" name="weight_limit" class="form-control" placeholder="MAX Berat" required onkeypress="return isNumberKey(event)" >
                        </div>
                        <div class="col-sm-12 "></div>                        
                        <div class="col-sm-6 form-group">
                            <!--  event tiket reguler bisa di eks  -->
                            <label>Event Khusus<span class="wajib">*</span></label>
                            <select class="form-control select2" name="cross_class" required>
                                <option value="" >Pilih</option>
                                <option value="t" >IYA</option>
                                <option value="f" >TIDAK</option>
                            </select>
                        </div>


                        <div class="col-sm-6 form-group">
                            <!--  event tiket reguler bisa di eks  -->
                            <label>IFCS<span class="wajib">*</span></label>
                            <?php echo form_dropdown("ifcs",$ifcs,""," class='form-control select2' required ") ?>
                        </div>

                        <div class="col-sm-6 form-group">
                            <!--  jam waktu lokal setempat  -->
                            <label>Zona Waktu<span class="wajib">*</span></label>
                            <select class="form-control select2" name="timeZone" required>
                                <option value="" >Pilih</option>
                                <option value="WIB" >WIB</option>
                                <option value="WIT" >WIT</option>
                                <option value="WITA" >WITA</option>
                            </select>
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
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>
