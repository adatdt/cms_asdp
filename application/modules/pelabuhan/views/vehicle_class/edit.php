<style type="text/css">
    .wajib{color:red;}

    .zoom img{
        -webkit-transition-duration:0.5s;
        -moz-transition-duration:0.5s;
        -o-transition-duration:0.5s;
    }
    .zoom img:hover{-webkit-transform:scale(2.1);
        -moz-transform:scale(2.1);
        -o-transform:scale(2.1);
        -webkit-transition-duration:0.5s;
        -moz-transition-duration:0.5s;
        -o-transition-duration:0.5s;
        box-shadow:0px 0px 30px gray;
        -webkit-box-shadow:0px 0px 30px gray;
        -moz-box-shadow:0px 0px 30px gray;
    }    
     .switch {
        position: relative;
        display: block;
        width: 90px;
        height: 34px;
        }

        .switch input {display:none;}

        .slidertes {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /*#ca2222;*/
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px !important;
        }

        .slidertes:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50% !important;
        }

        input:checked + .slidertes {
        background-color: #3598dc;
        }

        input:focus + .slidertes {
        box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slidertes:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(55px);
        }

        /*------ ADDED CSS ---------*/
        .slidertes:after
        {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 12px;
        font-family: "Open Sans", sans-serif;
        }

        input:checked + .slidertes:after
        {  
        content:'ON';
        }
 </style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/vehicle_class/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">

                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nama Golongan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Golongan" required value="<?php echo $row->name ?>">

                            <input type="hidden" name="vehicle_class" placeholder="Nama Golongan" required value="<?php echo $id?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Kendaraan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="tipe">
                                <option value="">Pilih</option>
                                <?php foreach($class_type as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $row->type==$value->id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?> 
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label>Panjang Minimum <span class="wajib">*</span></label>
                            <input type="text" name="min" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Minimum" required value="<?php echo $row->min_length ?>">
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Panjang Maksimal <span class="wajib">*</span></label>
                            <input type="text" name="max" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Panjang Maximum" required value="<?php echo $row->max_length ?>">
                        </div>

                        <div class="col-sm-4">
                            <label>Kapasitas Maximum Penumpang <span class="wajib">*</span></label>
                            <input type="text" name="capacity_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Maximum Penumpang" required value="<?php echo $row->max_capacity ?>">
                        </div>

                        <div class="col-sm-4">
                            <label>Berat Default <span class="wajib">*</span></label>
                            <input type="text" name="weight_maximum" class="form-control " onkeypress="return isNumberKey(event)" placeholder="Berat Default"  value="<?php echo $row->default_weight ?>" required >
                        </div>


                    </div>
                </div>

                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12">
                            <label>Deskripsi <span class="wajib">*</span></label>
                            <textarea class="form-control" name="description" required placeholder="Deskripsi"><?php echo $row->description ?></textarea>
                        </div>


                    </div>
                </div>

                <div class=" form-group">
                        <label>Nomor Polisi </label>
                        
                        <label class="switch" >
                        <input type="hidden" name="checkbox_param" value="0">
                        <input type="checkbox" name="checkbox_param" id="togBtn" value="1" <?= $row->id_number_required=='t'?"checked":""; ?> ><div class="slidertes round"></div>
                    </label>
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
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () { 
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>