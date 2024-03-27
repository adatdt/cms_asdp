 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
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
            <?php echo form_open('vaccine_parameter/vaccineParam/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                    <div class="col-sm-4 form-group">
                            <label>Tipe Assessment Vaksin<span class="wajib">*</span></label>
                            <?= form_dropdown("assessmentType",$assessmentType,$selectedAssessmentType,' class="form-control select2" required id="assessmentType" ' ) ?>
                            <input type="hidden" value="<?= $id ?>" name="id">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tipe Assessment Test<span class="wajib">*</span></label>
                            <?= form_dropdown("assessmentTestType",$assessmentTestType,$selectedAssessmentTestType,' class="form-control select2" required id="assessmentTestType" ' ) ?>
                        </div>
                        
                        <div class="col-sm-4 form-group">
                            <label>Alasan Dibawah Umur<span class="wajib">*</span></label> 
                            <input type="text" name="underAgeReason" class="form-control" required placeholder="Alasan Dibawah Umur" value='<?= $detail->under_age_reason ?>' >                        
                        </div>
                        

                        <div class="col-md-12 "></div>   

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" class="form-control waktu" id="dateFrom" required placeholder="YYYY-MM-DD HH:MM" readonly value='<?= date("Y-m-d H:i", strtotime($detail->start_date)) ?>'>                       
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" class="form-control waktu" id="dateTo" required placeholder="YYYY-MM-DD HH:MM" readonly value='<?= date("Y-m-d H:i", strtotime($detail->end_date)) ?>' >                        
                        </div>
                        
                                            
                        <div class="col-sm-12 "></div>
<!-- 
                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <?= form_dropdown("port",$port,"",' class="form-control "  id="port" multiple="multiple" ' ) ?>
                            <input type="hidden" required name="port2" id="port2">
                        </div> -->
                        
                        <!-- <div class="col-sm-4 form-group">
                            <label>Kelas kendaraan</label>
                            <?= form_dropdown("vehicleClass",$vehicleClass,"",' class="form-control "  id="vehicleClass" multiple="multiple" ' ) ?>
                            <input type="hidden" required name="vehicleClass2" id="vehicleClass2" >
                        </div>

                        <div class="col-md-12 form-group"></div>    -->

                        <div class="col-md-4 form-group">
                            <div class="portlet box">
                                <div class="portlet-body form">
                                    <div class="mt-element-list">
                                        <div class="mt-list-head list-simple font-white bg-primary">
                                            <div class="list-head-title-container">
                                                <h5 class="list-title">Pelabuhan</h5>
                                            </div>
                                        </div>

                                        <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailPort">
                                            <ul> 
                                                <?= form_dropdown("port",$port,"",' class="form-control "  id="port" multiple="multiple" ' ) ?>
                                                <input type="hidden" required name="port2" id="port2">                                            
                                            </ul>
                                            <ul> <p></p></u>
                                        <?php 
                                        if($detailPort)
                                        {                 
                                            foreach($detailPort as $key => $value) { ?>
                                            <ul class='sortable drag'>- <?= $value->port_name."&nbsp;".$value->label_status; ?></ul>                                    
                                        <?php 
                                            } 
                                        }
                                        ?>
                                        </div>

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
                                                <h5 class="list-title">Kelas Kendaraan</h5>
                                            </div>
                                        </div>

                                        <div class="mt-list-container list-simple max-height collapse in scrolling"  aria-expanded="true" style="" id="detailVehicleClass">
                                            <ul> 
                                                <?= form_dropdown("vehicleClass",$vehicleClass,"",' class="form-control "  id="vehicleClass" multiple="multiple" ' ) ?>
                                                <input type="hidden" required name="vehicleClass2" id="vehicleClass2" >
                                            </ul>
                                            <ul><p></p></ul>
                                        <?php 
                                            if($detailVehicle)
                                            {                                            
                                                foreach($detailVehicle as $key => $value) { ?>
                                                    <ul class='sortable drag'>- <?= $value->vehicle_class_name."&nbsp;".$value->label_status; ?></ul>                              
                                        <?php 
                                                }
                                            } 
                                        ?>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-12 form-group"><hr></div>
                        <div class="col-sm-12 form-group ">                            
                            <div  class="btn btn-sm btn-warning pull-right" id="addMinAge"><i class="fa fa-plus"></i> MIN Usia</div>
                        </div>
                        <div class="col-sm-12 form-group scrolling ">
                            <?php $index=0; 
                                $countMinAge = count($minAge);
                                foreach($minAge as $minAge2 ) {
                            ?>
                                <div class="row rowMinAge" style="margin:10px 0px 0px; padding-top:10px; background-color:#f6f6f6; border-radius: 25px;" id="rowMinAge_<?= $index ?>">
                                    <div class="col-sm-4 form-group">
                                        <label>Min Usia<span class="wajib">*</span></label> 
                                        <input type="number" name="minAge[<?= $index ?>]" id="minAge<?= $index ?>" class="form-control" required placeholder="Minimal Usia" min=1  required value="<?= $minAge2 ?>">
                                        <input type="hidden" name="idMinAge[<?= $index ?>]" value="<?= $index ?>"  required>
                                    </div>
                                    <div class="col-sm-8 form-group">
                                        <div onclick="myData.deleteMinAge(<?= $index ?>)" class="btn btn-sm btn-danger pull-right" id="deleteMaxAge_<?= $index ?>" title="Hapus Min Usia"><i class="fa fa-trash"></i></div>
                                    </div>


                                    <div class="col-sm-12 form-group"></div>

                                    <div class="col-sm-12 form-group ">

                                        <div class="col-sm-12 form-group">
                                            <div  class="btn btn-sm btn-warning add-vaccine-test"  data-idMinAge="<?= $index ?>"  ><i class="fa fa-plus"></i> Vaksin</div>
                                        </div>
                                        <?php 
                                            $idVaksinTest=0;
                                            foreach ($minAgeDetail as $key => $minAgeDetail2 ) {
                                                if($minAgeDetail2->min_age==$minAge2)
                                                {
                                        ?>

                                        <div class="col-sm-6 form-group classVaccineTest_<?= $index ?> classVaccineTest_<?= $index ?>_<?= $idVaksinTest ?> ">
                                            <label>Vaksin ke <span class="wajib">*</span></label> 
                                            <select name="vaccineCovid_<?= $index ?>[<?= $idVaksinTest ?>]" id="vaccineCovid_<?= $index ?>_<?= $idVaksinTest ?>" class="form-control select2 vaccineCovid_<?= $index ?>"  data-id=<?= $idVaksinTest ?> required >
                                            
                                                <option value="">Pilih</option>
                                                <option value=0 <?= $minAgeDetail2->vaccine_status==0?"selected":""; ?> >tidak vaksin</option>  
                                                <?php $nilIdx=1; for($i=0; $i<$getMaxVaccine; $i++ ) { ?>
                                                <option value=<?= $nilIdx ?> <?= $minAgeDetail2->vaccine_status==$nilIdx?"selected":""; ?>>vaksin <?= $nilIdx ?> </option>
                                                <?php $nilIdx++; } ?>
                                            
                                            </select>
                                        </div>

                                        <div class="col-sm-5 form-group classVaccineTest_<?= $index ?>_<?= $idVaksinTest ?>">
                                            <label>Tes Covid <span class="wajib">*</span></label> 
                                            <select name="testCovid_<?= $index ?>[<?= $idVaksinTest ?>]" id="testCovid_<?= $index ?>_<?= $idVaksinTest ?>" class="form-control select2 testCovid" required>
                                                <option value="">Pilih</option>
                                                <?php foreach($getTestCovid as $key => $value ) { ?>
                                                <option value=<?= $value->order_value ?> <?= $minAgeDetail2->test_status==$value->order_value?"selected":""; ?> ><?= $value->test_type=='empty'?'tidak perlu tes':$value->test_type; ?></option>
                                                <?php } ?>                                                                                            
                                            </select>
                                        </div>

                                        <div class="col-sm-1 form-group classVaccineTest_<?= $index ?>_<?= $idVaksinTest ?>">
                    
                                                <div style="padding:10px;"></div>
                                                <div  onclick=myData.deleteVaccineTest(<?= $index ?>,<?= $idVaksinTest ?>) style="border-radius:5px" class="btn btn-md btn-danger pull-left" id="deleteVaccineTest_<?= $index ?>_<?= $idVaksinTest ?>" title="Hapus Vaksin Status" ><i class="fa fa-trash"></i></div>
                                                                    
                                        </div>

                                        <?php 
                                            $idVaksinTest++; } } 
                                        ?>

                                        <div class="col-sm-12 form-group" id="vaccineTestContent_<?= $index ?>" ></div>

                                    </div>
                                </div>
                            <?php $index++; }  ?>


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
                                
                                            <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->web=='t'?'checked':'' ?> >Web &nbsp;&nbsp; 
                                        
                                        </div>
                                    </div>
                                </div>                        
                                
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->mobile=='t'?'checked':'' ?>>Mobile Reservasi &nbsp;&nbsp
                                        </div>
                                    </div>
                                </div>       

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->ifcs=='t'?'checked':'' ?> >IFCS &nbsp;&nbsp; 
                                        </div>
                                    </div>
                                </div>       
                                
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->b2b=='t'?'checked':'' ?> >B2B &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='pos_vehicle' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->pos_vehicle=='t'?'checked':'' ?> >Pos KND &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='pos_passanger' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->pos_passanger=='t'?'checked':'' ?> >Pos PNP &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='mpos' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->mpos=='t'?'checked':'' ?>>M Pos &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='vm' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->vm=='t'?'checked':'' ?> >VM &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='verifikator' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->verifikator=='t'?'checked':'' ?> >Verifikator &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>    
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->web_cs=='t'?'checked':'' ?> >Web CS &nbsp;&nbsp; 

                                        
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

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">

                                            <input type="checkbox" class="allow" name='pedestrian' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->pedestrian=='t'?'checked':'' ?> >Pejalan Kaki &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                        
                                            <input type="checkbox" class="allow" name='vehicle' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->vehicle=='t'?'checked':'' ?> >Kendaraan &nbsp;&nbsp; 

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

                                            <input type="checkbox" class="allow" name='vaccineActive' data-checkbox="icheckbox_flat-grey" value="1" <?= $detail->vaccine_active=='t'?'checked':'' ?> >Vaksin Aktif &nbsp;&nbsp; 

                                        
                                        </div>
                                    </div>
                                </div>   -->
                                
                                <div class="col-sm-6 form-group ">
                                    <div class="input-group">
                                        <div class="icheck-inline">                                    

                                            <input type="checkbox" class="allow" name='testVaccineActive' data-checkbox="icheckbox_flat-grey" value="1" value="1" <?= $detail->test_covid_active=='t'?'checked':'' ?> >Tes Covid Aktif &nbsp;&nbsp; 


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>        
                        


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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

        let idAddMinAge=parseInt(`<?= $countMinAge; ?>`);
        // let idAddMinAge=1;
        $("#addMinAge").click(function(){
            
            idAddMinAge = myData.addMinAge(idAddMinAge);
            idAddMinAge += 1;
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