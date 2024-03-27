
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
    .bootstrap-tagsinput  { width:100% !important; }
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #fff !important;
    }
    div.pac-container {
         z-index: 1050 !important;
    }
    .fullscreen-pac-container[style]{
    z-index: 2547483647 !important;
    top:50px !important;
    }
    .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 40px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #pac-input {
        background-color: #fff;
        padding: 0 11px 0 13px;
        width: 400px;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        text-overflow: ellipsis;
    }
    #pac-input:focus {
        border-color: #4d90fe;
        margin-left: -1px;
        padding-left: 14px;
        /* Regular padding-left + 1. */
        width: 401px;
    }
    .pac-container {
        font-family: Roboto;
    }
    #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
    }
    #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }
   
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('radius/rms/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3 form-group">                            
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port,""," class=' form-control select2' id='port' required " ); ?>                          
                        </div>

                        <div class=" col-sm-3 form-group">
                            <label>Tanggal Mulai <span class="wajib">*</span></label>
                            <input type="text" name="startDate" class="form-control"  placeholder="Tanggal Mulai" required id="dateFrom" readonly>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir <span class="wajib">*</span></label>
                            <input type="text" name="endDate" class="form-control"  placeholder="Tanggal Akhir" readonly required id="dateTo">                                
                        </div>
                        <div class="col-sm-3 form-group">                            
                            <label>Tanggal Aktif <span class="wajib">*</span></label>
                                <input type="text" name="activeDate" class="form-control"  placeholder="Tanggal Aktif" required id="activeDate" readonly>                            
                        </div>                                         

                        <div class="col-sm-12 form-group "></div>

                        <div class=" col-sm-3 form-group">
                            <label>Radius <span class="wajib">*</span></label>
                            <input type="text" name="radius" id="radius" class="form-control" value="0"  placeholder="Radius" required onkeypress='return isNumberKey(event)'>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Radius Tipe <span class="wajib">*</span></label>
                            <select name="radiusType" id="radiusType" class="form-control select2" required>
                                <!-- <option value="">Pilih</option> -->
                                <option value="<?= $this->enc->encode(1)?>" title="KM">KM (Kilometer)</option>
                                <option value="<?= $this->enc->encode(2)?>" title="M">M (Meter)</option>
                            </select>
                                
                        </div>                           
                                    
                        <div class="col-sm-3 form-group">
                            <label>Latitude <span class="wajib">*</span></label>
                            <input type="text" name="latitude" class="form-control"  placeholder="Latitude" id="lat" required>                                
                        </div> 
                        <div class=" col-sm-2 form-group">
                            <label>Longitude <span class="wajib">*</span></label>
                            <input type="text" name="longitude" class="form-control"  placeholder="Longitude" id="long" required>
                        </div>                       

                        <div class="col-sm-1 form-group">
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-primary mt-ladda-btn ladda-button add-url-image" style="margin-top: 25px" data-style="zoom-in" id="search" >
                                    <span class="ladda-label"><i class="fa fa-search-plus"></i> Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>                                
                        </div> 
                                              
                        <div class="col-sm-12 form-group" ></div>     
                        <div class=" col-sm-3 form-group">
                            <label>Layanan <span class="wajib">*</span></label>
                                <p>
                                    <input type="checkbox" name="servicePnp" data-id="pnp" class="allow3"  data-checkbox="icheckbox_flat-grey" value="t" ><?= $service[0]->name ?> &nbsp;&nbsp; 
                                </p>
                                <p>
                                    <input id="checkKnd" type="checkbox" name="serviceKnd" data-id="knd" class="allow3"  data-checkbox="icheckbox_flat-grey" value="t" ><?= $service[1]->name ?> &nbsp;&nbsp; 
                                </p>
                                <span id="showVehicleClass"></span>
                                <!-- <div class="erlab"></div> -->                                
  
                        </div>                        
                        <div class="col-md-3 col-sm-3" >
                            <label>Channel <span class="wajib">*</span></label>
                            <div class="icheck-inline ">                 
                                <p><input type="checkbox" name='channel[0]' class="allow2" data-id="web"  id="webChecked" data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("web"); ?>" >WEB &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[1]' class="allow2" data-id="mobile" id="mobileChecked" data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("mobile"); ?>" >MOBILE &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[2]' class="allow2"  data-id="ifcs"  id="ifcsChecked" data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("ifcs"); ?>" >IFCS &nbsp;&nbsp; </p>
                            </div>                        

                        </div>         

                        <div class="col-sm-6 ">    
                            <div class="form-group">
                                <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                                <div id="map" style="widthr:600px; height:450px; margin-bottom:15px;"></div>
                            </div>                                
                        </div>

                        <div class="col-sm-12 ">    
                            <div id="expWeb"></div>
                            <div id="expIfcs"></div>
                        </div>
                                                
                        <div class=" col-sm-12 "><?php echo form_close(); ?></div> 
                        <div class=" col-sm-12 ">
                            <div style="background-color:#e1f0ff; padding:10px;">
                                <div class="portlet box blue-madison">
                                    <div class="portlet-title">
                                        
                                        <div class="caption">User yang akan dikecualikan </div>
                                        <div class="pull-right btn-add-padding"></div>
                                    </div>
                                    <div class="portlet-body">

                                        <div style="color:red; font-style:italic ">User Pengecualian tidak wajib ditambahkan,  jika tidak ada user yang dikecualikan</div>

                                        <div class="kt-portlet">
                                            <div class="kt-portlet__head">

                                            </div>
                                            <p></p>
                                            <div class="kt-portlet__body">
                                                <ul class="nav nav-tabs " role="tablist">
                                                    <li class="nav-item active">
                                                            <a class="label label-primary " data-toggle="tab" href="#tab1">User Web dan Mobile</a>
                                                    </li>
                                                    <li class="nav-item ">
                                                            <a class="label label-primary " data-toggle="tab" href="#tab2">User IFCS</a>
                                                    </li>                                                

                                                </ul>
                                                <div class="tab-content " >
                                                    <div class="tab-pane active" id="tab1" role="tabpanel" > 
                                                      
                                                        <div class="row">
                                                            <div  class="formHiddenWeb">
                                                                <div class="col-sm-12 form-group"  >   
                                                                    <input type="hidden" id="idDataWeb" name="idDataWeb" value="1">                         
                                                                    <div id="inputExceptUserDivWeb"></div>
                                                                </div>
                                                                <div class="col-sm-6 form-group" id='selectUserWeb' >
                                    
                                                                    <div class="portlet box blue-madison">
                                                                        <div class="portlet-title">
                                                                            
                                                                            <div class="caption">User dibatasi</div>
                                                                            <div class="pull-right btn-add-padding"></div>
                                                                        </div>
                                                                        <div class="portlet-body">                        
                                                                            <table class="table" id="tableUserLimitedWeb">
                                                                                <thead >
                                                                                    <tr>
                                                                                        <th>EMAIL USER</th>
                                                                                        <th>
                                                                                            <div class='btn btn-danger transferData pull-right' title='Pindah Ke Pengecualian'  id="toAllExceptWeb" >
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
                                                                            
                                                                            <div class="caption">User pengecualian</div>
                                                                            <div class="pull-right btn-add-padding"></div>
                                                                        </div>
                                                                        <div class="portlet-body">                        
                                                                            <table class="table" id="tableUserLimitedExceptWeb">
                                                                                <thead id="headerPengecualianWeb" >                                                                      
                                                                                    <tr>
                                                                                        <th>
                                                                                            <div class='btn btn-danger transferData pull-left' title='Pindah Ke Pengecualian'  id="toAllLimitWeb" >
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


                                                    <!-- Except IFCS -->
                                                    <div class="tab-pane " id="tab2" role="tabpanel" >
                                                        <!-- <div style="padding-top: 10px; padding-right: 30px; padding-bottom: 35px; padding-left: 80px;">   
                                                            <div class="pull-right btn-add-padding add-user-email" id="add-user-ifcs-email"></div>
                                                        </div>             -->
                                                        <div class="row">
                                                            <div  class="formHiddenIfcs">
                                                            <div class="col-sm-12 form-group"  >   
                                                                <input type="hidden" id="idData" name="idData" value="1">                         
                                                                <div id="inputExceptUserDiv"></div>
                                                            </div>
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
                                                                        
                                                                        <div class="caption">User pengecualian</div>
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

                                                </div>      
                                            </div>
                                        </div>
                                                                
                                    </div>
                                </div>            
                            </div>

                        </div>
                        <!-- <div class="col-md-6 col-sm-6 form-group" id="showVehicleClass"></div>                         -->
                                    
                        <div class="col-sm-12"> </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            
        </div>
    </div>
</div>

<?php include "modal_user.php" ?>
<?php include "modal_user_ifcs.php" ?>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>

<script type="text/javascript">
    let setChannel = new Set();
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $(`#saveBtn`).on("click", function(event){

            const isTrue = $('#ff').valid();
            if(isTrue == true)
            {
                $('#ff').submit();
            }
        })

        // agar modal bisa di clik field inputnya
        $('.mfp-wrap').removeAttr('tabindex')


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });            
        });


        $('.allow3').iCheck({
            checkboxClass: 'icheckbox_square-blue service-icheck',
            radioClass: 'icheckbox_square-blue',
        });   

        $('#checkKnd').on('ifChecked ifUnchecked', function(event){
            let valueData = $(this).data("id")
            const vehicleClass = JSON.parse(`<?php echo json_encode($vehicleClass); ?>`);
            let $html = "";
            if (event.type == `ifChecked`) {
                
                $html = myData.getFormVehicleClass(vehicleClass);
                // console.log($html);
                $(`#showVehicleClass`).html($html);
                $( `#contentVehicleClass` ).slideDown( "slow" );
            } 
            else
            {
                $( `#contentVehicleClass` ).slideUp( "slow",function(){ $( `#contentVehicleClass` ).remove(); }  );
            }

            $('.allow').iCheck({
                checkboxClass: 'icheckbox_square-blue vehicle-class-icheck',
                radioClass: 'icheckbox_square-blue',
            });   
            
            $('#allDataCheck').on('ifChecked ifUnchecked', function(event){

                if (event.type == `ifChecked`) {
                    $(".vehicle-class-icheck").iCheck("check");
                } 
                // else {
                //     $(".vehicle-class-icheck").iCheck("uncheck");
                // }
            });  

        });        

        $('.allow2').iCheck({
                checkboxClass: 'icheckbox_square-blue channel-icheck',
                radioClass: 'icheckbox_square-blue',
            });   
            
        $('#allDataCheckChannel').on('ifChecked ifUnchecked', function(event){
            if (event.type == `ifChecked`) {
                $(".channel-icheck").iCheck("check");
            } else {
                $(".channel-icheck").iCheck("uncheck");
            }
        });

        $('.formHiddenIfcs').hide();
        $('.formHiddenWeb').hide();

        
        $('.allow2').on('ifChecked ', function(){
       
            if ($(this).is(":checked") && ($(this).attr("id") == 'ifcsChecked')) {
               
                $('.formHiddenIfcs').show();
                //  myData.tableUserExcept();
            }
            else if(($(this).attr("id") == 'mobileChecked') )
            {
                // console.log('oke')
                $('.formHiddenWeb').show();
            }
            else 
            {
                $('.formHiddenWeb').show();
            }
         

        });

        $('.allow2').on('ifUnchecked ', function(){
       
            if ($(this).attr("id") == 'ifcsChecked') {
                // console.log('bggt');
                $('.formHiddenIfcs').hide();
                // myData.toExceptNull(1);
                // console.log();
            
            }
            else if($('#webChecked').is(':checked') == true ||  $('#mobileChecked').is(':checked') == true)
            {
       
                $('.formHiddenWeb').show();

            }
            else
            {
                $('.formHiddenWeb').hide();

            }

        });

            
        $('#activeDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

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
        
        $('#modalUserWeb').on('shown.bs.modal', function (e) {
            $(document).off('focusin.modal');
        })

        myData.tableUserLimited();
        myData.tableUserExcept();
        myData.tableUserLimitedWeb();
        myData.tableUserExceptWeb();

        //Except IFCS
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
        

        //Except web & apps
        $("#toAllExceptWeb").on("click", function(){
            arrayUserIdExceptWeb=[];
            $("#idDataWeb").val(1)

        })

        $("#toAllExceptWeb").on("click", function(){
            arrayUserIdExceptWeb=[];
            $("#idDataWeb").val(0)

            myData.reloadTableUserLimitedWeb(); 
            myData.reloadTableUserExceptWeb(); 

        })   
        
        $("#toAllLimitWeb").on("click", function(){
            arrayUserIdExceptWeb=[];
            $("#idDataWeb").val(1)

            myData.reloadTableUserLimitedWeb(); 
            myData.reloadTableUserExceptWeb(); 
        })  
                
    })

    var map;
    initMap()
    var markers = [];

    $('#search').click(function() {
        
        let getLat =$('#lat').val();
        let getLong =$('#long').val();
        let getRadius =$('#radius').val();
        let getRadiusType =$("#radiusType option:selected" ).attr('title');
        let input = document.getElementById("pac-input");

        if(!getLat && !getLong){
            var midPoint = {lat: -6.21462, lng: 106.84513};
        }else{
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
        }
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,                      
            center: midPoint,
            mapTypeId: "roadmap",

        });
        addSearch(input);
        
        map.addListener('click', function(event) {
            // console.log(markers.length)
            if (markers.length >= 1) {
                deleteMarkers();
            }

            addMarker(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('long').value =  event.latLng.lng();
        });

        // initMap(getLat, getLong);

        if (getRadiusType == 'M'){
            var radiusCount = 1;
        }else{
            var radiusCount = 1000;
        }
        
        var midPoint = {};
        
        if(getLat && getLong && getRadius != 0){
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
            var totRadius = getRadius * radiusCount;

            var antennasCircle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                // strokeWeight: 2,
                strokeWeight: 0,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                map: map,
                clickable: false,
                // center: midPoint,
                center: new google.maps.LatLng(getLat, getLong),
                radius: totRadius

            });
        
        map.fitBounds(antennasCircle.getBounds());
        addMarker(midPoint);
        
        }
        
    });

    function initMap() {

        let midPoint = {lat: -6.21462, lng: 106.84513};
        let input = document.getElementById("pac-input");

        // console.log(midPoint)
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,                      
            center: midPoint,
            mapTypeId: "roadmap",

        });
        addSearch(input);
        
        map.addListener('click', function(event) {
            // console.log(markers.length)
            if (markers.length >= 1) {
                deleteMarkers();
            }

            addMarker(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('long').value =  event.latLng.lng();
        });
      
    }

    function addSearch(input) {

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
        /** @type {HTMLInputElement} */ (input));

        google.maps.event.addListener(searchBox, 'places_changed', function () {

            var places = searchBox.getPlaces();

            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }

            markers = [];
            var bounds = new google.maps.LatLngBounds();

            for (var i = 0, place; place = places[i]; i++) {

                var marker = new google.maps.Marker({
                    map: map,
                    draggable:true,
                    title: place.name,
                    position: place.geometry.location
                });
                
                var latInput = document.getElementsByName('latitude')[0];
                var lngInput = document.getElementsByName('longitude')[0];

                latInput.value = place.geometry.location.lat()
                lngInput.value = place.geometry.location.lng();
                google.maps.event.addListener(marker, 'dragend', function (e) {
                    latInput.value = e.latLng.lat();
                    lngInput.value = e.latLng.lng();
                });

                markers.push(marker);

                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
        });

    }

    // Add marker 
    function addMarker(location) {
        
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
        markers.push(marker);
    }

    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    // Removes markers 
    function clearMarkers() {
        setMapOnAll(null);
    }

    // Deletes all markers 
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }

    document.onfullscreenchange = function ( event ) {
        let target = event.target;
        let pacContainerElements = document.getElementsByClassName("pac-container");
        if (pacContainerElements.length > 0) {
            let pacContainer = document.getElementsByClassName("pac-container")[0];
            if (pacContainer.parentElement === target) {
                document.getElementsByTagName("body")[0].appendChild(pacContainer);
                pacContainer.className += pacContainer.className.replace("fullscreen-pac-container", "");
            } else {
                target.appendChild(pacContainer);
                pacContainer.className += " fullscreen-pac-container";
            }
        }
    }; 


</script>