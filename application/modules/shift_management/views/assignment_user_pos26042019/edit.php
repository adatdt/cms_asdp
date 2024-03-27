 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 
<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/assignment_user_pos/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-4 form-group">
                            <label>Kode Penugasan <span class="wajib">*</span></label>
                            <input type="text" class="form-control" required disabled value="<?php echo $detail2->assignment_code ?>">
                            <input type="hidden" required  value="<?php echo $this->enc->encode($detail2->assignment_code) ?>" name='assignment'>

                        </div>

                        <div class="col-md-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="port" required id="port" disabled>
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail2->port_id?"selected":""; ?>><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>

                        </div>


                        <div class="col-md-4 form-group">
                            <label>Tanggal Penugasan <span class="wajib">*</span></label>
                            <div class="input-group">
                                <input type="text" name="date"  class="form-control date" id="date" disabled placeholder="YYYY-MM-DD" value="<?php echo $detail2->assignment_date; ?>" required>
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-md-4 form-group">
                            <label>SPV POS <span class="wajib">*</span></label>
                            <select  name="spv" id="spv" class="form-control select2" placeholder="spv" required disabled>
                                <option value="">Pilih</option>
                                <?php foreach($userspv as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"
                                    <?php echo $value->id==$spv->user_id?"selected":""; ?>><?php echo $value->full_name ?></option>
                                <?php }?>
                            </select>

                            <input name="userspv" type="hidden" value="<?php echo $this->enc->encode($spv->user_id); ?>">

                        </div>

                        <div class="col-md-4 form-group">
                            <label>Regu <span class="wajib">*</span></label>
                            <select  name="team" id="team" class="form-control select2" placeholder="Team" required disabled>
                                <option value="">Pilih</option>
                                <?php foreach($regu as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->team_code) ?>"
                                     <?php echo $value->team_code==$detail2->team_code?"selected":""; ?> ><?php echo $value->team_name ?></option>
                                <?php }?>
                            </select>
                        </div>


                        <div class="col-md-4 form-group">
                            <label>Shift <span class="wajib">*</span></label>
                            <select class="form-control select2" name="shift" required id="shift" required="" disabled>
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail2->shift_id?"selected":""; ?>><?php echo $value->shift_name ?></option>
                                <?php }?>                                
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-md-12 form-group">
                            
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

                        <div class="col-md-12 form-group">
                            
                            <label>Tambah User <span class="wajib">*</span></label>
                            <select  name='username' class=' form-control  user' id="user" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($user as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="username2" id="username2">
                            
                        </div>

                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-md-12 form-group">
                            
                            <label>Daftar User CS</label>
                            <div class="input-group">
                                <div class="icheck-inline">
                                    <?php foreach ($detail_cs as $key=>$value ) { ?> 
                                    <label>
                                        <input type="checkbox" class="allow" name='list_usercs[<?php echo $key ?>]' data-checkbox="icheckbox_flat-grey" checked value="<?php echo $this->enc->encode($value->user_id); ?>">
                                        <?php echo $value->full_name; ?> &nbsp;&nbsp; 
                                    </label>
                                    <?php } ?> 
                                    </label>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-12 form-group">
                            
                            <label>Tambah User CS <span class="wajib">*</span></label>
                            <select  name='usernamecs' class=' form-control  usercs' id="usercs" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($usercs as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="usernamecs2" id="usernamecs2">
                            
                        </div>

                    </div>

                </div>
            </div>


      
            <?php echo createBtnForm('Edit'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>

<script type="text/javascript">


    function action_remove(x)
    {
        $("#"+x).remove();   
    }


    function get_user(param)
    {
        $.ajax({
            data:"port="+$("#port").val(),
            type:"post",
            url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_user",
            dataType:"json",
            success:function(x)
            {
                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length; i++)
                {
                    html += "<option value='"+x[i].id+"'>"+x[i].full_name+"</option>"
                }

                $("#"+param).html(html);

                console.log("#"+param);
                console.log(html);       
            }
        });
    }


    function get_usercs(param)
    {
        $.ajax({
            data:"port="+$("#port").val(),
            type:"post",
            url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_usercs",
            dataType:"json",
            success:function(x)
            {
                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length; i++)
                {
                    html += "<option value='"+x[i].id+"'>"+x[i].full_name+"</option>"
                }

                $("#"+param).html(html);

                console.log("#"+param);
                console.log(html);       
            }
        });
    }

    function getData()
    {
        $.ajax({
            data:"port="+$("#port").val(),
            type:"post",
            url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_data",
            dataType:"json",
            success:function(x)
            {
                var dataSpv=x.spv;
                var dataTeam=x.regu;
                var dataUser=x.user;
                var dataUsercs=x.usercs;

                var spvHtml="<option value=''>Pilih</option>";
                var teamHtml="<option value=''>Pilih</option>";
                var userHtml="";
                var usercsHtml="<option value=''>Pilih</option>";

                for(var i=0; i<dataSpv.length; i++)
                {
                    spvHtml += "<option value='"+dataSpv[i].id+"'>"+dataSpv[i].full_name+"</option>"
                }

                for(var i=0; i<dataTeam.length; i++)
                {
                    teamHtml += "<option value='"+dataTeam[i].team_code+"'>"+dataTeam[i].team_name+"</option>"
                }

                for(var i=0; i<dataUser.length; i++)
                {
                    userHtml += "<option value='"+dataUser[i].id+"'>"+dataUser[i].full_name+"</option>"
                }

                for(var i=0; i<dataUsercs.length; i++)
                {
                    usercsHtml += "<option value='"+dataUsercs[i].id+"'>"+dataUsercs[i].full_name+"</option>"
                }

                $("#spv").html(spvHtml);
                $("#team").html(teamHtml);
                $("#user").html(userHtml);
                $("#usercs").html(usercsHtml);
                // console.log(usercsHtml);

            }
        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

      $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),

            });
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,

            // endDate: new Date(),
        });

        $("#port").on("change",function(){
            getData()
        });


        $('#usercs').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Input user CS",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#usercs").on("change",function(){

            $("#usernamecs2").val($("#usercs").val());         
        })


        $('#user').select2({
            tags: false,
            // data: ["Clare","Cork","South Dublin"],
            tokenSeparators: [','], 
            placeholder: "Input User POS",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#user").on("change",function(){

            $("#username2").val($("#user").val());         
        })


        $('#tes').select2({
            tags: false,
            // data: ["Clare","Cork","South Dublin"],
            tokenSeparators: [','], 
            placeholder: "tes",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: true, 
            closeOnSelect: false
        });


    })

</script>