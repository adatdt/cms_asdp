<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php  echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
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
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                            <th>NO</th>
                            <th>PELABUHAN</th>
                            <th>LAYANAN</th>
                            <th>KONFIG STATUS</th>
                            <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Aksi
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </th>
                            </tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "fileJs.php"; ?>
<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.css'); ?>">
<script src="<?php echo base_url('assets/global/plugins/jquery-notific8/jquery.notific8.min.js'); ?>"></script>

<script type="text/javascript">
    let myData = new MyData();
    $(document).ready(function(){        
        myData.init()

    });
</script>
