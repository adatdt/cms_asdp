<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />


<style type="text/css">

    .checkbox label:after, 
    .radio label:after {
        content: '';
        display: table;
        clear: both;
    }

    .checkbox .cr,
    .radio .cr {
        position: relative;
        display: inline-block;
        border: 1px solid #a9a9a9;
        border-radius: .25em;
        width: 1.3em;
        height: 1.3em;
        /* float: left; */
        margin-right: .5em;
    }

    .radio .cr {
        border-radius: 50%;
    }

    .checkbox .cr .cr-icon,
    .radio .cr .cr-icon {
        position: absolute;
        font-size: .8em;
        line-height: 0;
        top: 50%;
        left: 20%;
    }

    .radio .cr .cr-icon {
        margin-left: 0.04em;
    }

    .checkbox label input[type="checkbox"],
    .radio label input[type="radio"] {
        display: none;
    }

    .checkbox label input[type="checkbox"] + .cr > .cr-icon,
    .radio label input[type="radio"] + .cr > .cr-icon {
        transform: scale(3) rotateZ(-20deg);
        opacity: 0;
        transition: all .3s ease-in;
    }

    .checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
    .radio label input[type="radio"]:checked + .cr > .cr-icon {
        transform: scale(1) rotateZ(0deg);
        opacity: 1;
    }

    .checkbox label input[type="checkbox"]:disabled + .cr,
    .radio label input[type="radio"]:disabled + .cr {
        opacity: .5;
    }


    .wajib{
        color: red;
    }
    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }


</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pembatasanMember/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" id="dateFrom" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" id="dateTo" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Batas Jumlah Trx<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Batas Jumlah Trx"  >                     
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Range Waktu Pembatasan<span class="wajib">*</span></label> 
                            <?= form_dropdown("limitType",$limitType,'','  class="form-control select2" id="limitType" required ' ) ?>                     
                        </div>
                        <div class="col-sm-12 form-group "></div>
                        <div class="col-sm-3 form-group ">
                            <div class="input-group">
                             
                                <div class="icheck-inline">                                    

                                    <input type="checkbox" class="allow" id="isCustom" name='isCustom' data-checkbox="icheckbox_flat-grey" value="1">
                                    <label> Custom Range Waktu</label> 
                                    <span id="inputCustomValue"></span> 

                                
                                </div>
                            </div>
                        </div>  

                        <div class="col-sm-12 form-group"  >                            
                            <div id="inputExceptUserDiv"></div>
                        </div>

                        <?php echo form_close(); ?> 

                        <div class="col-sm-12 ">
                            <hr/>
                        </div>
                        
                                              
                        <div class="col-sm-12 form-group" ></div>
                        <div class="col-sm-6 form-group" id='selectUser' >
                            
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">User dibatasi</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimited">
                                        <thead >
                                            <tr>
                                                <th>User/ Email</th>
                                                <th>
                                                    <!-- <div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  id="transferAll1" >
                                                        Semua Data <i class='fa fa-arrow-right ' aria-hidden='true'></i>
                                                    </div> -->
                                                    AKSI
                                                </th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group"  >
                            <div class="portlet box blue-madison">
                                <div class="portlet-title">
                                    
                                    <div class="caption">User pengecualian</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimitedExcept">
                                        <thead id="headerPengecualian" >                                                                      
                                            <tr>
                                                <th>
                                                    <!-- <div class='btn btn-danger transferData pull-left' title='Pindah Ke Pengecualian'  id="transferAll2" >
                                                        Semua Data <i class='fa fa-arrow-left ' aria-hidden='true'></i>
                                                    </div> -->

                                                    AKSI

                                                </th>
                                                <th>User/ Email</th>
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
            <?php //echo createBtnForm('Simpan'); ?>
            <div class="box-footer text-right">
                <button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Batal</button> 
                <button type="button" class="btn btn-sm btn-primary" id="saveBtn"><i class="fa fa-check"></i> Simpan</button>
            </div>            
           
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    
     
    $(document).ready(function(){


        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $("#saveBtn").on("click", function(){
            $('#ff').submit()
        })

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

/*        
        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
        });
*/
        $('#dateFrom').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

        $('#dateTo').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "+1m",
            startDate: new Date()
        });
        
        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datetimepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                minuteStep:1,
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                // endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datetimepicker("update")
            // myData.reload();
        });
        
        
        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        })
        .on('ifChanged', function(e) {
            // Get the field name
            var isChecked = e.currentTarget.checked;
            let inputDataHtml ="";
            if (isChecked == true) {
                inputDataHtml +=`                    
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Nominal Jenis Pembatasan"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
        });     


        // init datatable client side

        myData.setDataTableClient("tableUserLimitedExcept")

        let table2 = $("#tableUserLimitedExcept").DataTable();            
        $('#tableUserLimitedExcept tbody').on( 'click', '.transferDataLimit', function () {

            let currentRow=$(this).closest("tr"); 
            let colEmail=currentRow.find("td:eq(1)").html();
            let idData=table2.row(currentRow).data()[2];
            let idIndex=table2.row(currentRow).data()[3];

            let colAction=`<div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  >
                                <i class='fa fa-arrow-right' aria-hidden='true'></i>
                            </div>
                            `
            // revome data
            table2
            .row( $(this).parents('tr') )
            .remove()
            .draw();


        } );
        
        $('#transferAll1').on( 'click', function () {

            let allData= table1.rows().data();

            table1
            .clear()
            .draw();


            let transferData=[];

            
            let inputExceptUserDiv="";
            for (let i = 0; i < allData.length; i++) {
                
                let element = allData[i];

                let colAction=`<div class='btn btn-danger transferData' title='Pindah Ke Pembatasan'  >
                                <i class='fa fa-arrow-left' aria-hidden='true'></i>
                            </div>                            
                            
                            `                                            
                inputExceptUserDiv += `<input type="hidden" id="inputExceptUser_${element[3]}" type='hidden' name='idMemberExcept[${element[3]}]' value='${element[2]}' >`

                

                let dataPush =[colAction, element[0],element[2],element[3]]
                transferData.push(dataPush)
            }
              $("#inputExceptUserDiv").append(inputExceptUserDiv);

            table2.rows.add(transferData).draw();

        } );
        
        $('#transferAll2').on( 'click', function () {

            let allData= table2.rows().data();
            table2
            .clear()
            .draw();

            let transferData=[];
         
            for (let i = 0; i < allData.length; i++) {
                let element = allData[i];
                let colAction=`<div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  >
                                <i class='fa fa-arrow-right' aria-hidden='true'></i>                                

                                </div>
                            `
                let dataPush =[element[1],colAction, element[2], element[3] ]
                transferData.push(dataPush)
            }
            $("#inputExceptUserDiv").html("");
            table1.rows.add(transferData).draw();

        } );    
        

        myData.tableUserLimited();
        // myData.tableUserExcept();
    


    })
</script>