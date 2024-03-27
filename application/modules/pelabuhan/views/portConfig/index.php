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

                    <div class="row">
                        <div class="col-sm-12 form-inline">
                            <div class="input-group pad-top">
                                <div class="input-group-addon">Pelabuhan</div>
                                <?= form_dropdown("port",$port,"",'class="form-control select2" id="port" required') ?>
                            </div>
                            <div class="input-group pad-top">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Nama','name')">Nama</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Config Group','configGroup')">Config Group</a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" onclick="myData.changeSearch('Value','valueData')">Value</a>
                                        </li>                                                                      

                                    </ul>
                                </div>
                                <!-- /btn-group -->
                                <input type="text" class="form-control" placeholder="Cari Data" data-name="name" name="searchData" id="searchData"> 
                            </div>   
                            <div class="input-group pad-top">
                                <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                    <span class="ladda-label">Cari</span>
                                    <span class="ladda-spinner"></span>
                                </button>
                            </div>                                                                            
                        </div>

                    </div>                    

                    <p></p>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
                                <th>CONFIG GROUP</th>
                                <th>PELABUHAN</th>
                                <th>VALUE</th>
    							<th>STATUS</th>
                                <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th>
                            </tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="tokenHash" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">    
    const myData =  new MyData();


    jQuery(document).ready(function () {
        myData.init();
        $("#cari").on("click",function(){
            $(this).button('loading');
            $('#dataTables').DataTable().ajax.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        })

    });
</script>
