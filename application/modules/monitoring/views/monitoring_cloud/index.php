<style>
#center-tbl{
    background:#fff;
}
#center-tbl:hover{
    background:#fff;
    background-color:#fff;
}
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

        <?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        

                                        <div class="col-md-3" style="padding-right: 0px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Server Lokal</span>
                                                    <select id="server" class="form-control js-data-example-ajax select2" dir="">
                                                    <?php echo $option?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5" style="padding-right: 0px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Tanggal</span>
                                                    <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo $date_p ?>" readonly="readonly">
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo $date_p ?>" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="padding-left: 5px;">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-danger" id="searching" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <?php //echo $tampil?>

                                <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="dataTables">
                                    <?php echo $table?>
                                    
                                    
                                </table> 


                                </div>

                                <div class="cart" style="margin-top:50px">

                                    <div id="chart_ltc" style="width:100%; min-height:500px"></div>
                                    <div id="chart_ctl" style="width:100%; min-height:500px"></div>

                                </div>

                                
                            </div>
                        </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                 </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
var csfrData = {};
csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
$.ajaxSetup({
    data: csfrData
});
$(document).ready(function(){

    $('#datefrom').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#dateto').datetimepicker('setStartDate', e.date)
    });

    $('#dateto').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        startDate: $('#datefrom').val(),
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#datefrom').datetimepicker('setEndDate', e.date)
    });

    $('#searching').click(function(){
        $('#chart_ltc').html('');
        $('#chart_ctl').html('');
        listData();
    });

    // $('#dataTables').DataTable({
    //     "ordering": false
    // });

    var listData = function(){
        $.ajax({
            url         : 'monitoring_cloud/get_data',
            data        : {
                server_id : $('#server').val(),
                start_date : $('#datefrom').val(),
                end_date : $('#dateto').val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`]
            },
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                $('#searching').button('loading');
                // unBlockUiId('.box');
            },

            success: function(json) {
                //Make your callback here.
                let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                let getToken = json[getTokenName];
                csfrData[getTokenName] = getToken;

                if( json[getTokenName] == undefined )
                {
                csfrData[json.csrfName] = json.tokenHash;
                }
                    
                $.ajaxSetup({
                    data: csfrData
                });
                includeData(json);
                statusData(json);
                list_bar(json,'ltc');
                list_bar(json,'ctl');
                //dSend = json.data.post;
            },

            error: function() {
                toastr.error('Please contact the administrator');
            },

            complete: function(json){
                $("#searching").button('reset');
                //$('.box').unblock();
            }
        });

    }

    listData();


    function includeData(json){
        // console.log(json);

        var jsondataLocal = json.local[0];
        var jsondataCloud = json.cloud[0];
        
        Object.keys(jsondataLocal).forEach(function(k){
            $('td[data-status="'+k+'"]').html(jsondataLocal[k]);
        });
        Object.keys(jsondataCloud).forEach(function(k){
            $('td[data-status="'+k+'"]').html(jsondataCloud[k]);
        });

    }

    function statusData(json){
        var num = json.num;
        for(var i = 1; i <= num; i ++){
            var ltc_local = $('td[data-id="ltc_local_'+i+'"]').html();
            var ltc_cloud = $('td[data-id="ltc_cloud_'+i+'"]').html();
            
            var ltc_status = '';
            if(ltc_local == ltc_cloud){
                ltc_status = '<i class="fa fa-check-circle" style="color:green" title="Up to date"></i>';
            }else{
                ltc_status = '<i class="fa fa-times-circle" style="color:red" title="Not update"></i>';
            }


            var ctl_local = $('td[data-id="ctl_local_'+i+'"]').html();
            var ctl_cloud = $('td[data-id="ctl_cloud_'+i+'"]').html();
            
            var ctl_status = '';
            if(ctl_local == ctl_cloud){
                ctl_status = '<i class="fa fa-check-circle" style="color:green" title="Up to date"></i>';
            }else{
                ctl_status = '<i class="fa fa-times-circle" style="color:red" title="Not update"></i>';
            }


            $('td[data-id="ltc_status_'+i+'"]').html(ltc_status);
            $('td[data-id="ctl_status_'+i+'"]').html(ctl_status);
        }
    }




    //chart
    
    
    function list_bar(dataJSON,statusData){
        var dataBar = dataJSON.bar;
        var urlJS = "<?php echo base_url()?>assets/global/plugins/echarts/echarts-all.js";
        $.getScript(urlJS, function () {
            if(statusData == 'ctl'){
                var idElement = 'chart_ctl';
                var data_local = dataBar.data_ctl_local;
                var data_cloud = dataBar.data_ctl_cloud;
                var title_bar = 'Cloud To Local';
            }else{
                var idElement = 'chart_ltc';
                var data_local = dataBar.data_ltc_local;
                var data_cloud = dataBar.data_ltc_cloud;
                var title_bar = 'Local To Cloud';
            }
            var chart = document.getElementById(idElement);
            var myChart = echarts.init(chart);
            
            var option = {
                title: { text: title_bar },
                toolbox: {
                    show : true,
                    feature : {
                        saveAsImage : {show: true}
                    }
                },
                tooltip: { 
                    trigger: 'axis',
                    axisPointer : {            
                        type : 'shadow'
                    }
                },
                grid: {
                  x : 200
                },
                legend: { data: [ 'Local', 'Cloud' ] },
                xAxis: { 
                    type : 'value'
                },
                yAxis: {
                    type : 'category',
                    data: dataBar.list_table ,
                    margin: 0,
                    axisLabel: {
                        show : true,
                        interval: 0,
                        inside : true,
                        rotate : 'vertical'
                    },
                    axisTick: {
                        alignWithLabel: true
                    }                    
                },
                series: [{
                    name: 'Local',
                    type: 'bar',
                    data: data_local
                },
                {
                    name: 'Cloud',
                    type: 'bar',
                    data: data_cloud
                }]
            };

            myChart.setOption(option);

            window.onresize = function(){
                myChart.resize();
            }
            



        });


    }

})

</script>



<style type="text/css">
  .padding-title-chart{
    padding: 5px 10px 0px 5px !important;
  }

  .padding-body{
    padding: 0px 5px 0px 20px !important;
  }
</style>
