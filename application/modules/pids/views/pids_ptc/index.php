<style type="text/css">
    .mt-element-list .list-simple.mt-list-container {
        border-left: 1px solid;
        border-right: 1px solid;
        border-bottom: 1px solid;
        border-color: #e7ecf1;
        padding: 5px;
    }

    .mt-element-list .list-simple.mt-list-container ul>.mt-list-item {
        list-style: none;
        border-bottom: 1px solid;
        border-color: #e7ecf1;
        padding: 5px 0;
    }

    .max-height{
        height: 180px !important;
        overflow-y: scroll;
    }

    .max-height::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
        background-color: #F5F5F5;
    }

    .max-height::-webkit-scrollbar
    {
        width: 12px;
        background-color: #F5F5F5;
    }

    .max-height::-webkit-scrollbar-thumb
    {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
        background-color: #555;
    }

    .pad-left{
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    .mt-element-list .list-simple.mt-list-head .list-title {
        margin: 0;
        padding-right: 0px; 
    }
    
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 4px;
        line-height: 1.42857;
        vertical-align: top;
        border-top: 1px solid #e7ecf1;
    }

    .mt-element-list .list-simple.mt-list-head {
         padding: 7px; 
    }

    .padd-btn{
        padding: 0px 5px 0px 5px !important;
        font-size: 14px !important;
    }

    .portlet {
        margin-top: 0;
        margin-bottom: 10px;
        padding: 0;
        border-radius: 4px;
    }

    .page-header.navbar.navbar-fixed-top, .page-header.navbar.navbar-static-top {
        z-index: 1040;
    }
    .toast-top-center {
        top: 24px;
        left: 47%;
    }
    .toast-top-center .toast-info {
        box-shadow: 0 0 5px #007984 !important;
        margin: 0 auto 0 -150px !important;
    }
    #toast-container > .toast-info {
        margin: 0 0 6px !important;
    }
    .toast .toast-time {
        float: right;
        font-size: 12px;
        line-height: 24px;
    }
    .toast-info {
        background-color: #36c6d3;
        opacity: .85;
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

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?> <b>(<?php echo $port_name; ?>)</b></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body" id="box-ui">
                    <div class="row">
                        <div class="col-md-12 form-inline">
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">Tanggal</div>
                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo date('Y-m-d') ?>" placeholder="YYYY-MM-DD"  autocomplete="off" readonly>
                            </div>    
                            <div class="input-group select2-bootstrap-prepend">
                               <div class="input-group-addon">Pelabuhan</div>
                               <?php echo form_dropdown("port",$port,"","class='form-control select2' ") ?>

                            </select>
                        </div> 

                        <div class="input-group ">
                            <button type="button" class="btn btn-small btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" title="Cari" id="searching" style="padding: 6px 12px; font-size: 14px">Cari</button>
                        </div> 
                    </div>
                    <hr><hr>


                    <div class="col-md-12 pad-left">
<!--                         <div class="col-md-3 pad-left">
                            <div class="table-scrollable" style="margin-top:0px !important">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th> Warna </th>
                                            <th> Keterangan </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="background-color: #F1C40F"></td>
                                            <td> Masuk Alur </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #E87E04"></td>
                                            <td> Sandar </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #337ab7"></td>
                                            <td> Mulai Pelayanan </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #9A12B3"></td>
                                            <td> Selesai Pelayanan </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #36c6d3"></td>
                                            <td> Tutup Ramdor </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #26C281"></td>
                                            <td> Berlayar </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->
                        <div id="listDock"></div>
                        
                    </div>
                    <div class="col-md-12 pad-left">
                        <div id="summary"></div>
                    </div>                    
                    <hr>
<!--                     <div class="col-md-12 hidden pad-left" id="listProblem">
                        <?php foreach ($problem as $key => $row) { ?>
                            <div class="col-md-3 pad-left">
                                <div class="portlet box">
                                    <div class="portlet-body form">
                                        <div class="mt-element-list" style="position: relative;">
                                            <div class="mt-list-head list-simple font-white bg-red">
                                                <div class="list-head-title-container">
                                                    <h5 class="list-title"><?php echo $row['title'] ?> <a href="javascript:;" class="ahref-problem" data-toggle="collapse" data-target="#p_<?php echo $key ?>"><i class="fa fa-angle-down pull-right" style="color: white;"></i></a></h5>
                                                </div>
                                            </div>
                                            <div class="mt-list-container list-simple max-height" id="p_<?php echo $key ?>" >
                                                <ul class="sortable problem" data-problem="<?php echo $row['problem'] ?>" id="<?php echo $row['id'] ?>">
                                                </ul>                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        <?php } ?>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    const add = (a,b) =>{
        return a+b
    }

    function listDermaga(){
        $.ajax({
            url         : 'pids_ptc/list_dock',
            data        : {
                date : '<?php echo date('Y-m-d'); ?>',
                port : $("[name='port']").val(),
            },
            type        : 'POST',
            dataType    : 'json',

            beforeSend: function(){
                $('#box-ui').block({
                    message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>',
                    overlayCSS: {
                        opacity: 0.2
                    }
                });
            },

            success: function(d) {

                console.log(d)
                if(d.code){
                    $('#listProblem').removeClass('hidden');
                    dataUL = d.data.dataPids;
                    dataSummary = d.data.summary;
                    html_dock = '';


                    for(i in dataUL){
                        dataList = {
                            data: dataUL[i],
                            name: i, 
                            type:'dock',
                            action: d.data.action,
                            identity_app: d.data.identity_app
                        }

                        html_dock += listDock(dataList);
                    }

                    if(Object.keys(dataSummary).length>0)
                    {
                        html_sumary=getSummary(dataSummary);
                    }
                    else
                    {
                        html_sumary="";
                    }


                    $('#listDock').html(html_dock);
                    $('#summary').html(html_sumary);
                    $('#data-not-found').html('');
                }
                else
                {
                    $('#listProblem').addClass('hidden');
                    $('#listDock').html('');
                    $('#summary').html('');
                    $('#data-not-found').html('<center><h3>Data not found</h3></center>');
                }
            },

            error: function() {
                console.log('Please contact the administrator');
            },

            complete: function(){
                // $('#searching').button('reset');
                $('#box-ui').unblock();
            }
        }).done(function(){
            $('.ahref').trigger('click');
        })
    }


    $('#searching').click(function(){
        listDermaga();
    })    
    $(document).ready(function () {

        listDermaga();

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        var options = {
          "closeButton": true,
          "newestOnTop": true,
          "positionClass": "toast-top-center",
          "timeOut": "0"
        }

        setInterval(function(){
            $('.toast-time').each(function(){
                $(this).text(pretty($(this).data('time')));
            });
        }, 10 * 1000);


        $('.ahref-problem').trigger('click');

        setInterval(function(){
            var today = new Date();
            var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
            if(time == '0:0:0'){
                listDermaga();
            }
        },1000)
    });
    
    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(find, 'g'), replace);
    }

    function listDock(z){
        // console.log(z.data)
        // str = z.name.replace(' ','_');
        str = replaceAll(z.name, ' ', '_');
        html = '<div class="col-md-6 pad-left">\
            <div class="portlet box">\
                <div class="portlet-body form">\
                    <div class="mt-element-list">\
                        <div class="mt-list-head list-simple font-white bg-primary">\
                            <div class="list-head-title-container">\
                                <h5 class="list-title">'+z.name+' <a href="javascript:;" class="ahref" data-toggle="collapse" data-target="#'+str+'"><i class="fa fa-angle-down pull-right" style="color: white;"></i></a></h5>\
                            </div>\
                        </div>\
                        <div class="mt-list-container list-simple collapse max-height" id="'+str+'">'+getShip(z)+'\
                        </div>\
                    </div>\
                </div>\
            </div>\
        </div>';
        return html;
    }

    function getShip(x)
    {
        html='';
        data=x.data
        // console.log(data[1].name);
        html +='<table class="table table-striped">\
                        <thead>\
                    <tr>\
                        <th>NO</th>\
                        <th>NAMA KAPAL</th>\
                        <th>KAPAL PENGGANTI</th>\
                        <th>STATUS</th>\
                        <th>AKSI</th>\
                    </tr>\
                </thead><tbody>';
        var no=1
        for(i in data )
        {

            var shipBackup=data[i].ship_backup_name
            var chekShipBackup=shipBackup==null?"":shipBackup;

            html +='<tr>\
                        <th scope="row">'+no+'</th>\
                        <td>'+data[i].ship_name+'</td>\
                        <td>'+chekShipBackup+'</td>\
                        <td style="text-align:center; padding-top:10px;" >'+data[i].detail_status+'</td>\
                        <td style="text-align:center" >'+data[i].actions+'</td>\
                    </tr>';  

            no++;                        
                        
        }


        html +='</tbody></table>';
        return html;

    }

    function getSummary(x)
    {
        var html='<table class="table table-striped">\
            <thead>\
                <tr>\
                        <th colspan="5">Summary</th>\
                </tr>\
                <tr>\
                    <th>NAMA DERMAGA</th>\
                    <th>TOTAL KAPAL BEROPRASI</th>\
                    <th>TOTAL KAPAL ANCHOR</th>\
                    <th>TOTAL KAPAL RUSAK</th>\
                    <th>TOTAL KAPAL DOCKING</th>\
                    <th>TOTAL KAPAL BERLAYAR</th>\
                </tr>\
            </thead>\
            <tbody>'

            var count_ship=[];
            var count_anchor=[];
            var count_broken=[];
            var count_docking=[];

            for(i in x){
                html +='<tr>\
                    <th>'+i+'</th>\
                    <td style="text-align:center" >'+x[i].count_ship+'</td>\
                    <td style="text-align:center" >'+x[i].count_anchor+'</td>\
                    <td style="text-align:center" >'+x[i].count_broken+'</td>\
                    <td style="text-align:center" >'+x[i].count_docking+'</td>\
                    <td style="text-align:center" >'+x[i].count_sail+'</td>\
                </tr>'                

                if(x[i].count_ship=="-")
                {
                    // replace - jadi 0 dan kemudian masukan data ke array
                    count_ship.push(parseInt(x[i].count_ship.replace("-", 0)));
                    count_anchor.push(parseInt(x[i].count_anchor.replace("-", 0)));
                    count_broken.push(parseInt(x[i].count_broken.replace("-", 0)));
                    count_docking.push(parseInt(x[i].count_docking.replace("-", 0)));
                }
                else
                {
                    // memasukan data langsung ke array
                    count_ship.push(parseInt(x[i].count_ship));   
                    count_anchor.push(parseInt(x[i].count_anchor));   
                    count_broken.push(parseInt(x[i].count_broken));   
                    count_docking.push(parseInt(x[i].count_docking));   
                }
            }

            // validasi apakah returnya berupa nan value..
            var sumShip= isNaN(count_ship.reduce(add))?0:count_ship.reduce(add);
            var sumAnchor= isNaN(count_anchor.reduce(add))?0:count_anchor.reduce(add);
            var sumBroken= isNaN(count_broken.reduce(add))?0:count_broken.reduce(add);
            var sumDocking= isNaN(count_docking.reduce(add))?0:count_docking.reduce(add);

            // html +='<tr>\
            //     <th>Grand Total </th>\
            //     <td style="text-align:center" >'+sumShip +'</td>\
            //     <td style="text-align:center" >'+sumAnchor+'</td>\
            //     <td style="text-align:center" >'+sumBroken+'</td>\
            //     <td style="text-align:center" >'+sumDocking+'</td>\
            // </tr>'

            html +='</tbody>\
        </table>';

        return html;
    }
</script>
