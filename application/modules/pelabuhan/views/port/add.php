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

                        <div class="col-sm-12 "></div>
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

                        <div class="col-sm-6 form-group">
                            <label>Urutan <span class="wajib">*</span></label>
                            <input type="text" name="ordering" class="form-control" placeholder="Urutan" required onkeypress="return isNumberKey(event)" >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Username Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="username_siwasops" class="form-control" placeholder="Username Siwasops" required >
                        </div> 
                        <div class="col-sm-6 form-group">
                            <label>Password Siwasops <span class="wajib">*</span></label>
                            <input type="password" name="password_siwasops" class="form-control" placeholder="Password Siwasops" required >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>URL Login Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="url_login_siwasops" class="form-control" placeholder="URL Login Siwasops" required >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>URL Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="url_siwasops" class="form-control" placeholder="URL Siwasops" required >
                        </div>    

                        <div class="col-sm-12 "></div>  
<!--                         <div class="col-sm-6 form-group">
                            <label>ID BMKG </label>
                            <input type="text" name="port_id_bmkg" class="form-control" placeholder="ID BMKG">
                        </div>                         -->

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
            data['username_siwasops'] = btoa($('[name=username_siwasops]').val());
            data['password_siwasops'] = btoa($('[name=password_siwasops]').val());
            data['url_login_siwasops'] = btoa($('[name=url_login_siwasops]').val());
            data['url_siwasops'] = btoa($('[name=url_siwasops]').val());
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>
