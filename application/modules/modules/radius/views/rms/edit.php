
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
            <?php echo form_open('radius/rms/action_edit', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                     <div class="row">

                        <div class="col-sm-3 form-group">
                            <div class="mt-element-ribbon bg-grey-steel">
                                <div class="ribbon ribbon-color-primary uppercase">KODE RMS</div>
                                <div class="ribbon-content " style="height:50px; "  ><b><?= $detailHeader->rms_code; ?></b></div>
                            </div>
                        </div>

                       <div class=" col-sm-12 form-group"></div>
                        <div class="col-sm-3 form-group">                            
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <?= form_dropdown("port",$port["data"],$port["selected"]," class=' form-control select2' id='port' required " ); ?>         
                            <input type="hidden" name="rmsCode" required  value="<?= $code ?>">                 
                        </div>

                        <div class=" col-sm-3 form-group">
                            <label>Tanggal Mulai <span class="wajib">*</span></label>
                            <input type="text" name="startDate" class="form-control"  placeholder="Tanggal Mulai" required id="dateFrom" readonly value="<?= date("Y-m-d H:i", strtotime($detailHeader->start_date)) ?>">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir <span class="wajib">*</span></label>
                            <input type="text" name="endDate" class="form-control"  placeholder="Tanggal Akhir" readonly required id="dateTo" value="<?= date("Y-m-d H:i", strtotime($detailHeader->end_date)); ?>">                                
                        </div>
                        <div class="col-sm-3 form-group">                            
                            <label>Tanggal Aktif <span class="wajib">*</span></label>
                                <input type="text" name="activeDate" class="form-control"  placeholder="Tanggal Aktif" required id="activeDate" readonly value="<?= date("Y-m-d H:i", strtotime($detailHeader->reservation_date)); ?>">                            
                        </div>                                         

                        <div class=" col-sm-12 "></div>   
                        <div class=" col-sm-3 form-group">
                            <label>Radius <span class="wajib">*</span></label>
                            <input type="text" name="radius" id="radius" class="form-control"  placeholder="Radius" required onkeypress='return isNumberKey(event)' value="<?= $detailHeader->radius ?>">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Radius Tipe <span class="wajib">*</span></label>
                            <select name="radiusType" id="radiusType" class="form-control select2" required>
                                <!-- <option value="">Pilih</option> -->
                                <option value="<?= $this->enc->encode(1)?>" <?= $detailHeader->radius_type==1?"selected":"" ?> title="KM">KM (Kilometer)</option>
                                <option value="<?= $this->enc->encode(2) ?>" <?= $detailHeader->radius_type==2?"selected":"" ?> title="M">M (Meter)</option>
                            </select>
                                
                        </div>                           
                        <div class="col-sm-3 form-group">
                            <label>Latitude <span class="wajib">*</span></label>
                            <input type="text" name="latitude" class="form-control"  placeholder="Latitude" id="lat" required value="<?= $detailHeader->latitude ?>">                                
                        </div>
                        <div class=" col-sm-2 form-group">
                            <label>Longitude <span class="wajib">*</span></label>
                            <input type="text" name="longitude" class="form-control"  placeholder="Longitude" id="long" required value="<?= $detailHeader->longitude ?>" >
                        </div>                        
                        <div class="col-sm-1 form-group">
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-primary mt-ladda-btn ladda-button add-url-image" style="margin-top: 25px" data-style="zoom-in" id="search" >
                                    <span class="ladda-label"><i class="fa fa-search-plus"></i> Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>                                
                        </div>
                        <div class=" col-sm-12 "></div>       
                        <div class=" col-sm-3 form-group">
                            <label>Layanan <span class="wajib">*</span></label>
                                <p>
                                    <input type="checkbox" name="servicePnp" data-id="pnp" class="allow3"  data-checkbox="icheckbox_flat-grey" value="t" <?= $detailHeader->is_pedestrian=='t'?"checked":""; ?>  ><?= $service[0]->name ?> &nbsp;&nbsp; 
                                </p>
                                <p>
                                    <input id="checkKnd" type="checkbox" name="serviceKnd" data-id="knd" class="allow3"  data-checkbox="icheckbox_flat-grey" value="t" <?= $detailHeader->is_vehicle=='t'?"checked":""; ?> ><?= $service[1]->name ?> &nbsp;&nbsp; 
                                </p>
                                <span id="showVehicleClass"></span>
                                <!-- <div class="erlab"></div> -->                                
  
                        </div>                        
                        <div class="col-md-3 col-sm-3" >
                            <label>Channel <span class="wajib">*</span></label>
                            <div class="icheck-inline ">                 
                                <p><input type="checkbox" name='channel[0]' class="allow2" data-id="web"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("web"); ?>" <?= @$channel["web"] ?>>WEB &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[1]' class="allow2" data-id="mobile"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("mobile"); ?>" <?= @$channel["mobile"] ?> >MOBILE &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[2]' class="allow2"  data-id="ifcs"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("ifcs"); ?>" <?= @$channel["ifcs"] ?>>IFCS &nbsp;&nbsp; </p>
                            </div>                        

                        </div>         
                         <!-- <div class="col-sm-6 ">    
                            <div class="form-group">
                                <iframe
                                    width="600"
                                    height="450"
                                    style="border:0"
                                    loading="lazy"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps/embed/v1/place?key=API_KEY
                                        &q=Space+Needle,Seattle+WA">
                                </iframe>                     
                            </div>                                
                        </div> -->
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
                        <!--                         
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
                                                        <div class="pull-right btn-add-padding add-user-email" id="add-user-web-email"> </div>                                                    
                                                        <div class="row">
                                                            <div class="col-md-12" >
                                                                <p></p>
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="tableWebUser" style=" width: 50%;">
                                                                    <thead>
                                                                        <tr>
                                                                            <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                                <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                    <div class="input-group select2-bootstrap-prepend">
                                                                                        User Web dan Mobile yang akan dikecualikan 
                                                                                    </div>

                                                                                    <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                                <div>
                                                                            
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>NO</th> 
                                                                            <th>EMAIL USER</th>
                                                                            <th>
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                AKSI
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            </th>
                                                                        </tr>
                                                                    </thead>                                    
                                                                </table>      
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="tab-pane " id="tab2" role="tabpanel" >
                                                        <div class="pull-right btn-add-padding add-user-email" id="add-user-ifcs-email"></div>            
                                                        <div class="row">
                                                            <div class="col-md-12" >
                                                                <p></p>
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="tableIfcs" style=" width: 50%;">
                                                                    <thead>
                                                                        <tr>
                                                                            <th colspan=8 style="text-align:left; padding-left:14px;  "  >
                                                                                <div class="col-sm-12 form-inline" style="margin-top:10px;">
                                                                                    <div class="input-group select2-bootstrap-prepend">
                                                                                        User IFCS yang akan dikecualikan 
                                                                                    </div>

                                                                                    <div class="input-group select2-bootstrap-prepend pull-right"> </div>
                                                                                <div>
                                                                            
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>NO</th> 
                                                                            <th>EMAIL USER</th>
                                                                            <th>
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                AKSI
                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            </th>
                                                                        </tr>
                                                                    </thead>                                    
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
                        -->
                        <!-- <div class="col-sm-12"> </div> -->
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            
            <?php echo createBtnForm('Update') ?>
        </div>
    </div>
</div>

<?php include "modal_user.php" ?>
<?php include "modal_user_ifcs.php" ?>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    const vehicleClass = JSON.parse(`<?php echo json_encode($vehicleClass); ?>`);
    const selectedVehicle = JSON.parse(`<?php echo json_encode($selectedVehicle); ?>`);
    let setChannel = new Set();
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        // $(`#saveBtn`).on("click", function(){
        //     $('#ff').valid();
        // })
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

        // untuk menampilkan data
        const isChecked = $(`#checkKnd`).is(":checked");
        if(isChecked==true)
        {
            $html = myData.getFormVehicleClass(vehicleClass, selectedVehicle);
            // console.log($html);
            $(`#showVehicleClass`).html($html);
            $( `#contentVehicleClass` ).slideDown( "slow" );

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
        }

        $('#checkKnd').on('ifChecked ifUnchecked', function(event){
            let valueData = $(this).data("id")            
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
                else {
                    $(".vehicle-class-icheck").iCheck("uncheck");
                }
            });  

        });        

        $('.allow2').iCheck({
                checkboxClass: 'icheckbox_square-blue channel-icheck',
                radioClass: 'icheckbox_square-blue',
            });   
            
        $('#allDataCheckChannel').on('ifChecked ifUnchecked', function(event){
            if (event.type == `ifChecked`) {
                $(".channel-icheck").iCheck("check");
            } 
            else {
                $(".channel-icheck").iCheck("uncheck");
            }
        });

        $('.allow2').on('ifChecked ifUnchecked', function(event){
                
            const getId = $(this).attr("data-id");
            if (event.type == `ifChecked`) {
                setChannel.add(getId);
            } else {
                setChannel.delete(getId);
            }
            
            $(".add-user-email").html("");
            let btnAddEmail ="";
            let idBtnAddEmail ="";
            setChannel.forEach(element => {                
                if(element == "web" || element == "mobile" )
                {
                    btnAddEmail = `<button type="button" class="btn btn-sm btn-warning pull-right" data-toggle="modal" data-target="#modalUserWeb"><i class="fa fa-plus"></i>Tambah</button>`
                    idBtnAddEmail =`add-user-web-email`                    
                }
                else
                {
                    btnAddEmail = `<button type="button" class="btn btn-sm btn-warning pull-right" data-toggle="modal" data-target="#modalUserIfcs"><i class="fa fa-plus"></i>Tambah</button>`
                    idBtnAddEmail =`add-user-ifcs-email`
                }
                $(`#${idBtnAddEmail}`).html(btnAddEmail);
               
            });
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
            // startDate: new Date()
            startDate:`<?= date("Y-m-d H:i", strtotime($detailHeader->start_date)) ?>`

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
                
    })
    
    var map;
    var markers = [];

    let getLat =$('#lat').val();
    let getLong =$('#long').val();

    initMapEdit(getLat, getLong);

    $('#search').click(function() {

        let getLat =$('#lat').val();
        let getLong =$('#long').val();
        let getRadius =$('#radius').val();
        let getRadiusType =$("#radiusType option:selected" ).attr('title');  

        initMapEdit(getLat, getLong);

    });

    function initMapEdit(getLat="-6.21462", getLong="106.84513") {
        
        let lat =  parseFloat(getLat);
        let long =  parseFloat(getLong);
        let radius =$('#radius').val();
        let getRadiusType =$("#radiusType option:selected" ).attr('title');
        let input = document.getElementById("pac-input");

        var midPoint = {lat: lat, lng: long};
        // console.log(midPoint)
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,                      
            center: midPoint,

        });
        addSearch(input);

        if (getRadiusType == 'M'){
            var radiusCount = 1;
        }else{
            var radiusCount = 1000;
        }                 

        var midPoint = {lat: lat, lng: long};
        var totRadius = radius * radiusCount;
        var cntr = new google.maps.LatLng(lat, long);
        
        // console.log(cntr)
        var option = {
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            // strokeWeight: 2,
            strokeWeight: 0,
            fillColor: "#FF0000",
            fillOpacity: 0.35,
            map: map,
            clickable: false,
            center: cntr,
            radius: totRadius

        }

        var antennasCircle = new google.maps.Circle(option);
        antennasCircle.getBounds()     
        addMarker(midPoint);          
        
        map.addListener('click', function(event) {       

            if (markers.length >= 1) {
                deleteMarkers();
            }

            addMarker(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('long').value =  event.latLng.lng();
    
        });            

    }

    function addSearch_bck(input) {

        const searchBox = new google.maps.places.SearchBox(input);
        // console.log(input)
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        let markers = [];

        searchBox.addListener("places_changed", () => {
            if (markers.length >= 1) {
                deleteMarkers();
            }

            const places = searchBox.getPlaces();

            if (places.length == 0) {
            return;
            }

            markers.forEach((marker) => {
                // console.log( marker)
            marker.setMap(null);
            });
            markers = [];

            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }
           console.log(place)
            // markers.push(
            //     new google.maps.Marker({
            //     map,
            //     title: place.name,
            //     position: place.geometry.location,
            //     }),
            // );
            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }

            });
            map.fitBounds(bounds);
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
        // console.log(location)
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