 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/assignment_user_pos/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Kode Penugasan</label>
                            <input type="text" class="form-control" value="<?php echo $detail2->assignment_code; ?>" disabled >
                            <input type="hidden"  value="<?php echo $this->enc->encode($detail2->assignment_code); ?>" name="assignment_code">
                            <input type="hidden"  value="<?php echo $detail2->assignment_date; ?>" name="assignment_date">
                        </div>
<!--                         <div class="col-sm-12">
                            
                        </div> -->

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" disabled  id="port" >
                                    <option value="">Pilih</option>
                                <?php foreach($port as $port ) { ?>
                                    <option value="<?php echo $this->enc->encode($port->id) ?>" <?php echo $port->id==$detail2->port_id?"selected":""; ?>><?php echo $port->name; ?></option>
                                <?php } ?>
                            </select>

                            <input type="hidden" value="<?php echo $this->enc->encode($detail2->port_id) ?>" name="port" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift</label>
                            <select class="form-control select2" disabled  id="shift" >
                                    <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail2->shift_id?"selected":""; ?>><?php echo $value->shift_name; ?></option>
                                <?php } ?>
                            </select>


                            <input type="hidden" value="<?php echo $this->enc->encode($detail2->port_id) ?>" name="port" required>

                            <input type="hidden" value="<?php echo $this->enc->encode($detail2->shift_id) ?>" name="shift" required>
                        </div>


                        <div class="col-sm-4 form-group">

                            <label>Regu</label>
                            <select class="form-control select2" disabled >
                                    <option value="">Pilih</option>
                                <?php foreach($team as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->team_code);?>"  <?php echo $value->team_code==$detail2->team_code?"selected":""; ?>><?php echo $value->team_name; ?></option>
                                <?php } ?>
                            </select>

                            <input type="hidden" value="<?php echo $this->enc->encode($detail2->team_code) ?>" required name="team" id="team">

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Penugasan</label>
                            <input class="form-control  date" id="date" placeholder="yyyy-mm-dd" value="<?php echo $detail2->assignment_date; ?>" required name="assignment_date" disabled>
                            <!-- <div class="input-group-addon"><i class="icon-calendar"></i></div> -->
                            
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>SPV</label>
                            <input class="form-control" id="spv_name" placeholder="yyyy-mm-dd" value="<?php echo $detail2->spv_name; ?>" required name="spv_name" disabled>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>CS</label>
                            <input class="form-control" id="spv_name" placeholder="yyyy-mm-dd" value="<?php echo $detail2->cs_name; ?>" required name="spv_name" disabled>
                        </div>

                        <div class="col-sm-12  input_fields_wrap" > 

                            <div class="add_field_button btn btn-warning pull-right btn-sm" >Tambah User</div>
                            <div style="height:10px"></div>
                            
                            <label>User</label>
                            <select name="user[0]" class="form-control" >
                                <option value="">Pilih</option>
                                <?php foreach ($user as $key=>$value) {?>
                                <option value="<?php echo $this->enc->encode($value->id)?>"><?php echo $value->username?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 " ><hr></div>

                        <div class="col-sm-12 ">
                            <label>Daftar User Ditugaskan</label>
                            <div class="input-group">
                                <div class="icheck-inline">
                                    <?php foreach ($detail as $key=>$value ) { ?> 
                                    <label>
                                        <input type="checkbox" class="allow" name='list_user[<?php echo $key ?>]' data-checkbox="icheckbox_flat-grey" checked value="<?php echo $this->enc->encode($value->user_id); ?>">
                                        <?php echo $value->full_name; ?> &nbsp;&nbsp; 
                                    </label>
                                    <?php } ?> 
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var data_option ="<option value=''>Pilih</option> <?php foreach($user as $key=>$value ) { ?>"+
                         "<option value='<?php echo $this->enc->encode($value->id); ?>'><?php echo $value->username; ?></option> <?php } ?>";

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                $(wrapper).append('<div><a href="#" class="remove_field pull-right">Hapus</a><select name="user['+x+']" class="form-control" >'+data_option+'</select></div>'); //add input box

                x++; //text box increment
            }
        });
        
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        });
    })
</script>