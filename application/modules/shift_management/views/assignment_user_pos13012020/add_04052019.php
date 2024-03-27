 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <script src="<?php echo base_url() ?>assets/js/jquery-repeater.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery-repeater.js"></script>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/assignment_user_pos/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-3 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" name="port" required id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>
                        </div>


                        <div class="col-md-3 form-group">
                            <label>Pelabuhan2</label>
                            <select class="form-control select2" name="port2" required id="tags" multiple="multiple">
                                <?php foreach($port as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>



                        <div class="col-md-3 form-group">
                            <label>Tanggal Penugasan </label>
                            <div class="input-group">
                                <input type="text" name="date"  class="form-control date" id="date" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d')?>" required>
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div>
                        </div>


                        <div class="col-md-3 form-group">
                            <label>SPV POS</label>
                            <select  name="spv" id="spv" class="form-control select2" placeholder="spv" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label>Regu</label>
                            <select  name="team" id="team" class="form-control select2" placeholder="Team" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-md-3 form-group">
                            <label>Shift</label>
                            <input type="text" name="shift" id="shift" class="form-control" placeholder="shift" required>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-md-3 form-group">
                            
                            <button class="btn btn-md btn-warning add_field_button pull-right"><i class="fa fa-plus"></i></button>
                            <br>
                            <label>Tambah User</label>
<!--                             <input type="text" name="username[0]" id="username" class="form-control" placeholder="Cara Pembayaran" required>
 -->
                            <select required name='username[0]' class=' form-control select2 user' id="user">
                                <option value=''>Pilih</option>
                            </select>
                            
                        </div>

                        <div class="input_fields_wrap"></div>

                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <button class="btn btn-md btn-warning add_field_button_cs pull-right"><i class="fa fa-plus"></i></button>
                            <br>
                            <label>Tambah User CS</label>
                            <select required name='usernamecs[0]' class=' form-control select2 usercs'>
                                <option value=''>Pilih</option>
                            </select>
                            
                        </div>

                        <div class="input_fields_wrap2"></div>

                    </div>

                </div>
            </div>


      
            <?php echo createBtnForm('Simpan'); ?>
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
                var userHtml="<option value=''>Pilih</option>";
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
                $(".user").html(userHtml);
                $(".usercs").html(usercsHtml);
                // console.log(usercsHtml);

            }
        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        var max_fields      = 500; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var wrapper2         = $(".input_fields_wrap2"); //Fields wrapper
        var add_button2      = $(".add_field_button_cs"); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                var idparam="username"+x
                $(wrapper).append("<div class='col-md-3 form-group' id='"+idparam+"'> <a class='remove_field pull-right btn btn-danger'  onClick='action_remove("+'"'+idparam+'"'+")'><i class='fa fa-trash'></i></a><br><label>Tambah User</label><select required name='username["+x+"]' class=' form-control select2 user' id='user"+x+"' ><option value=''>Pilih</option></select></div>");

                $('.select2:not(.normal)').each(function () {
                    $(this).select2({
                        dropdownParent: $(this).parent()
                    });
                });

                get_user("user"+x);

                x++; 


            }

        });


        $(add_button2).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                var idparam="usernamecs"+x
                $(wrapper2).append("<div class='col-md-3 form-group' id='"+idparam+"'> <a class='remove_field pull-right btn btn-danger'  onClick='action_remove("+'"'+idparam+'"'+")'><i class='fa fa-trash'></i></a><br><label>Tambah User</label><select required name='usernamecs["+x+"]' class=' form-control select2 usercs' id='usercs"+x+"'><option value=''>Pilih</option></select></div>");

                $('.select2:not(.normal)').each(function () {
                    $(this).select2({
                        dropdownParent: $(this).parent()
                    });
                });

                get_usercs("usercs"+x);

                x++; 

            }

        });

    

      $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
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

        // $("#port2").select2();
        // setTimeout(function(){
        //     $("#port2").val(['1','2','3','4']);
        //     $('#port2').trigger('change');
        // },100)


        $('#tags').select2({
            tags: true,
            data: ["Clare","Cork","South Dublin"],
            tokenSeparators: [','], 
            placeholder: "Add your tags here",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: true, 
            closeOnSelect: false
        });


    })

</script>