 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" /> 
<style type="text/css">
    .wajib{
        color: red;
    }
    #mytable-comand-center th {
        padding: 6px 6px 6px 12px;
        text-align: left;
    }

    /* #mytable-comand-center td {
        padding: 1px 5px;
        font-size: 12px;
    } */

    .hide-row { display:none; }

    .table-scrollable {
        margin: 0px 0!important;
    }

</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/assignment_user_pos/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" name="port" required id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo $value->name ?></option>
                                <?php }?>                                
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Tanggal Penugasan <span class="wajib">*</span></label>
                            <div class="input-group">
                                <input type="text" name="date"  class="form-control date" id="date" readonly placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d')?>" required>
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div>
                        </div>


                        <div class="col-md-4 form-group">
                            <label>SPV POS <span class="wajib">*</span></label>
                            <select  name="spv" id="spv" class="form-control select2" placeholder="spv" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-md-4 form-group">
                            <label>Regu <span class="wajib">*</span></label>
                            <select  name="team" id="team" class="form-control select2" placeholder="Team" required>
                                <option value="">Pilih</option>
                            </select>
                        </div>


                        <div class="col-md-4 form-group">

                            <label>Shift <span class="wajib">*</span></label>
                            <select class="form-control select2" name="shift" required id="shift" required="">
                                <option value="">Pilih</option>         
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-md-6 form-group">
                            
                            <label>Tambah User <span class="wajib">*</span></label>
                            <select required name='username[]' class=' form-control  user' id="user" multiple="multiple">
                            </select>

                            <input type="hidden" required name="username2" id="username2">
                            
                        </div>


                        <div class="col-md-6 form-group">
                            
                            <label>Tambah User CS <span class="wajib">*</span></label>
                            <select required name='usernamecs' class=' form-control  usercs' id="usercs" multiple="multiple">
                                <option value=''>Pilih</option>
                            </select>

                            <input type="hidden" required name="usernamecs2" id="usernamecs2">
                            
                        </div>

                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-md-6 form-group">
                            
                            <label>Tambah User PTC/ STC <span class="wajib">*</span></label>
                            <select required name='userptcstc' class=' form-control  userptcstc' id="userptcstc" multiple="multiple">
                                <option value=''>Pilih</option>
                            </select>

                            <input type="hidden" required name="userptcstc2" id="userptcstc2">
                            
                        </div>    
                        
                        <div class="col-md-6 form-group">
                            
                            <label>Tambah User Verifikator </label>
                            <select  name='uservertifikator' class='form-control uservertifikator' id="uservertifikator" multiple="multiple">
                                <option value=''>Pilih</option>
                            </select>

                            <input type="hidden"  name="uservertifikator2" id="uservertifikator2">
                            
                        </div>     
                        
                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-md-6 form-group">
                            
                            <label>Tambah User Comand Center </label>
                            <select  name='userComandCenter' class='form-control userComandCenter' id="userComandCenter" multiple="multiple">
                                <option value=''>Pilih</option>
                            </select>

                            <input type="hidden"  name="userComandCenter2" id="userComandCenter2">
                            
                        </div>  
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-md-12 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Daftar User Comand Center</h5>
                                            </div>
                                        </div>
                                        <table id="mytable-comand-center" class="display" cellspacing="0" width="100%">
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


      
            <?php echo createBtnForm('Simpan'); ?>
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
            data:{
                "port":$("[name='port']").val(),
                "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
            },
            type:"post",
            url:"<?php echo site_url()?>shift_management/assignment_user_pos/get_data",
            dataType:"json",
            beforeSend:function(){
                unBlockUiId('box')
            },
            success:function(x)
            {
                $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                
                let csfrData = {};
                csfrData[x.csrfName] = x.tokenHash;
                
                $.ajaxSetup({
                        data: csfrData,
                }); 
                
                var dataSpv=x.spv;
                var dataTeam=x.regu;
                var dataUser=x.user;
                var dataUsercs=x.usercs;
                var dataUserPtcStc=x.userptcstc;
                var dataUserVertifikator=x.userVertifikator;
                var dataUserComandCenter=x.userComandCenter;
                var dataShift=x.shift;                

                var spvHtml="<option value=''>Pilih</option>";
                var teamHtml="<option value=''>Pilih</option>";
                var userHtml="";
                var usercsHtml="<option value=''>Pilih</option>";
                var userptcstcHtml="<option value=''>Pilih</option>";
                var userVertifikatorHtml="<option value=''>Pilih</option>";
                var userComandCenterHtml="<option value=''>Pilih</option>";
                var shiftHtml="<option value=''>Pilih</option>";

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

                for(var i=0; i<dataUserPtcStc.length; i++)
                {
                    userptcstcHtml += "<option value='"+dataUserPtcStc[i].id+"'>"+dataUserPtcStc[i].full_name+"</option>"
                }

                for(var i=0; i<dataShift.length; i++)
                {
                    shiftHtml += "<option value='"+dataShift[i].shift_id+"'>"+dataShift[i].shift_name+"</option>"
                }                                

                for(var i=0; i<dataUserVertifikator.length; i++)
                {
                    userVertifikatorHtml += "<option value='"+dataUserVertifikator[i].id+"'>"+dataUserVertifikator[i].full_name+"</option>"
                }
                
                for(var i=0; i<dataUserComandCenter.length; i++)
                {
                    userComandCenterHtml += "<option value='"+dataUserComandCenter[i].id+"' id='id_"+dataUserComandCenter[i].id+"'>"+dataUserComandCenter[i].full_name+"</option>"
                }

                $("#spv").html(spvHtml);
                $("#team").html(teamHtml);
                $("#user").html(userHtml);
                $("#usercs").html(usercsHtml);
                $("#userptcstc").html(userptcstcHtml);
                $("#uservertifikator").html(userVertifikatorHtml);
                $("#userComandCenter").html(userComandCenterHtml);

                $("#shift").html(shiftHtml);

                // delete cmc
                tableComandCenter.clear().draw();

                // console.log(tableComandCenter);

            },
                complete: function(){
                $('#box').unblock(); 
            }
        });
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

            let dataComandCenter = $(`#userComandCenter2`).val();

            let setComandCenterValue = dataComandCenter.split(",")
            
            x.deviceComandCenter.forEach(element => {

                let deviceTerminal  = `<option value='${element.terminal_code}'  id='${element.terminal_code}'>${element.terminal_name}</option>`;

                getDeviceTerminal.push([deviceTerminal]); 

            });
         
            getText.forEach(element => {
            
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

            getText.forEach(element => {

                $("#deviceComandCenter_"+element.id).on("change",function(){
                    
                    $("#deviceComandCenter2_"+element.id).val($("#deviceComandCenter_"+element.id).val());         
                }) 
            });


            for (let i = 0; i < deviceComand.length; i++) {   

                let splitDeviceComand = deviceComand[i][1].toString();

                $("#deviceComandCenter2_"+deviceComand[i][0]).val(splitDeviceComand); 
                $("#deviceComandCenter_"+deviceComand[i][0]).val(splitDeviceComand.split(",")).change();
            }    

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

        $('#userptcstc').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Input user PTC STC",
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
            placeholder: "User Vertifikator ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#uservertifikator").on("change",function(){
            
            $("#uservertifikator2").val($("#uservertifikator").val());         
        })                

        $('#userComandCenter').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "User Comand Center ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true,
        }); 

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
    
        let getDataComandCenter =[];
  
        $("#userComandCenter").on("select2:select select2:unselect ", function (e) {
            var data = e.params.data; 

            let getText = $(this).select2('data') 

            let dataSelected = getDataComandCenter;

            getDataComandCenter = [];
            getDeviceTerminal = [];

            getUserComandCenter(getText,getDeviceTerminal,getDataComandCenter);

         })

        $("#userComandCenter").on("change",function(){

             $("#userComandCenter2").val($("#userComandCenter").val());

        })  
                
        tableComandCenter.on( 'draw.dt', function () {

            $(`.hps`).on("click", function(){
                const id = $(this).attr("data-id")
                const text = $(this).attr("data-text")

                // insert ke input Comand Center
                let dataComandCenter = $(`#userComandCenter2`).val();

                let splitComandCenter = dataComandCenter.split(",")

                let setComandCenterValue = splitComandCenter.filter(function(x){
                    return x != id
                })

                // console.log(id)
                $(`#userComandCenter2`).val(setComandCenterValue.toString());

                $(`#id_${id}`).prop("selected", false);
                $(`#id_${id}`).trigger("change");

                // console.log(getDataComandCenter)

                let tmpData = getDataComandCenter; 
                getDataComandCenter = []

                getDataComandCenter = tmpData.filter(function(x){
                    //  console.log(x)
                    return x[0] != text
                })   

                deviceComand = []

                setComandCenterValue.forEach(element => {
                    
                    let getDeviceComand = $("#deviceComandCenter2_"+element).val();
                    deviceComand.push([element,getDeviceComand]); 

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
                // console.log(deviceComand)
                // device comand center
                for (let i = 0; i < deviceComand.length; i++) {   

                    let splitDeviceComand = deviceComand[i][1].toString();

                    $("#deviceComandCenter2_"+deviceComand[i][0]).val(splitDeviceComand); 
                    $("#deviceComandCenter_"+deviceComand[i][0]).val(splitDeviceComand.split(",")).change();
                }

                setComandCenterValue.forEach(element => {

                    $("#deviceComandCenter_"+element).on("change",function(){
                        
                        $("#deviceComandCenter2_"+element).val($("#deviceComandCenter_"+element).val());         
                    }) 
                });

                }) 

            });

        })

</script>