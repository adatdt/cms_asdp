<style type="text/css">
    .pad-top{
        padding-top: 5px;
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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php //echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Tanggal Muntah</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                            </div>    

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Kapal</div>
                                                <select id="ship" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($ship as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div> 

                                            <div class="input-group select2-bootstrap-prepend pad-top">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                    <option value="">Pilih</option>
                                                    <?php foreach($port as $key=>$value) {?>
                                                    <option value="<?php echo $this->enc->encode($value->port_code); ?>"><?php echo strtoupper($value->name); ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>      

                                            <div class="input-group pad-top">
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Boarding
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:;" onclick="myData.changeSearch('Kode Boarding','boardingCode')">Kode Boarding</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:;" onclick="myData.changeSearch('Nama Kapal','shipName')">Nama Kapal</a>
                                                            </li>     

                                                            <li>
                                                                <a href="javascript:;" onclick="myData.changeSearch('Kode Jadwal','scheduleCode')">Kode Jadwal</a>
                                                            </li>                                                                                                                                
                                                        </ul>
                                                    </div>
                                                    <!-- /btn-group -->
                                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="boardingCode" name="searchData" id="searchData"> 
                                            </div>                          

                                            <div class="input-group pad-top">
                                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                                        <span class="ladda-label">Cari</span>
                                                        <span class="ladda-spinner"></span>
                                                    </button>
                                            </div>        

                                        </div>
                                    </div>
                                </div>                           

                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>TANGGAL</th>
                                            <th>KAPAL</th>
                                            <th>PELABUHAN</th>
                                            <th>KODE BOARDING</th>
                                            <th>KODE JADWAL</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
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

<?php include "fileJs.php" ?>
<script type="text/javascript">
    
    var myData= new MyData()
    jQuery(document).ready(function () {
        myData.initHistory();

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload('dataTables');
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });        

        
    });
</script>
