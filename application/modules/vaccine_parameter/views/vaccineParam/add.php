<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />


<style type="text/css">
    .wajib{
        color: red;
    }
    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }



</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('vaccine_parameter/vaccineParam/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Tipe Assessment Vaksin<span class="wajib">*</span></label>
                            <?= form_dropdown("assessmentType",$assessmentType,"",' class="form-control select2" required id="assessmentType" ' ) ?>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Assessment Test<span class="wajib">*</span></label>
                            <?= form_dropdown("assessmentTestType",$assessmentTestType,"",' class="form-control select2" required id="assessmentTestType" ' ) ?>
                        </div>                        

                        
                        <div class="col-sm-4 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" class="form-control waktu" id="dateFrom" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" class="form-control waktu" id="dateTo" required placeholder="YYYY-MM-DD HH:MM" readonly >                        
                        </div>
                        
                        
                        
                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan<span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,"",' class="form-control " required id="port" multiple="multiple" ' ) ?>
                            <input type="hidden" required name="port2" id="port2">
                        </div>
                        
                        <div class="col-sm-4 form-group">
                            <label>Kelas kendaraan<span class="wajib">*</span></label>
                            <?= form_dropdown("vehicleClass",$vehicleClass,"",' class="form-control " required id="vehicleClass" multiple="multiple" ' ) ?>
                            <input type="hidden" required name="vehicleClass2" id="vehicleClass2" >
                        </div>
                        
                        <div class="col-sm-12 "></div>
                        
                        <div class="col-sm-4 form-group">
                            <label>Alasan Dibawah Umur<span class="wajib">*</span></label> 
                            <input type="text" name="underAgeReason" class="form-control" required placeholder="Alasan Dibawah Umur" >                        
                        </div>
                        <div class="col-sm-12 form-group"><hr></div>
                        <div class="col-sm-12 form-group ">                            
                            <div  class="btn btn-sm btn-warning pull-right" id="addMinAge"><i class="fa fa-plus"></i> MIN Usia</div>
                        </div>
                        <div class="col-sm-12 form-group scrolling">
                        
                            <div class="row rowMinAge" style="margin:0px; padding-top:10px; background-color:#f6f6f6; border-radius: 25px;" id="rowMinAge_0">
                                <div class="col-sm-4 form-group">
                                    <label>Min Usia<span class="wajib">*</span></label> 
                                    <input type="number" name="minAge[0]" id="minAge0" class="form-control" required placeholder="Minimal Usia" min=1  required>
                                    <input type="hidden" name="idMinAge[0]" value="0"  required>
                                </div>


                                <div class="col-sm-12 form-group"></div>

                                <div class="col-sm-12 form-group ">

                                    <div class="col-sm-12 form-group">
                                        <div  class="btn btn-sm btn-warning add-vaccine-test"  data-idMinAge="0"  ><i class="fa fa-plus"></i> Vaksin</div>
                                    </div>

                                    <div class="col-sm-6 form-group classVaccineTest_0 classVaccineTest_0_0">
                                        <label>Vaksin ke <span class="wajib">*</span></label> 
                                        <select name="vaccineCovid_0[]" id="vaccineCovid_0_0" class="form-control select2 vaccineCovid_0"  data-id=0 required >
                                            <option value="">Pilih</option>
                                            <option value=0>tidak vaksin</option>  
                                            <?php $nilIdx=1; for($i=0; $i<$getMaxVaccine; $i++ ) { ?>
                                            <option value=<?= $nilIdx ?>>vaksin <?= $nilIdx ?> </option>
                                            <?php $nilIdx++; } ?>

                                        </select>
                                    </div>

                                    <div class="col-sm-5 form-group classVaccineTest_0_0">
                                        <label>Tes Covid <span class="wajib">*</span></label> 
                                        <select name="testCovid_0[]" id="testCovid_0_0" class="form-control select2 testCovid" required>
                                            <option value="">Pilih</option>
                                            <?php foreach($getTestCovid as $key => $value ) { ?>
                                            <option value=<?= $value->order_value ?>><?= $value->test_type=='empty'?'tidak perlu tes':$value->test_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-1 form-group classVaccineTest_0_0">
                
                                            <div style="padding:10px;"></div>
                                            <div  onclick=myData.deleteVaccineTest(0,0) style="border-radius:5px" class="btn btn-md btn-danger pull-left" id="deleteVaccineTest_0_${idTest}" title="Hapus Vaksin Status" ><i class="fa fa-trash"></i></div>
                                                                
                                    </div>

                                    <div class="col-sm-12 form-group" id="vaccineTestContent_0" ></div>

                                </div>
                            </div>
                            <div id="contentMinAge"></div>                        
                        </div>

                        <div class="col-sm-12 form-group"><hr></div>

                        <div class="col-sm-6 form-group">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label>Channel</label> 
                                </div>                        
                                
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">

                                            <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey" value="1" >Web &nbsp;&nbsp;                                 
                                        </div>
                                    </div>
                                </div>                        
                                
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey" value="1">Mobile Reservasi&nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey" value="1">IFCS &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey" value="1">B2B &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='pos_vehicle' data-checkbox="icheckbox_flat-grey" value="1">Pos KND &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='pos_passanger' data-checkbox="icheckbox_flat-grey" value="1">Pos PNP &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='mpos' data-checkbox="icheckbox_flat-grey" value="1">M Pos &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='vm' data-checkbox="icheckbox_flat-grey" value="1">VM &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='verifikator' data-checkbox="icheckbox_flat-grey" value="1">Verifikator &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>         

                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey" value="1">Web CS &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>                                                            

                            </div>
                        </div>                        

                        <div class="col-sm-6 form-group">
                            
                            <div class="row"> 
                                <div class="col-sm-12 form-group">
                                    <label>Jenis PJ</label> 
                                </div>                        
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">

                                            <input type="checkbox" class="allow" name='pedestrian' data-checkbox="icheckbox_flat-grey" value="1" >Pejalan Kaki &nbsp;&nbsp; 
                                    
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">

                                            <input type="checkbox" class="allow" name='vehicle' data-checkbox="icheckbox_flat-grey" value="1" >Kendaraan &nbsp;&nbsp; 
                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <div class="row"> 
                                <div class="col-sm-12 form-group">
                                    <label>Fitur</label> 
                                </div>       

                                <!-- <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='vaccineActive' data-checkbox="icheckbox_flat-grey" value="1">Vaksin Aktif &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>   -->
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='testVaccineActive' data-checkbox="icheckbox_flat-grey" value="1">Tes Covid Aktif &nbsp;&nbsp; 

                                        
                                        </div>
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

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2').select2();

        $('#port').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Pilih ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#port").on("change",function(){
            
            $("#port2").val($("#port").val());         
        })  
        
        $('#vehicleClass').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Pilih ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#vehicleClass").on("change",function(){
            
            $("#vehicleClass2").val($("#vehicleClass").val());         
        })
        
        // $('.waktu').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     minuteStep:1,
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,
        //     // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
        // });

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
        });

        let idAddMinAge=1;
        $("#addMinAge").click(function(){
            
            idAddMinAge += myData.addMinAge(idAddMinAge);
        })

        $(".add-vaccine-test").off().on("click",function(){
            // data-idMinAge="0" data-idVaccineTest="0"
            // let dataVaccineTestId = 1;
            let idMinAge= $(this).attr("data-idMinAge");
            let dataLength = $(`.vaccineCovid_${idMinAge}`).length
            let data = $(`.vaccineCovid_${idMinAge}`).map(function(){ 
                return $(this).attr(`data-id`) 
            }).toArray();

            let idVaccineTest= data[dataLength-1] // ambil data class yang terakhir
             myData.addVaccineTest(idMinAge, idVaccineTest)
        })
        
    })
</script>