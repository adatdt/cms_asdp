    <style>
    ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
    li  { margin: 5px; padding: 5px; width: 150px; }

    </style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">

                    <div class="table-toolbar">
                        <form action="<?php echo site_url()?>transaction/stc/stc_action" method="post">
                            <div class="row">
                                <div class="col-sm-12 form-inline">

                                    <div class="input-group select2-bootstrap-prepend">
                                        <div class="input-group-addon">Tanggal</div>
                                        <input type="text" name='dateFrom' class="form-control date input-small" id="dateFrom" value="<?php echo date('Y-m-d'); ?>" placeholder="YYYY-MM-DD"  autocomplete="off">
                                    </div>    

                                    <div class="input-group select2-bootstrap-prepend">
                                        <div class="input-group-addon">Tanggal</div>
                                        <select class="form-control select2" placeholder="Pilih" id="port" name="port">
                                            <option value="">Pilih</option>
                                            <?php foreach( $port as $key=>$value) { ?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$port_id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                            <?php } ?>
                                        </select>

                                    </div> 

                                    <div class="input-group ">
                                        <!-- <input type="text" name="total_dock" id="total_dock"> -->

                                        <input name="submit" type="submit" class="btn btn-small btn-primary" />
                                    </div> 

                                </div>

                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <!-- looping jumlah dermaga -->


                        <?php $docks=''; $no=1; if (!empty($data_dock)){ foreach($data_dock as $key=>$value){ 

                            $docks .= '
                            $("#dock-'.$value->id.'").sortable({
                                connectWith: "#sortable_1, #sortable_2, #idle",
                                revert: true,
                                stop: function(event, ui) {
                                    var scheduleId =  $("#dock-'.$value->id.'").sortable("toArray");
                                    anchor = $("#sortable_1").sortable("toArray");
                                    rusak = $("#sortable_2").sortable("toArray");
                                    idle = $("#idle").sortable("toArray");
                                    // console.log("'.$value->name.' : " + dockId)
                                    // console.log("Anchor : " + anchor);
                                    // console.log("Rusak : " + rusak);
                                    // console.log("Idle : " + idle);

                                    $.ajax({
                                        type:"post",
                                        dataType:"json",
                                        data:"anchor="+anchor+"&rusak="+rusak+"&idle="+idle+"&scheduleId="+scheduleId,
                                        url:"'.site_url().'transaction/stc/processing",
                                        success:function(x)
                                        {
                                            console.log(x);
                                        }
                                    });
                                  }
                            });  ';


                            $data_schedule=$this->stc->data_schedule($port_id,$value->id,$dateFrom)->result();
                        ?>
                        <div class="col col-md-4 form-group">
                            <div class="portlet box green" >
                                <div class="portlet-title">
                                    <div class="caption" align="center"><?php echo $value->name; ?> </div>
                                </div>

                                <div class="portlet-body form" style="min-height: 300px">
                                    <div class="form-body">

                                        <ul id="<?php echo "dock-".$value->id ?>" class="sortable" onchange="get_data()">
                                        <?php 
                                        $no_index=1;
                                        foreach($data_schedule as $key=>$value) { ?>
                                            <li class="ui-state-default li_ship" id="<?php echo $value->id ?>" >
                                                <?php echo $value->ship_name; ?> </li>
                                        <?php $no_index++; }?>
                                        </ul>

                                    </div>

                                </div>
                            </div>  
                        </div>
                        <?php } $no++; } ?>
                        <!-- end looping per dernmaga -->

                        <div class="col col-md-4 form-group" >
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Angchor</div>
                                </div>
                                <div class="portlet-body form" style="min-height: 300px">
                                    <div class="form-body">
                                        <ul id="sortable_1">
                                          <li class="ui-state-default" id="1">Item 1</li>
                                          <li class="ui-state-default" id="2">Item 2</li>
                                          <li class="ui-state-default" id="3">Item 3</li>
                                          <li class="ui-state-default" id="4">Item 4</li>
                                          <li class="ui-state-default" id="5">Item 5</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>  
                        </div>


                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">Rusak </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <ul id="sortable_2">
                                          <li class="ui-state-default" id="1">Item 1</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>  
                        </div>

                        <div class="col col-md-4 form-group">
                            <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption" align="center">idl </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <p>Idle</p>
                                        <ul id="idle">
                                          <li class="ui-state-default" id="1">Item 1</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>  
                        </div>
                    </div>

                    <!-- start -->


                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function get_dock()
    {
        $.ajax({
            data:"port="+$("#port").val(),
            type:"post",
            url:"<?php echo site_url(); ?>transaction/stc/get_dock",
            dataType:"json",
            success:function(x){

                // console.log(x);
                $("#total_dock").val(x.length);
                // alert("tes data");
            }
        });
    }

    function get_data()
    {
        alert("tes data");
    }



    jQuery(document).ready(function () {


        var sortedIDs;
        var anchor;
        var rusak;
        var idle;
        
        <?php echo $docks; ?>
       

        $("#sortable_1").sortable({
          connectWith: "",
          revert: true,
          stop: function(event, ui) {
            sortedIDs = $("#sortable_1").sortable("toArray");
            console.log(sortedIDs);
          }
        });


        $("#sortable_2").sortable({
          revert: true,
          stop: function(event, ui) {
            sortedIDs = $("#sortable_2").sortable("toArray");
            console.log(sortedIDs);
          }
        });
        
        
        $("#idle").sortable({
          revert: true,
          stop: function(event, ui) {
            sortedIDs = $("#sortable2").sortable("toArray");
            console.log(sortedIDs);
          }
        });


        $("#draggable").draggable({
          connectToSortable: "#sortable",
          helper: "clone",
          revert: "invalid",
          drag: function(event, ui) {
            console.log("TEST");
          },
        });
        $("ul, li").disableSelection();

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        // $("#port").change(function(){
        //     get_dock();
        // });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);


        
    });

</script>
