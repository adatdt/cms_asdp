<!-- start modal -->
<style type="text/css">


.zoom img{
    -webkit-transition-duration:0.5s;
    -moz-transition-duration:0.5s;
    -o-transition-duration:0.5s;
    }
.zoom img:hover{-webkit-transform:scale(2.1);
    -moz-transform:scale(2.1);
    -o-transform:scale(2.1);
    -webkit-transition-duration:0.5s;
    -moz-transition-duration:0.5s;
    -o-transition-duration:0.5s;
    box-shadow:0px 0px 30px gray
    ;-webkit-box-shadow:0px 0px 30px gray;
    -moz-box-shadow:0px 0px 30px gray;
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
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
    							<th>KAPASITAS <br/> MAXIMAL</th>
                                <th>PANJANG <br/> MIN (mm)</th>
    							<th>PANJANG <br/> MAX (mm)</th>
                                <th>BERAT <br/> DEFAULT </th>
                                <th>JENIS <br/> KENDARAAN</th>
                                <th>GROUP <br/> KENDARAAN</th>
                                <th>JENIS GROUP <br/> KENDARAAN</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DESKRIPSI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
                                <th> LEBAR LINE METER</th>
                                <th> PANJANG LINE METER</th>
                                <th> LUAS LINE METER
                                    <br>
                                    (LEBAR * LUAS )
                                </th>
                                <th> GAMBAR</th>
    							<th> STATUS</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            </tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "fileJs.php" ?>

<script type="text/javascript">

    var myData= new MyData();

    jQuery(document).ready(function () {
        myData.init();
    });
</script>
