
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
    .bootstrap-tagsinput  { width:100% !important; }
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #fff !important;
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

                        <div class=" col-sm-12 "></div>   
                        <div class=" col-sm-3 form-group">
                            <label>Radius <span class="wajib">*</span></label>
                            <input type="text" name="radius" id="radius" class="form-control"  value="0" placeholder="Radius" required onkeypress='return isNumberKey(event)'>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Radius Tipe <span class="wajib">*</span></label>
                            <select name="radiusType" id="radiusType" class="form-control select2" required>
                                <!-- <option value="">Pilih</option> -->
                                <option value="<?= $this->enc->encode(1)?>" title="KM">KM (Kilometer)</option>
                                <option value="<?= $this->enc->encode(2)?>" title="M">M (Meter)</option>
                            </select>
                                
                        </div>                           
                                          
                        <div class=" col-sm-2 form-group">
                            <label>Longitude <span class="wajib">*</span></label>
                            <input type="text" name="longitude" class="form-control"  placeholder="Longitude" id="long" required>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label>Latitude <span class="wajib">*</span></label>
                            <input type="text" name="latitude" class="form-control"  placeholder="Latitude" id="lat" required>                                
                        </div>                        
                        <div class="col-sm-2 form-group">
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-primary mt-ladda-btn ladda-button add-url-image" style="margin-top: 25px" data-style="zoom-in" id="search" >
                                    <span class="ladda-label"><i class="fa fa-search-plus"></i> Search</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>                                
                        </div> 
                        <div class=" col-sm-12 "></div>       
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
                                <p><input type="checkbox" name='channel[0]' class="allow2" data-id="web"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("web"); ?>" >WEB &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[1]' class="allow2" data-id="mobile"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("mobile"); ?>" >MOBILE &nbsp;&nbsp; </p>
                                <p><input type="checkbox" name='channel[2]' class="allow2"  data-id="ifcs"  data-checkbox="icheckbox_flat-grey" value="<?= $this->enc->encode("ifcs"); ?>" >IFCS &nbsp;&nbsp; </p>
                            </div>                        

                        </div>         
                        <div class="col-sm-6 ">    
                            <div class="form-group">
                                <div id="map" style="widthr:600px; height:450px;"></div>
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
<script async defer
    src="https://maps.google.com/maps/api/js?key=AIzaSyDk_JhHOVjSy5xU4FnKUXcomihclcuU170&q=Space+Needle,Seattle+WA&sensor=false&callback=initMap">
</script>
<script type="text/javascript">
    let setChannel = new Set();
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $(`#saveBtn`).on("click", function(){
            $('#ff').valid();
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
                } else {
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
            } else {
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
                    btnAddEmail = `<button type="button" class="btn btn-sm btn-warning pull-right" data-toggle="modal" data-backdrop="static" data-target="#modalUserWeb"><i class="fa fa-plus" ></i>Tambah</button>`
                    idBtnAddEmail =`add-user-web-email`                    
                }
                else
                {
                    btnAddEmail = `<button type="button" class="btn btn-sm btn-warning pull-right" data-toggle="modal" data-target="#modalUserIfcs"  data-backdrop="static"><i class="fa fa-plus"></i>Tambah</button>`
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
                
    })
 
    var map;
    var markers = [];

    $('#search').click(function() {

        let getLat =$('#lat').val();
        let getLong =$('#long').val();
        let getRadius =$('#radius').val();
        let getRadiusType =$("#radiusType option:selected" ).attr('title');

        initMap(getLat, getLong);
        
        if (getRadiusType == 'M'){
            var radiusCount = 1;
        }else{
            var radiusCount = 1000;
        }

        if(getLat && getLong && getRadius != 0){
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
            var totRadius = getRadius * radiusCount;
            
            var antennasCircle = new google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                map: map,
                center: midPoint,
                radius: totRadius

            });
        map.fitBounds(antennasCircle.getBounds());
        addMarker(midPoint);
        
        }

    });

    function initMap(getLat, getLong) {

        if(!getLat && !getLong){
            var midPoint = {lat: -6.21462, lng: 106.84513};
        }else{
            var midPoint = {lat: parseFloat(getLat), lng: parseFloat(getLong)};
        }
        console.log(midPoint)

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,                      
            center: midPoint,

        });

        map.addListener('click', function(event) {
            if (markers.length >= 1) {
                deleteMarkers();
            }

            addMarker(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('long').value =  event.latLng.lng();
        });
    }

    // Adds a marker 
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

    // Removes the markers 
    function clearMarkers() {
        setMapOnAll(null);
    }

    // Deletes all markers 
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }
      
</script>