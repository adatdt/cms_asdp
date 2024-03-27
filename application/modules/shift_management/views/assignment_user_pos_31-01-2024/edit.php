 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 
<style type="text/css">
    .wajib{
        color: red;
    }
    .scrolling{

        height: 200px;
        overflow-y: auto;
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

                        <div class="col-md-4 form-group">
                            <label>Tambah User <span class="wajib">*</span></label>
                            <select  name='username' class=' form-control  user' id="user" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($user as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="username2" id="username2">
                            
                        </div>

                        <div class="col-md-4 form-group">
                            
                            <label>Tambah User CS <span class="wajib">*</span></label>
                            <select  name='usernamecs' class=' form-control  usercs' id="usercs" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($usercs as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="usernamecs2" id="usernamecs2">
                            
                        </div>

                        <div class="col-md-4 form-group">
                            
                            <label>Tambah User PTC/ STC <span class="wajib">*</span></label>
                            <select  name='userptcstc' class=' form-control  usercs' id="userptcstc" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($userptcstc as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="userptcstc2" id="userptcstc2">
                            
                        </div>


                        <div class="col-md-12 form-group"></div>

                        <div class="col-md-4 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User Ditugaskan</h5>
                                            </div>
                                        </div>


                                        <div class="mt-list-container list-simple max-height collapse in scrolling" id="detailUsePos" aria-expanded="true" style=""></div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User CS</h5>
                                            </div>
                                        </div>

                                        <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailUseCs"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User PTC/ STC</h5>
                                            </div>
                                        </div>

                                        <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailUsePtcStc"></div>

                                    </div>
                                </div>
                            </div>
                        </div>    
                        
                        <div class="col-md-12 form-group"></div>

                        <div class="col-md-4 form-group">
                            
                            <label>Tambah User Verifikator <span class="wajib">*</span></label>
                            <select  name='uservertifikator' class=' form-control  uservertifikator' id="uservertifikator" multiple="multiple">
                                <option value="">Pilih</option>
                                <?php foreach($uservertifikator as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo $value->full_name ?></option>
                                <?php }?>   
                            </select>

                            <input type="hidden" required name="uservertifikator2" id="uservertifikator2">
                            
                        </div>
                        <div class="col-md-12 form-group"></div>   

                        <div class="col-md-4 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User Verifikator</h5>
                                            </div>
                                        </div>

                                        <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailUserVertifikator"></div>

                                    </div>
                                </div>
                            </div>
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
            data:{
                "port":$("#port").val(),
                "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
            },
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

    function getUserDetail()
    {
      $.ajax({
        data:
            {
                "assignment":$("[name='assignment']").val(),
                "port":$("[name='port']").val(),
                "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
            },
        type:"post",
        dataType:"json",
        url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_user_detail",
        beforeSend:function(){
            unBlockUiId('detailUsePos')
            unBlockUiId('detailUseCs')
            unBlockUiId('detailUsePtcStc')
            unBlockUiId('detailUserVertifikator')
        },        
        success:function(x)
        {
            // console.log(x);  
            $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                
                let csfrData = {};
                csfrData[x.csrfName] = x.tokenHash;
                
                $.ajaxSetup({
                        data: csfrData,
                });          

            if(x.code==1)
            {

                var html1="<option value=''>Pilih</option>";
                var html2="<option value=''>Pilih</option>";
                var html3="<option value=''>Pilih</option>";
                var html4="<option value=''>Pilih</option>";

                
                for(var i1=0; i1<x.user.length; i1++)
                {
                    html1 +="<option value='"+x.user[i1].id+"'>"+x.user[i1].full_name+"</option>";
                }

                for(var i2=0; i2<x.usercs.length; i2++)
                {
                    html2 +="<option value='"+x.usercs[i2].id+"'>"+x.usercs[i2].full_name+"</option>";
                }

                for(var i3=0; i3<x.userptcstc.length; i3++)
                {
                    html3 +="<option value='"+x.userptcstc[i3].id+"'>"+x.userptcstc[i3].full_name+"</option>";
                }

                for(var i4=0; i4<x.uservertifikator.length; i4++)
                {
                    html4 +="<option value='"+x.uservertifikator[i4].id+"'>"+x.uservertifikator[i4].full_name+"</option>";
                }                

                $("#user").html(html1);
                $("#usercs").html(html2);   
                $("#userptcstc").html(html3);
                $("#uservertifikator").html(html4);

                var userPos=x.user_pos;
                var userCs=x.user_cs;
                var userPtcstc=x.user_ptcstc;
                var userVertifikator=x.user_vertifikator;

                htmlUserPos="";
                htmlUserCs="";
                htmlUserPtcStc="";
                htmlUserVertifikator="";

                for(var i=0; i<userPos.length; i++ )
                {
                    htmlUserPos +="<ul class='sortable drag'>"+userPos[i].actions+"&nbsp;<i class='fa fa-check'></i>"+userPos[i].full_name+"</ul>"                    
                }

                for(var i=0; i<userCs.length; i++ )
                {
                    htmlUserCs +="<ul class='sortable drag'>"+userCs[i].actions+"&nbsp;<i class='fa fa-check'></i>"+userCs[i].full_name+"</ul>"                    
                }

                for(var i=0; i<userPtcstc.length; i++ )
                {
                    htmlUserPtcStc +="<ul class='sortable drag'>"+userPtcstc[i].actions+"&nbsp;<i class='fa fa-check'></i>"+userPtcstc[i].full_name+"</ul>"                    
                }
                for(var i=0; i<userVertifikator.length; i++ )
                {
                    htmlUserVertifikator +="<ul class='sortable drag'>"+userVertifikator[i].actions+"&nbsp;<i class='fa fa-check'></i>"+userVertifikator[i].full_name+"</ul>"                    
                }                                                                

                $("#detailUsePos").html(htmlUserPos);
                $("#detailUseCs").html(htmlUserCs);
                $("#detailUsePtcStc").html(htmlUserPtcStc);
                
                $("#detailUserVertifikator").html(htmlUserVertifikator);


            }

        },
        complete: function(){
            $('#detailUsePos').unblock(); 
            $('#detailUseCs').unblock(); 
            $('#detailUsePtcStc').unblock(); 
            $('#detailUserVertifikator').unblock(); 
        }     

      });  
    }

    function confirmDeletUser(message,url){
        alertify.confirm(message, function (e) {
            if(e){
                returnConfirmDeletUser(url)
            }
        });
    }
    
    function returnConfirmDeletUser(url){
        $.ajax({
            url         : url,
            type        : 'GET',
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                
                let csfrData = {};
                csfrData[json.csrfName] = json.tokenHash;
                
                $.ajaxSetup({
                        data: csfrData,
                });

                if(json.code == 1)
                {
                    toastr.success(json.message, 'Sukses');
                    getUserDetail();
                }
                else
                {
                    toastr.error(json.message, 'Gagal');
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $('#box').unblock(); 
            }
        });
    }        

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        getUserDetail();

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

        $('#userptcstc').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Input user PTC/ STC",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#userptcstc").on("change",function(){

            $("#userptcstc2").val($("#userptcstc").val());         
        })


        $('#uservertifikator').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Input user Vertifikator",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#uservertifikator").on("change",function(){

            $("#uservertifikator2").val($("#uservertifikator").val());         
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
