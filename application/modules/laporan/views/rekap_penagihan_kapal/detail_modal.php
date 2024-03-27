<style>
    .table-333 > tbody > tr > td,
    .table-333 > tbody > tr > th {
        padding: 4px 8px;
        border: 1px solid #333 !important;
    }
    .table-333 {
        font-family: 'Verdana', sans-serif;
    }

    .table-no-border, .table-no-border > tbody > tr > td, .table-no-border > tbody > tr > th, .table-no-border > tfoot > tr > td, .table-no-border > tfoot > tr > th, .table-no-border > thead > tr > td, .table-no-border > thead > tr > th {
        border: none;
    }
</style>

<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div id="print" class="portlet-body" >

            <div class="row">
                <div class="col-md-12">                       
                    <div class="portlet-body">
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <td rowspan="4" style="width: 20%;vertical-align: middle;text-align: center;border-right: none !important;"><img src="<?php echo base_url('assets/img/asdp.png') ?>" alt="ASDP" width="180px"></td>
                               <td rowspan="4" class="text-center bold" style="width: 50%;font-size: 14pt; line-height: 1.5;vertical-align: middle;padding: 8px 58px;border-left: none !important;">
                                   REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT
                               </td>
                               <td style="width: 15%; border-right: none !important;">No. Dokumen</td>
                               <td style="width: 15%; border-left: none !important;">: </td>
                           </tr>
                           <tr>
                               <td style="border-right: none !important;">Revisi</td>
                               <td style="border-left: none !important;">:</td>
                           </tr>
                           <tr>
                               <td style="border-right: none !important;">Berlaku Efektif</td>
                               <td style="border-left: none !important;">:</td>
                           </tr>
                           <tr>
                               <td style="border-right: none !important;">Halaman</td>
                               <td style="border-left: none !important;">:</td>
                           </tr>
                       </table>

                       <table class="table table-333 table-bordered full-width" align="center">
                        <tr>
                            <td style="border-right: none !important;width: 15%">PELABUHAN</td>
                            <td style="border-left: none !important;width: 35%">: <?= strtoupper($port_name) ?></td>
                            <td style="border-right: none !important;width: 15%">TANGGAL</td>
                            <td style="border-left: none !important;">: <?= format_date($date) ?></td>
                        </tr>
                        <tr>
                            <td style="border-right: none !important;">SHIFT</td>
                            <td style="border-left: none !important;">: <?= $shift_name ?></td>
                            <td style="border-right: none !important;">JAM</td>
                            <td style="border-left: none !important;">: <?= $shift_time ?></td>
                        </tr>
                    </table>

                    <table class="table table-333 table-bordered full-width" align="center">
                       <tr>
                           <th class="text-center">NO</th>
                           <th class="text-center">NAMA PERUSAHAAN /<br>NAMA KAPAL</th>
                           <th class="text-center">JLH<br>TRIP</th>
                           <th class="text-center">Penumpang</th>
                           <th class="text-center">Kendaraan</th>
                           <th class="text-center">KSO<br>PT. ASDP</th>
                           <th class="text-center">JUMLAH</th>
                       </tr>
                       <tr>
                           <td class="text-center" style="padding: 2px 8px">1</td>
                           <td class="text-center" style="padding: 2px 8px">2</td>
                           <td class="text-center" style="padding: 2px 8px">3</td>
                           <td class="text-center" style="padding: 2px 8px">4</td>
                           <td class="text-center" style="padding: 2px 8px">5</td>
                           <td class="text-center" style="padding: 2px 8px">6</td>
                           <td class="text-center" style="padding: 2px 8px">6</td>
                       </tr>
                       <?php
                           $totalTrip = 0;
                           $totalPenumpang = 0;
                           $totalVehicle = 0;
                           $total = 0;

                        foreach ($data as $key => $value) {
                            $totalTrip += $value->qty;
                            $totalPenumpang += $value->penumpang;
                            $totalVehicle += $value->vehicle;
                            $total += ($value->penumpang + $value->vehicle);
                        ?>
                        <tr>
                            <td class="text-center"><?= ($key+1) ?></td>
                            <td><?= $value->ship_name ?></td>
                            <td class="text-center"><?= idr_currency($value->qty) ?></td>
                            <td class="text-right"><?= ($value->penumpang == 0) ? '-' : idr_currency($value->penumpang) ?></td>
                            <td class="text-right"><?= ($value->vehicle == 0) ? '-' : idr_currency($value->vehicle) ?></td>
                            <td class="text-right">-</td>
                            <td class="text-right"><?= (($value->vehicle+$value->penumpang) == 0 ) ? '-' : idr_currency($value->vehicle+$value->penumpang) ?></td>
                        </tr>

                        <?php } ?>
                    <tr>
                       <th class="text-center"></th>
                       <th>JUMLAH</th>
                       <th class="text-center"><?= $totalTrip ?></th>
                       <th class="text-right"><?= idr_currency($totalPenumpang) ?></th>
                       <th class="text-right"><?= idr_currency($totalVehicle) ?></th>
                       <th class="text-right">-</th>
                       <th class="text-right"><?= idr_currency($total) ?></th>
                   </tr>
               </table>

               <table class="table table-no-border full-width" align="center">
                <tr>
                    <td class="text-center" style="padding-bottom: 0;width: 33%"></td>
                    <th class="text-center" style="width: 33%"></th>
                    <td class="text-center" style="padding-bottom: 0"><?php echo ucwords(strtolower($port_name)) . ", " . format_date($date); ?></td>
                </tr>
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center">Supervisor</th>
                </tr>
                <tr>
                    <th class="center"></th>
                    <th class="center"></th>
                    <th class="center">
                        <br>
                    </th>
                </tr>
                <tr>
                    <th class="center"></th>
                    <th class="center"></th>
                    <th class="center"></th>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"><?= $spv ?></td>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">NIK. .................................</td>
                </tr>
            </table>

        </div>
    </div>
</div>
</div>
</div>
</div>