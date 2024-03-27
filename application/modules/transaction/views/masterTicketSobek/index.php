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
                    <div class="pull-right btn-add-padding" style="padding-left: 5px" ><?php echo $btn_add; ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px" ><?php echo $btn_excel; ?></div>
                    <div class="pull-right btn-add-padding"> <?php if($import){?>

                        <a href="<?php echo base_url()?>template_excel/master_ticket_sobek.xlsx" class="btn btn-sm btn-warning">Format Excel</a>
                        
                    <?php } ?></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Pelabuhan</div>
                                                <?= form_dropdown("port",$port,"",' id="port" class="form-control select2"' ) ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend" id="divService" >
                                                <div class="input-group-addon">Jenis PJ</div>
                                                <?= form_dropdown("service",$service,"",'id="service" class="form-control select2"' ) ?>
                                            </div>

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Layanan</div>
                                                <?= form_dropdown("layanan",$layanan,"",'id="layanan" class="form-control select2"' ) ?>
                                            </div>            

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Status</div>
                                                <?= form_dropdown("status",$status,"",'id="status" class="form-control select2"' ) ?>
                                            </div>                                                        

                                           <div class="input-group pad-top">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >No. Tiket
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" onclick="myData.changeSearch('No. Tiket','ticketNumber')">No. Tiket</a>
                                                        </li>                       
                                                    </ul>
                                                </div>
                                                <!-- /btn-group -->
                                                <input type="text" class="form-control" placeholder="Cari Data" data-name="ticketNumber" name="searchData" id="searchData"> 
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
                                            <th>NO TIKET</th>
                                            <th>PELABUHAN</th>
                                            <th>LAYANAN</th>
                                            <th>JENIS PJ</th>
                                            <th>GOLONGAN</th>
                                            <th>TANGGAL INPUT</th>
                                            <th>USER INPUT</th>
                                            <th>TANGGAL DIGUNAKAN</th>
                                            <th>STATUS</th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            AKSI
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
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
    
    let myData= new MyData();
    $(document).ready(function () 
    {
        myData.init();

        $("#cari").on("click",function(){
            myData.reload();
        });

        
        $("#service").on("change",function(){
            
            var getValue=$("#service option:selected").html();

            getValue=getValue.replace(" ","_");

            $("#divGolongan").remove();

            html =` <div class="input-group select2-bootstrap-prepend" id="divGolongan" > `
            if(getValue.toLowerCase()=='kendaraan')
            {
                html +=`<div class="input-group-addon">Golongan Kendaraan</div>
                        <?= form_dropdown("golongan",$vehicleClass,"", 'class="form-control select2" required  id="golongan" ' ) ?>`
                
            }
            else if(getValue.toLowerCase()=='pejalan_kaki')
            {
                html +=`<div class="input-group-addon">Golongan Pejalan Kaki</div>
                        <?= form_dropdown("golongan",$passangerType,"", 'class="form-control select2" required  id="golongan" ' ) ?>`   
            }
            else
            {
                html +=``;
            }

            html +=`</div>`;

            $(html).insertAfter("#divService");

            $('.select2:not(.normal)').each(function () {
                $(this).select2({
                    dropdownParent: $(this).parent()
                });
            });

        })        
    });
</script>
