<style type="text/css">
    .wajib{ color:red; }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
<!--                         <div class="col-sm-6 form-group">
                            <label>KODE <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Kode Pelabuhan" required value="<?php echo $row->port_code ?>" disabled>
                        </div> -->
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelabuhan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pelabuhan" required value="<?php echo $row->name ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Nama Kota <span class="wajib">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="Nama Kota" required value="<?php echo $row->city ?>">
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Profit Center<span class="wajib">*</span></label>
                            <input type="text" name="profit_center" class="form-control" placeholder="Kode Profit Center" value="<?php echo $row->profit_center ?>" required>
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Maximum Berat<span class="wajib">*</span></label>
                            <input type="text" name="weight_limit" class="form-control" placeholder="MAX Berat" required onkeypress="return isNumberKey(event)" value="<?php echo $row->weight_limit; ?>" >
                        </div>
                        <div class="col-sm-12"></div>                        
                        <div class="col-sm-6 form-group">
                            <label>Event Khusus<span class="wajib">*</span></label>
                            <select class="form-control select2" name="cross_class" required>
                                <option value="" <?php echo $row->cross_class==''?"selected":"" ?> >Pilih</option>
                                <option value="t" <?php echo $row->cross_class=='t'?"selected":"" ?> >IYA</option>
                                <option value="f" <?php echo $row->cross_class=='f'?"selected":"" ?> >TIDAK</option>
                            </select>
                        </div>

                        
                        <div class="col-sm-6 form-group">
                            <label>IFCS<span class="wajib">*</span></label>
                            <?php echo form_dropdown("ifcs",$ifcs,$ifcs_selected," class='form-control select2' required ") ?>
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Zona Waktu<span class="wajib">*</span></label>
                            <?php echo form_dropdown("timeZone",$timeZone,$timeZone_selected," class='form-control select2' required ") ?>
                        </div>    

                        <div class="col-sm-6 form-group">
                            <label>Urutan <span class="wajib">*</span></label>
                            <input type="text" name="ordering" class="form-control" placeholder="Urutan" required onkeypress="return isNumberKey(event)" value="<?= $row->order ?>" >
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label>Username Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="username_siwasops" class="form-control" placeholder="Username Siwasops" value="<?php echo $row->username_siwasops ?>" required >
                        </div> 
                        <div class="col-sm-6 form-group">
                            <label>Password Siwasops <span class="wajib">*</span></label>
                            <input type="password" name="password_siwasops" class="form-control" placeholder="Password Siwasops" value="<?php echo $row->password_siwasops ?>" required >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>URL Login Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="url_login_siwasops" class="form-control" placeholder="URL Login Siwasops" value="<?php echo $row->url_login_siwasops ?>" required >
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>URL Siwasops <span class="wajib">*</span></label>
                            <input type="text" name="url_siwasops" class="form-control" placeholder="URL Siwasops" value="<?php echo $row->url_siwasops ?>" required >
                        </div>

                        <div class="col-sm-12 "></div>  
                        <!-- <div class="col-sm-6 form-group">
                            <label>ID BMKG </label>
                            <input type="text" name="port_id_bmkg" class="form-control" placeholder="ID BMKG" value="<?php // echo $row->port_id_bmkg ?>" >
                        </div>                                                                                                                     -->

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
