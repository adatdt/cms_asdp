 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/assignment_user_pos/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port" id="port">
                                    <option value="">Pilih</option>
                                <?php foreach($port as $port ) { ?>
                                    <option value="<?php echo $this->enc->encode($port->id) ?>"><?php echo $port->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Regu</label>
                            <select class="form-control select2" required name="team" id="team">
                                    <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Penugasan</label>
                            <div class="input-group">
                                <input class="form-control  date" id="date" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>" required name="assignment_date">
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div> 
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-4 form-group">
                            <label>Shift</label>
                            <select class="form-control select2" required name="shift" id="shift">
                                    <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->shift_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>SPV</label>
                            <select class="form-control select2" required name="spv" id="spv">
                                    <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Costomer service</label>
                            <select class="form-control select2" required name="cs" id="cs">
                                    <option value="">Pilih</option>
                            </select>
                        </div>


<!--                         <div class="col col-md-4"><a class=" btn btn-sm btn-warning" id="add_user" >Tambah User</a></div>

                        <div class="col col-md-4"></div> -->

                        <div class="col-sm-12" > 
                            <div style="padding:0px; " >
                                <div class="add_field_button btn btn-warning pull-right btn-sm" >Tambah User</div>
                            </div>
                            <div style="height:10px"></div>
                            <label>Tambah User</label>                           
                            <select name="user[0]" class="form-control select2" required id="username">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-12 input_fields_wrap"></div>

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

    function get_team()
    {
        $("#port").on("change",function(){
            $.ajax({
                type:"post",
                url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_team",
                data:"port="+$("#port").val(),
                dataType:"json",
                success:function(x){

                    var html="<option value=''>Pilih</option>";
                    for(var i=0;i<x.length;i++)
                    {
                        html +="<option value='"+x[i].team_code+"''>"+x[i].team_name+"</option>";

                    }
                    
                    $("#team").html(html);
                }
            });
        });
    }

    function get_user()
    {
        $("#port").on("change",function(){
            $.ajax({
                type:"post",
                url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_user",
                data:"port="+$("#port").val(),
                dataType:"json",
                success:function(x){

                    var html="<option value=''>Pilih</option>";
                    
                    for(var i=0;i<x.length;i++)
                    {
                        html +="<option value='"+x[i].id+"''>"+x[i].full_name+"</option>";

                    }
                    
                    $("#username").html(html);

                    // remove field add user
                    $(".remove_user").remove();

                }
            });
        });
    }

    function get_spv()
    {
        $("#port").on("change",function(){
            $.ajax({
                type:"post",
                url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_spv",
                data:"port="+$("#port").val(),
                dataType:"json",
                success:function(x){

                    var html="<option value=''>Pilih</option>";
                    
                    for(var i=0;i<x.length;i++)
                    {
                        html +="<option value='"+x[i].id+"''>"+x[i].full_name+"</option>";

                    }
                    
                    $("#spv").html(html);

                }
            });
        });
    }

    function get_cs()
    {
        $("#port").on("change",function(){
            $.ajax({
                type:"post",
                url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_cs",
                data:"port="+$("#port").val(),
                dataType:"json",
                success:function(x){

                    var html="<option value=''>Pilih</option>";
                    
                    for(var i=0;i<x.length;i++)
                    {
                        html +="<option value='"+x[i].id+"''>"+x[i].full_name+"</option>";

                    }
                    
                    $("#cs").html(html);

                }
            });
        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var data_option ="<option value=''>Pilih</option> <?php foreach($user as $key=>$value ) { ?>"+
                         "<option value='<?php echo $this->enc->encode($value->id); ?>'><?php echo $value->username; ?></option> <?php } ?>";
        var data_option2="<option value=''>Pilih</option>";

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                
                $(wrapper).append('<div class="remove_user"><a href="#" class="remove_field pull-right">Hapus</a><select id="username'+x+'" name="user['+x+']" class="form-control select2" required>'+data_option2+'</select></div>'); 
                get_user2('#username'+x);
                x++; //text box increment

                // $('.select2').select2();
            }

        });
        
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        });


        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        get_team();
        get_user();
        get_spv();
        get_cs();

        function get_user2(y)
        {           
            $.ajax({
                type:"post",
                url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_user",
                data:"port="+$("#port").val(),
                dataType:"json",
                success:function(x){

                    var html="<option value=''>Pilih</option>";
                    for(var i=0;i<x.length;i++)
                    {
                        html +="<option value='"+x[i].id+"''>"+x[i].full_name+"</option>";

                    }
                    
                    $(y).html(html);

                }
            });



        }

    })
</script>