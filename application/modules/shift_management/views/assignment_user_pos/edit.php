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
                        <div class="col-md-4 form-group">
                            
                            <label>Tambah User Comand Center <span class="wajib">*</span></label>

                            <?php 
                                $option = "";
                                foreach ($userComandCenter as $key => $value) {
                                    $option .= " <option value='".$key."'>".$value."</option>";
                                }
                            ?>
                            
                            <select class='form-control' id='getComandCenter'  multiple='multiple' >
                                <?= $option; ?>
                            </select>
       
                            <input type="hidden" name="userComandCenter" required id="userComandCenter">   
                            <input type="hidden" name="userComandId"  value="<?= implode(",",$comandId)?>">                               

                            <!-- <input type="hidden" value="<?= implode(",",$shiftCodeUserComand) ?>" name='shift_code'> -->
                            
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
                        
                        <div class="col-md-8 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User Comand Center</h5>
                                            </div>
                                        </div>

                                        <!-- <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailUserComandCenter"></div> -->

                                        <table id="mytable-comand-center">
                                            <thead>
                                               <tr>
                                                    <th width="130">User Comand Center</th>
                                                    <th width="180">Nama Perangkat</th>
                                                    <th width="50">Aksi</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                     
                                            </tbody>
                                        </table>

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
     
    const tableComandCenter = $('#mytable-comand-center').DataTable({
        paging:   false,
        bFilter: false, 
        bInfo: false,
        bJQueryUI: false,
        "order": [[1,"desc"]],                    
        "columnDefs": [
            { "orderable": false, "targets": "_all" } 
        ]
    });

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

                // console.log("#"+param);
                // console.log(html);       
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

                // console.log("#"+param);
                // console.log(html);       
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
            unBlockUiId('detailUserComandCenter')
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
                var html5="<option value=''>Pilih</option>";

                
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
                
                for(var i5=0; i5<x.userComandCenter.length; i5++)
                {
                    html5 +="<option value='"+x.userComandCenter[i5].id+"' id='id_"+x.userComandCenter[i5].id+"'>"+x.userComandCenter[i5].full_name+"</option>";
                } 

                $("#user").html(html1);
                $("#usercs").html(html2);   
                $("#userptcstc").html(html3);
                $("#uservertifikator").html(html4);
                $("#getComandCenter").html(html5);

                var userPos=x.user_pos;
                var userCs=x.user_cs;
                var userPtcstc=x.user_ptcstc;
                var userVertifikator=x.user_vertifikator;
                // var userComandCenter=x.user_comand_center

                htmlUserPos="";
                htmlUserCs="";
                htmlUserPtcStc="";
                htmlUserVertifikator="";
            
                // getDataComandCenter = [];


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
        // var table = $('#mytable-comand-center').DataTable(); // replace with your table id 
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
                    getDeviceDetail();
               
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

    function getDeviceDetail()
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
        url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_device_detail",
         
        success:function(x)
        {
            // console.log(x);  
            $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                
            let csfrData = {};
            csfrData[x.csrfName] = x.tokenHash;
            
            $.ajaxSetup({
                    data: csfrData,
            });  

            
            let getDataComandCenter =[];

            const firstInit = dataInitEdit(x.deviceComandCenter,x.getDataComandCenter,x.userComand);

            getDataComandCenter =[];
            getDataComandCenter = firstInit[0] 
            // console.log(getDataComandCenter)
            tableComandCenter.clear();
            tableComandCenter.rows.add(getDataComandCenter).draw(); 

            $(`.deviceComandCenter`).select2({
                tags: false,
                tokenSeparators: [','], 
                placeholder: "Device Comand Center ",
                /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
                selectOnClose: false, 
                closeOnSelect: true,

            });

            x.userComand.forEach(element => {

                $("#deviceComandId_"+element.user_id).on("change",function(){
                    
                    $("#deviceComandId2_"+element.user_id).val($("#deviceComandId_"+element.user_id).val());         
                }) 
            });

            for (const [key, value] of Object.entries(x.selectedDeviceComand)) {
      
                let splitDevice =(value.map(item => `${item.terminal_code}`).toString());
                const  selectedDevice = splitDevice.split(',');

                $("#deviceComandCenter_"+key).val(selectedDevice).change()
                $("#deviceComandId_"+key).val(selectedDevice).change()

            }            

        }  

      });  
    }
    
    function dataInitEdit(getDeviceComandCenter,getDataComandCenter,userComand)
    {
        // const  selectedUserComand =selectedUser.split(",");

        getDataComandCenter = [];
        getDeviceTerminal = [];

        getDeviceComandCenter.forEach(element => {

            let deviceTerminal  = `<option value='${element.terminal_code}' id='${element.terminal_code}'>${element.terminal_name}</option>`;
   
            getDeviceTerminal.push([deviceTerminal]); 
    
        });
        // console.log(userComand)
        userComand.forEach(element => {

            // let hapus = `<a class="btn btn-danger hps" data-id='id_${element.user_id}'   id='hps_${element.full_name}'   data-text='${element.full_name}'  title="hapus" ><i class="fa fa-trash-o"></i></a>`
            let hapus = `<a class="btn btn-danger " onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \'${element.shift_code_id}\')" title="Hapus" href="#"><i class="fa fa-trash-o"></i></a>`

            let device  = ` <select  name='deviceComandId' style="width: 450px;" class='form-control deviceComandCenter' id='deviceComandId_${element.user_id}' multiple="multiple">
                            ${getDeviceTerminal}
                            </select> 
                            <input type="hidden"  name="deviceComandId_${element.user_id}" id="deviceComandId2_${element.user_id}">
                            <input type="hidden"  name="shiftCodeComand_${element.user_id}" value="${element.shift_code}" >
                          
                            `;
            
            getDataComandCenter.push([element.full_name,device,hapus]);           

        });
       
        // console.log(getDataComandCenter)    
        
        return [getDataComandCenter];

    }

    function getUserComandCenter(getText,getDeviceTerminal,getDataComandCenter)
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
        url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_device_detail",
         
        success:function(x)
        {
            deviceDataId = []
            deviceComand = []

            let dataComandCenter = $(`#userComandCenter`).val();

            let setComandCenterValue = dataComandCenter.split(",")
            
            x.deviceComandCenter.forEach(element => {

                let deviceTerminal  = `<option value='${element.terminal_code}'  id='${element.terminal_code}'>${element.terminal_name}</option>`;

                getDeviceTerminal.push([deviceTerminal]); 

            });

            x.userComand.forEach(element => {

                // let hapus = `<a class="btn btn-danger hps" data-id='id_${element.user_id}'   id='hps_${element.full_name}'   data-text='${element.full_name}'  title="hapus" ><i class="fa fa-trash-o"></i></a>`
                let hapus = `<a class="btn btn-danger " onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \'${element.shift_code_id}\')" title="Hapus" href="#"><i class="fa fa-trash-o"></i></a>`

                let device  = ` <select  name='deviceComandId' style="width: 450px;" class='form-control deviceComandCenter' id='deviceComandId_${element.user_id}' multiple="multiple">
                                ${getDeviceTerminal}
                                </select> 
                                <input type="hidden"  name="deviceComandId_${element.user_id}" id="deviceComandId2_${element.user_id}">
                                <input type="hidden"  name="shiftCodeComand_${element.user_id}" value="${element.shift_code}" >
                            
                                `;

                getDataComandCenter.push([element.full_name,device,hapus]);
                
                //untuk device
                let deviceId = $("#deviceComandId_"+element.user_id).val();

                deviceDataId.push([element.user_id,deviceId]); 


            });
            
            getText.forEach(element => {
                // getValDevice =$("#deviceComandCenter_"+element.id).val();
                let hapus = `<a class="btn btn-danger hps" data-id='${element.id}'   id='hps_${element.text}'   data-text='${element.text}'  title="hapus" ><i class="fa fa-trash-o"></i></a>`

                let device  = ` <select  name='deviceComandCenter' style="width: 450px;" class='form-control deviceComandCenter' id='deviceComandCenter_${element.id}' multiple="multiple">
                                ${getDeviceTerminal}
                                </select> 
                                <input type="hidden"  name="deviceComandCenter2_${element.id}" id="deviceComandCenter2_${element.id}">
                                `
                                ; 
                getDataComandCenter.push([element.text,device,hapus]); 

            });

            setComandCenterValue.forEach(element => {
                  
                let getDeviceComand = $("#deviceComandCenter2_"+element).val();
                deviceComand.push([element,getDeviceComand]); 

            });

            tableComandCenter.clear();
            tableComandCenter.rows.add($(getDataComandCenter)).draw();

            $(`.deviceComandCenter`).select2({
                tags: false,
                tokenSeparators: [','], 
                placeholder: "Device Comand Center ",
                /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
                selectOnClose: false, 
                closeOnSelect: true,

            });

            // console.log(deviceComand)
            //device comand id


            for (let i = 0; i < deviceDataId.length; i++) {   
                    
                let splitDeviceId = deviceDataId[i][1].toString();
        
                $("#deviceComandId2_"+deviceDataId[i][0]).val(splitDeviceId); 
                $("#deviceComandId_"+deviceDataId[i][0]).val(splitDeviceId.split(",")).change();
            }

            // console.log(deviceComand[0][1])
            // // device comand center
            

            getText.forEach(element => {

                $("#deviceComandCenter_"+element.id).on("change",function(){
                    
                    $("#deviceComandCenter2_"+element.id).val($("#deviceComandCenter_"+element.id).val());         
                }) 
            });

            x.userComand.forEach(element => {

                $("#deviceComandId_"+element.user_id).on("change",function(){
                    
                    $("#deviceComandId2_"+element.user_id).val($("#deviceComandId_"+element.user_id).val());         
                }) 
            });


            // console.log(deviceComand[0][1])
            if (deviceComand[0][1] !== null ){

                console.log(deviceComand)

                for (let i = 0; i < deviceComand.length; i++) {   

                    let splitDeviceComand = deviceComand[i][1].toString();

                    $("#deviceComandCenter2_"+deviceComand[i][0]).val(splitDeviceComand); 
                    $("#deviceComandCenter_"+deviceComand[i][0]).val(splitDeviceComand.split(",")).change();
                }
            }


            // for (const [key, value] of Object.entries(x.selectedDeviceComand)) {

            //     let splitDevice =(value.map(item => `${item.terminal_code}`).toString());
            //     const  selectedDevice = splitDevice.split(',');

            //     $("#deviceComandCenter_"+key).val(selectedDevice).change()
            //     $("#deviceComandId_"+key).val(selectedDevice).change()

            // }            
            
           
        }  

      });  

    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        getUserDetail();
        // getDataDeviceComandCenter()


      $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width:'100%',

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
            closeOnSelect: true,
            width:'100%'
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
            closeOnSelect: true,
            width:'100%'
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
            closeOnSelect: true,
            width:'100%'
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
            closeOnSelect: true,
            width:'100%'
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

        $('#getComandCenter').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Input user Comand Center",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true,
            width:'100%'
        });

        let getDataComandCenter =[];
    
        const  selectedUser =`<?= implode(",",$selectedUserComand); ?>`;

        let getDeviceComandCenter=<?php echo json_encode($deviceComandCenter); ?>; 
        let userComand=<?php echo json_encode($userComand); ?>; 


        const firstInit = dataInitEdit(getDeviceComandCenter,getDataComandCenter,userComand);

        getDataComandCenter =[];
        getDataComandCenter = firstInit[0] 
        // console.log(getDataComandCenter)
        tableComandCenter.clear();
        tableComandCenter.rows.add(getDataComandCenter).draw(); 

        $(`.deviceComandCenter`).select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Device Comand Center ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true,

        });

        userComand.forEach(element => {

            $("#deviceComandId_"+element.user_id).on("change",function(){
                
                $("#deviceComandId2_"+element.user_id).val($("#deviceComandId_"+element.user_id).val());         
            }) 
        });
        

        let getDetailDevice =<?php echo json_encode($selectedDeviceComand); ?>;

        for (const [key, value] of Object.entries(getDetailDevice)) {
      
            let splitDevice =(value.map(item => `${item.terminal_code}`).toString());
            const  selectedDevice = splitDevice.split(',');

            $("#deviceComandCenter_"+key).val(selectedDevice).change()
            $("#deviceComandId_"+key).val(selectedDevice).change()

        }

        $("#getComandCenter").on("select2:select select2:unselect", function (e) 
        {
            var data = e.params.data; 

            let items= $(this).val();       
            let valueData =""
            if(items != null )
            {
                valueData = items.toString()
            }
            
            $("#userComandCenter").val(valueData)

            let getText = $(this).select2('data') 
            const  selectedUserComand =selectedUser.split(","); 

            getDataComandCenter = [];
            getDeviceTerminal = [];

            getUserComandCenter(getText,getDeviceTerminal,getDataComandCenter);
            
        })

        $("#getComandCenter").on("change",function()
        {
            $("#userComandCenter").val($("#getComandCenter").val());
        }) 
        
        tableComandCenter.on( 'draw.dt', function () {
            
            $(`.hps`).on("click", function(){
                const id = $(this).attr("data-id")
                const text = $(this).attr("data-text")

                alertify.confirm(`Apakah anda yakin ingin menghapus user ini dari assignmnet ?`, function (e) {
                    if(e){
        
                        // insert ke input Comand Center
                        let dataComandCenter = $(`#userComandCenter`).val();
        
                        let splitComandCenter = dataComandCenter.split(",")
        
                        let setComandCenterValue = splitComandCenter.filter(function(x){
                            return x != id
                        })
                        // console.log(setComandCenterValue)
                        $(`#userComandCenter`).val(setComandCenterValue.toString());
        
                        $(`#id_${id}`).prop("selected", false);
                        $(`#id_${id}`).trigger("change");
        
                        let tmpData = getDataComandCenter; 
                        getDataComandCenter = []
        
                        getDataComandCenter = tmpData.filter(function(x){
                            //  console.log(x)
                            return x[0] != text
                        })   
        
                        deviceDataId = []
                        deviceComand = []
        
                        setComandCenterValue.forEach(element => {
                        
                        let getDeviceComand = $("#deviceComandCenter2_"+element).val();
                        deviceComand.push([element,getDeviceComand]); 
        
                        });
        
                        userComand.forEach(element => {
                        
                            let deviceId = $("#deviceComandId_"+element.user_id).val();
            
                            deviceDataId.push([element.user_id,deviceId]); 
            
                        });
                        
                        tableComandCenter.clear();
                        tableComandCenter.rows.add( getDataComandCenter ).draw();
        
                        $(`.deviceComandCenter`).select2({
                            tags: false,
                            tokenSeparators: [','], 
                            placeholder: "Device Comand Center ",
                            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
                            selectOnClose: false, 
                            closeOnSelect: true,
        
                        });
        
                        //device comand id
                        for (let i = 0; i < deviceDataId.length; i++) {   
                            
                            let splitDeviceId = deviceDataId[i][1].toString();
                
                            $("#deviceComandId2_"+deviceDataId[i][0]).val(splitDeviceId); 
                            $("#deviceComandId_"+deviceDataId[i][0]).val(splitDeviceId.split(",")).change();
                        }
        
                        // device comand center
                        for (let i = 0; i < deviceComand.length; i++) {   
        
                            // let splitDeviceComand = deviceComand[i][1].toString();
                            let splitDeviceComand = deviceComand[i][1];
        
                            $("#deviceComandCenter2_"+deviceComand[i][0]).val(splitDeviceComand); 
                            $("#deviceComandCenter_"+deviceComand[i][0]).val(splitDeviceComand.split(",")).change();
                        }
        
                        setComandCenterValue.forEach(element => {
        
                            $("#deviceComandCenter_"+element).on("change",function(){
                                
                                $("#deviceComandCenter2_"+element).val($("#deviceComandCenter_"+element).val());         
                            }) 
                        });
        
                        userComand.forEach(element => {
        
                            $("#deviceComandId_"+element.user_id).on("change",function(){
                                
                                $("#deviceComandId2_"+element.user_id).val($("#deviceComandId_"+element.user_id).val());         
                            }) 
                        });
                    }
                })                    
            }) 
        });  

    })

</script>
