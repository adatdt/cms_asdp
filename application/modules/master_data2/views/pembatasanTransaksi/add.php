<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />


<style type="text/css">

    .form-kendaraan {
        font-size: 1rem;
        font-weight: unset;
        line-height: 5.5;
        display: grid;
        grid-template-columns: 2em auto;
        gap: 1.5em;
        font-size: 14px;
    }

    .form-kendaraan + .form-kendaraan {
      margin-top: 1em;
    }
    

    input[type="checkbox"] {
        -webkit-appearance: none;
        appearance: none;
        background-color: var(--form-background);
        margin: bottom;
        font: inherit;
        color: currentColor;
        width: 2.50em;
        height: 2.15em;
        border: 0.15em solid currentColor;
        border-radius: 0.15em;
        transform: translateY(-0.075em);
        display: grid;
        place-content: center;
    }

    input[type=checkbox], input[type=radio] {
        margin: 25px 0 0;
        margin-top: 1px\9;
        line-height: normal;
    }

    input[type="checkbox"]::before {
      content: "";
      width: 0.65em;
      height: 0.65em;
      clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
      transform: scale(0);
      transform-origin: bottom left;
      transition: 120ms transform ease-in-out;
      box-shadow: inset 1em 1em var(--form-control-color);
      /* Windows High Contrast Mode */
      background-color: CanvasText;
    }

    input[type="checkbox"]:checked::before {
      transform: scale(1);
    }

    input[type="checkbox"]:focus {
      outline: max(2px, 0.15em) solid currentColor;
      outline-offset: max(2px, 0.15em);
    }

    input[type="checkbox"]:disabled {
      --form-control-color: var(--form-control-disabled);

      color: var(--form-control-disabled);
      cursor: not-allowed;
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
            <?php echo form_open('master_data2/pembatasanTransaksi/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                          <input type="hidden" name="valGolongan" id="valGolongan">     
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" id="dateFrom" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly style="background-color: #ffffff;">                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" id="dateTo" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly  style="background-color: #ffffff;" >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Range Waktu Pembatasan<span class="wajib">*</span></label> 
                            <?= form_dropdown("limitType",$limitType,'','  class="form-control select2" id="limitType" required ' ) ?>                     
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Batas Jumlah Trx<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Batas Jumlah Trx"  >                     
                        </div>

                        <div class="col-sm-12 form-group "></div>

                        <div class="col-sm-3 form-group ">
                            <div class="input-group">  
                             <label class="form-kendaraan">
                                <input type="checkbox" id="pejalanKaki" name='pejalanKaki' value="1">
                                Pejalan kaki
                              </label>                     
                            </div>
                        </div>

                        <div class="col-sm-3 form-group ">
                            <div class="input-group">
                                <label class="form-kendaraan">
                                     <input class="jenisKendaraan" type="checkbox" id="kendaraan" name="kendaraan" value="2">
                                    Kendaraan
                                </label> 
                                 <div id="inputGolonganValue"></div>    
                            </div>
                        </div> 

                        <div class="col-sm-3 form-group ">
                            <div class="input-group">
                                <label class="form-kendaraan">                                   
                                    <input type="checkbox" class="allow" id="isCustom" name='isCustom' data-checkbox="icheckbox_flat-grey" value="1">
                                    Custom Range Waktu
                                </label> 
                                    <span id="inputCustomValue"></span>                        
                            </div>
                        </div> 

                        <div class="col-sm-12 form-group"  >   
                            <input type="hidden" id="idData" name="idData" value="1">                         
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
                                    
                                    <div class="caption">User pengecualian</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimited">
                                        <thead >
                                            <tr>
                                                <th>EMAIL USER</th>
                                                <th>
                                                    <div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  id="toAllExcept" >
                                                        Semua Data <i class='fa fa-arrow-right ' aria-hidden='true'></i>
                                                    </div>
                                                    <!-- AKSI -->
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
                                    
                                    <div class="caption">User dibatasi</div>
                                    <div class="pull-right btn-add-padding"></div>
                                </div>
                                <div class="portlet-body">                        
                                    <table class="table" id="tableUserLimitedExcept">
                                        <thead id="headerPengecualian" >                                                                      
                                            <tr>
                                                <th>
                                                    <div class='btn btn-danger transferData pull-left' title='Pindah Ke Pengecualian'  id="toAllLimit" >
                                                    <i class='fa fa-arrow-left ' aria-hidden='true'></i> Semua Data
                                                    </div>

                                                    <!-- AKSI -->

                                                </th>
                                                <th>EMAIL USER</th>
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
        
        
        // $('.allow').iCheck({
        //     // checkboxClass: 'icheckbox_square-blue',
        //     // radioClass: 'icheckbox_square-blue',
        // })
        // .on('ifChanged', function(e) {
        //     // Get the field name
        //     var isChecked = e.currentTarget.checked;
        //     let inputDataHtml ="";
        //     if (isChecked == true) {
        //         inputDataHtml +=`                    
        //             <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Waktu yang diinginkan"  >      

        //         `
        //     }

        //     $("#inputCustomValue").html(inputDataHtml);
        // });

        $( ".allow" )
          .change(function(e) {
          var isChecked = e.currentTarget.checked;
            let inputDataHtml ="";
            if (isChecked == true) {
                inputDataHtml +=`                    
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Range Waktu"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
            


          })

          .change(); 


        $( ".jenisKendaraan" )
          .change(function(e) {
           var isChecked = e.currentTarget.checked;
           let inputDataHtml ="";
            if (isChecked == true) {
                
                inputDataHtml +=`

                <?= form_dropdown("golongan[]",$golongan,'',' id="golongan" required class="form-control select2" multiple="multiple" ' ) ?>


                `
            }

            $("#inputGolonganValue").html(inputDataHtml);
            
            $(document).ready(function(){
            $('.select2').select2();
            });

            $('#golongan').change(function() {
              var selectedValues = $(this).find('option:selected(:selected)').map(function() {
                return this.value;
              }).get();

              $("#valGolongan").val(selectedValues);
              
              console.log(selectedValues);     
            });


          })

          .change(); 

            


        myData.tableUserLimited();
        myData.tableUserExcept();
    
        $("#toAllExcept").on("click", function(){
            arrayUserIdExcept=[];
            $("#idData").val(1)

        })

        $("#toAllExcept").on("click", function(){
            arrayUserIdExcept=[];
            $("#idData").val(0)

            myData.reloadTableUserLimited(); 
            myData.reloadTableUserExcept(); 

        })   
        
        $("#toAllLimit").on("click", function(){
            arrayUserIdExcept=[];
            $("#idData").val(1)

            myData.reloadTableUserLimited(); 
            myData.reloadTableUserExcept(); 
        })           
        


    })
</script>