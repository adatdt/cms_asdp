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
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal Penugasan</div>
                                    <input type="text" class="form-control  input-small date" id="dateFrom" value="<?php echo date("Y-m-d"); ?>" readonly>
                                </div>                            
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <?= form_dropdown("port",$port,"",'id="port" class="form-control select2"') ?>
                                </div>    

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">User Grup</div>
                                    <?= form_dropdown("userGroup",$userGroup,"",'id="userGroup" class="form-control select2"') ?>
                                </div>                                    

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Shift</div>
                                    <?= form_dropdown("shift",$shift,"",'id="shift" class="form-control select2"') ?>
                                </div>                                    

                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Generate</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>   
                                <div  class="input-group select2-bootstrap-prepend pad-top " id="showPdf"></div>                                     
                            </div>

                            <div class="col-sm-12 form-inline">
                                <div class="input-group select2-bootstrap-prepend pad-top" style="color:red; font-style: italic;">
                                    Kode QR tergenerate hanya pada operator yang belum tutup dinas
                                </div>
                                                     
                            </div>
                                                        
                        </div>
                        <input type="hidden" id="tokenHash" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    </div>

                    <div class="row" id="showQr"></div>


                </div>
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">

    const myData = new MyData();
    $(document).ready(function () 
    {
        $('.select2').select2();

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            endDate: new Date(),
        });    
        
        $("#port").on("change", function(){
            let data = 
                        {"port":$(this).val(),
                        "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()}         
            myData.getShift(data);
        })

        $("#cari").on("click", function(){
            let data = {
                        port:$("#port").val(),   
                        dateFrom:$("#dateFrom").val(),
                        userGroup:$("#userGroup").val(),   
                        shift:$("#shift").val(),
                        <?php echo $this->security->get_csrf_token_name(); ?> : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(),         
                    }
            myData.generateQr(data);
        })  
        

    });

</script>
