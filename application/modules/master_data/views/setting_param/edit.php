 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

 <style type="text/css">
     .wajib {
         color: red
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
 
 <div class="col-md-6 col-md-offset-3">
     <div class="portlet box blue" id="box">
         <?php echo headerForm($title) ?>
         <div class="portlet-body">
             <?php echo form_open('master_data/setting_param/action_edit', 'id="ff" autocomplete="on"'); ?>
             <div class="box-body">
                 <div class="form-group">
                     <div class="row">

                         <div class="col-sm-6 form-group">
                             <label>Nama <span class="wajib">*</span></label>
                             <input type="text" name="name" class="form-control" placeholder="Nama Param" required value="<?php echo $detail->param_name; ?>" readonly>
                             <input type="hidden" name="id" value="<?php echo $this->enc->encode($detail->param_id) ?>">
                         </div>
                         <div class="col-sm-6 form-group">
                             <label>Value Param <span class="wajib">*</span></label>
                             <?php if (in_array($detail->value_type, array('boolean', 'Boolean')) )
                                        { 
                                            $type = "hidden";
                                            $toggle = "";
                                            $max = "";
                                            $min = '';
                                        }
                                        else 
                                        {
                                            $type = "text";
                                            $toggle = "display:none;";
                                            $max="";
                                            $min = '';
                                            if ($detail->param_name == "max_file_size_refund"){
                                                $type  = "number";
                                                $max = 'max = '.$getMaxSize;
                                            }
                                            if ($detail->param_name == "max_file_resize_refund"){
                                                $type  = "number";
                                                $max = 'max = '.$getMaxResize;
                                            }
                                            if ($detail->param_name == "limit_same_char_nik"){
                                                $type  = "number";
                                                $max = 'max = "17"';
                                                $min = 'min = "3"';
                                            }
                                            if ($detail->param_name == "booking_expired"){
                                                $type  = "number";
                                                $min = 'min = "15"';
                                            }
                                        }
                                            ?>
                             <input type="<?php echo $type;?>" name="value_param" class="form-control" placeholder="Value Param" <?php echo $max; echo $min; ?> required value="<?php echo htmlspecialchars($detail->param_value, ENT_QUOTES, 'UTF-8'); ?>">
                             <label class="switch" style="<?php echo $toggle;?>">
                                <input type="hidden" name="checkbox_param" value="0">
                                <input type="checkbox" name="checkbox_param" id="togBtn" value="1" <?php if (in_array(htmlspecialchars($detail->param_value, ENT_QUOTES, 'UTF-8'), (array('1', 'true')))) { echo "checked";}  ?>><div class="slidertes round"></div>
                            </label>
                         </div>

                         <div class="col-sm-6 form-group">
                             <label>Tipe Param <span class="wajib">*</span></label>
                             <input type="text" name="tipe_param" class="form-control" placeholder="Tipe Param" required value="<?php echo $detail->type; ?>">
                         </div>

                         <div class="col-sm-6 form-group">
                             <label>Tipe Value <span class="wajib">*</span></label>
                             <?php 
                                // custom saat param verification_time , maka tipe value tidak bisa di edit
                                $customTipeValue=$detail->param_name=="verification_time"?"readonly":"";
                             ?>

                             <input type="text" name="tipe_value" class="form-control" placeholder="Tipe Value" <?= $customTipeValue ?> required value="<?php echo $detail->value_type; ?>">
                         </div>

                         <div class="col-sm-6 form-group">
                             <label>Info <span class="wajib">*</span></label>
                             <input type="text" name="info" class="form-control" placeholder="Info" required value="<?php echo $detail->info; ?>">
                         </div>
<!-- 
                         <div class="col-sm-6 form-group">
                            <label>Kategori <span class="wajib">*</span></label>
                                      <select id="category" class="form-control js-data-example-ajax select2" dir="" name="category">
                                        <option value="<?php echo $detail->category_name; ?>"><?php echo $detail->category_name == null ? 'lainnya' : $detail->category_name ?></option>
                                        <?php foreach($kategori as $key=>$value ) { ?>
                                            <option value="<?php echo $value->category_name; ?>"><?php echo $value->category_name; ?></option>
                                        <?php } ?>
                                        <option value="lainnya">lainnya</option>
                                    </select>
                        </div> -->

                     </div>
                 </div>
             </div>
             <?php echo createBtnForm('Edit') ?>
             <?php echo form_close(); ?>
         </div>
     </div>
 </div>

 <script type="text/javascript">
     $(document).ready(function() {
         validateForm('#ff', function(url, data) {
             data['info'] = replaceStyle(data.info);
             data['tipe_value'] = replaceStyle(data.tipe_value);
             data['tipe_param'] = replaceStyle(data.tipe_param);
             data['name'] = replaceStyle(data.name);
             data['value_param'] = replaceStyle(data.value_param);  
             postData(url, data);
         });

         $('.select2:not(.normal)').each(function() {
             $(this).select2({
                 dropdownParent: $(this).parent()
             });
         });
     })
 </script>