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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days")); $last_day=date('Y-m-d',strtotime("-1 days")) ?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding">
<!--                         <?php if ($btn_excel) {?>
                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <?php } ?> -->
                    </div>
                </div>
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                    <div class="portlet-body">

                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <ul class="nav nav-tabs " role="tablist">
                                    <li class="nav-item active">
                                            <a class="label label-primary " data-toggle="tab" href="#tab1">Semua Data</a>
                                    </li>
                                    <li class="nav-item">
                                            <a class="label label-primary " data-toggle="tab" href="#tab2">Data Sudah diapprove</a>
                                    </li>
                                </ul>
              
                                <div class="tab-content " >

                                    <div class="tab-pane active" id="tab1" role="tabpanel" >

                                        <div class="table-toolbar">
                                            <div class="row">
                                                <div class="col-sm-12 form-inline">

                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Tanggal Boarding</div>
                                                        <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $last_week; ?>" readonly>
                                                        <div class="input-group-addon">s/d</div>
                                                        <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                                    </div>    



                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Pelabuhan</div>
                                                        <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                                            <?php if(!empty($ket)) {} else { ?>
                                                            <option value="">Pilih</option>
                                                            <?php } foreach($port as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>

                                                        
                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Tujuan</div>
                                                        <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                                            <option value="">Pilih</option>
                                                            <?php foreach($port_destination as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>  

                                                    <div class="pull-right btn-add-padding">
                                                        <?php if ($btn_excel) {?>
                                                            <button  class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                                                        <?php } ?>
                                                    </div>        

                                                </div>

                                            </div>
                                        </div>

                                        <table class="table table-bordered table-striped   table-hover" id="dataTables">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>TANGGAL BOARDING</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TANGGAL JADWAL</th>
                                                    <th>KAPAL</th>
                                                    <th>PELABUHAN</th>
                                                    <th>DERMAGA</th>
                                                    <th>TUJUAN</th>
                                                    <th>TIPE KAPAL</th>
                                                    <th>JAM BERANGKAT</th>
                                                    <th>KETERANGAN</th>
                                                    <th>AKSI</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>

                                    </div>

                                    <div class="tab-pane" id="tab2" role="tabpanel">

                                        <div class="table-toolbar">
                                            <div class="row">
                                                <div class="col-sm-12 form-inline">

                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Tanggal Boarding</div>
                                                        <input type="text" class="form-control date input-small" id="dateFrom2" value="<?php echo $last_day; ?>" readonly>
                                                        <div class="input-group-addon">s/d</div>
                                                        <input type="text" class="form-control date input-small" id="dateTo2" value="<?php echo $now; ?>" readonly>
                                                    </div>    


                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Pelabuhan</div>
                                                        <select id="port_origin2" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin2">
                                                            <?php if(!empty($ket)) {} else { ?>
                                                            <option value="">Pilih</option>
                                                            <?php } foreach($port as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>

                                                        
                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <div class="input-group-addon">Tujuan</div>
                                                        <select id="port_destination2" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination2">
                                                            <option value="">Pilih</option>
                                                            <?php foreach($port_destination as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>  

                                                    <div class="pull-right btn-add-padding">
                                                        <?php if ($btn_excel) {?>
                                                            <button  class="btn btn-sm btn-warning download" id="download_excel2">Excel</button>
                                                        <?php } ?>
                                                    </div>        

                                                </div>

                                            </div>
                                        </div>

                                        <table class="table table-bordered table-striped   table-hover" id="dataTables2">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>TANGGAL BOARDING</th>
                                                    <th>KODE BOARDING</th>
                                                    <th>TANGGAL JADWAL</th>
                                                    <th>KAPAL</th>
                                                    <th>PELABUHAN</th>
                                                    <th>DERMAGA</th>
                                                    <th>TUJUAN</th>
                                                    <th>TIPE KAPAL</th>
                                                    <th>JAM BERANGKAT</th>
                                                    <th>KETERANGAN</th>
                                                    <th>AKSI</th>
                                                </tr>
                                            </thead>
                                            <tfoot></tfoot>
                                        </table>

                                    </div>

                                </div>      
                            </div>
                        </div>
                    </div>

                </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php"; ?>

<script type="text/javascript">

        $(document).ready(function () {
        table.init();
        table2.init();


        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#dateTo").change(function(){
            table.reload();
        });

        $("#dateFrom").change(function(){
            table.reload();
        });

        $("#port_origin").change(function(){
            table.reload();
        });

        $("#port_destination").change(function(){
            table.reload();
        });


        $("#dateTo2").change(function(){
            table2.reload();
        });

        $("#dateFrom2").change(function(){
            table2.reload();
        });

        $("#port_origin2").change(function(){
            table2.reload();
        });

        $("#port_destination2").change(function(){
            table2.reload();
        });
        
    });
</script>
