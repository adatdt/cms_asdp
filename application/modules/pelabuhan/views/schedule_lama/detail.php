<style type="text/css">
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 1px 8px 1px 8px;
        line-height: 1.42857;
        vertical-align: top;
        border-top: 1px solid #e7ecf1;
    }

    .table {
        width: 100%;
        margin-bottom: 10px;
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
          <?php echo '<a href="' . $url_parent . '">' . $parent . '</a>'; ?>
          <i class="fa fa-circle"></i>
        </li>
        <li>
          <?php echo '<a href="' . $url_parent1 . '">' . $parent1 . '</a>'; ?>
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
    <br />
    <div class="portlet box blue-madison">
      <div class="portlet-title">
        <div class="caption"><?php echo $title ?></div>
        <div class="pull-right btn-add-padding">
            <?php echo generate_button('schedule', 'view', '<a href="' . site_url('pelabuhan/schedule') . '" class="btn btn-sm btn-warning">Kembali</a>'); ?>
        </div>
      </div>
      <div class="portlet-body">
        <div class="row">
            <div class="col-lg-9">
                <table class="table table-striped">
                    <thead>
                        <tr class="tr-detail">
                            <td colspan="3">Keberangkangkatan</td>
                        </tr>
                    </thead>
                    <tbody>
                    <!--
                        <tr class="warning">
                            <td>Nama kapal</td> <td>:</td><td><?php echo $schedule->ship_name; ?></td>
                        </tr>
                    -->
                        <tr class="warning">
                            <td>Keberangkatan</td> <td>:</td><td><?php echo $schedule->origin_name; ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Tujuan</td> <td>:</td><td><?php echo $schedule->dest_name; ?></td>
                        </tr>
                        <!--
                        <tr class="warning">
                            <td>Kapasitas Kendaraan</td> <td>:</td><td><?php echo $schedule->vehicle_capacity; ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Kapasitas penumpang</td> <td>:</td><td><?php echo $schedule->people_capacity; ?></td>
                        </tr>
                    -->
                    </tbody>
                </table>
                <p></p>
                <table class="table table-striped">
                    <thead >
                        <tr class="tr-detail">
                            <td colspan="2">Tarif Kendaraan</td>
                        </tr>
                        <tr class="warning">
                            <th>Golongan</th>
                            <th style="text-align: right;">Tarif (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vehicle_fare as $vehicle_fare) { ?>
                        <tr class="warning">
                            <td><?php echo $vehicle_fare->vehicle_name?></td><td align="right"><?php echo idr_currency($vehicle_fare->fare); ?></td>
                        </tr>
                        <?php  } ?>
                    </tbody>
                </table>
                <p></p>
                <table class="table table-striped">
                    <thead>
                        <tr class="tr-detail">
                            <td colspan="2">Tarif Perorangan</td>
                        </tr>
                        <tr class="warning">
                            <th>Golongan</th>
                            <th style="text-align: right;">Tarif (Rp)</th>
                        </tr>
                    </thead> 
                    <tbody>
                        <tr class="warning">
                            <td>Tarif Dewasa (Rp.)</td>
                            <td align="right"><?php echo idr_currency($schedule->adult_fare); ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Tarif Anak (Rp.)</td>
                            <td align="right"><?php echo idr_currency($schedule->child_fare); ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Tarif Bayi (Rp.)</td>
                            <td align="right"><?php echo idr_currency($schedule->infant_fare); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table class="table table-striped">
                    <tr style="background:#ff9c00; color:#FFFFFF;">
                        <td align="center">Waktu Keberangkatan</td>
                    <tr class="warning">
                    <?php foreach($get_time as $get_time) {?>
                    <tr class="warning">
                        <td align="center">Pukul : <?php echo format_time($get_time->departure); ?></td>
                    <tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <!-- strat row 
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead >
                        <tr style="background:#FFCC00; color:#FFFFFF;">
                            <td colspan="2">Tarif Kendaraan</td>
                        </tr>
                        <tr class="warning">
                            <th>Golongan</th>
                            <th>Tarif (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($vehicle_fare as $vehicle_fare) { ?>
                        <tr class="warning">
                            <td><?php echo $vehicle_fare->vehicle_name?></td><td align="right"><?php echo idr_currency($vehicle_fare->fare); ?></td>
                        </tr>
                        <?php  } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end row -->
        <!-- start 
        <div class="row">
            <div class="col-lg-9">
                <table class="table table-striped">
                    <thead>
                        <tr style="background:#FFCC00; color:#FFFFFF;">
                            <td colspan="3">Tarif Perorangan</td>
                        </tr>
                    </thead> 
                    <tbody>
                        <tr class="warning">
                            <td>Tarif Dewasa (Rp.)</td> <td>:</td><td align="right"><?php echo idr_currency($schedule->adult_fare); ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Tarif Anak (Rp.)</td> <td>:</td><td align="right"><?php echo idr_currency($schedule->child_fare); ?></td>
                        </tr>
                        <tr class="warning">
                            <td>Tarif Bayi (Rp.)</td> <td>:</td><td align="right"><?php echo idr_currency($schedule->infant_fare); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3"> 

            </div>
        </div>
        <!-- end row -->
    </div> 
</div>

</div>
