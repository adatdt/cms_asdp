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
                                   <?= $report_title ?>
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
                                <td style="border-right: none !important;width: 15%">CABANG</td>
                                <td style="border-left: none !important;width: 35%">: <?=$header->branch_name ?></td>
                                <td style="border-right: none !important;width: 15%">SHIFT</td>
                                <td style="border-left: none !important;">: <?=$header->shift_name ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">PELABUHAN</td>
                                <td style="border-left: none !important;">: <?=$header->port ?></td>
                                <td style="border-right: none !important;">REGU</td>
                                <td style="border-left: none !important;">: <?=$header->team_name ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">LINTASAN</td>
                                <td style="border-left: none !important;">: <?=$header->origin ?> - <?=$header->destination ?></td>
                                <td style="border-right: none !important;">TANGGAL</td>
                                <td style="border-left: none !important;">: <?=format_date($header->shift_date) ?></td>
                            </tr>
                       </table>
                       
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <th class="text-center">NO</th>
                               <th class="text-center">JENIS TIKET</th>
                               <th class="text-center">TARIF <br/>(JRP)</th>
                               <th class="text-center">PRODUKSI<br/>(Lbr)</th>
                               <th class="text-center">PENDAPATAN<br/>(Rp)</th>
                               <th class="text-center">KETERANGAN</th>
                           </tr>
                           <tr>
                               <td class="text-center" style="padding: 2px 8px">1</td>
                               <td class="text-center" style="padding: 2px 8px">2</td>
                               <td class="text-center" style="padding: 2px 8px">3</td>
                               <td class="text-center" style="padding: 2px 8px">4</td>
                               <td class="text-center" style="padding: 2px 8px">5</td>
                               <td class="text-center" style="padding: 2px 8px">6</td>
                           </tr>
                           <tr>
                               <td class="text-center">1</td>
                               <td colspan="5">PENUMPANG</td>
                           </tr>
                           <?php 
                           $produksi_penumpang = 0;
                           $pendapatan_penumpang = 0;
                           foreach ($penumpang as $key_pnp => $pnp) {
                            $produksi_penumpang += $pnp->produksi;
                            $pendapatan_penumpang += $pnp->responsibility_fee;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $pnp->golongan?></td>
                                <td class="text-right"><?= idr_currency($pnp->harga) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->produksi) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->responsibility_fee) ?></td>
                                <td class="text-right"></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2">Sub Jumlah</th>
                               <th class="text-right"><?=idr_currency($produksi_penumpang) ?></th>
                               <th class="text-right"><?=idr_currency($pendapatan_penumpang) ?></th>
                               <th></th>
                           </tr>
                            <tr>
                               <td class="text-center">2</td>
                               <td colspan="5">KENDARAAN</td>
                           </tr>
                           <?php 
                           $produksi_kendaraan = 0;
                           $pendapatan_kendaraan = 0;
                           foreach ($kendaraan as $key_vhc => $vhc) { 
                            $produksi_kendaraan += $vhc->produksi;
                            $pendapatan_kendaraan += $vhc->responsibility_fee;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $vhc->golongan?></td>
                                <td class="text-right"><?= idr_currency($vhc->harga) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->produksi) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->responsibility_fee) ?></td>
                                <td class="text-right"></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2">Sub Jumlah</th>
                               <th class="text-right"><?=idr_currency($produksi_kendaraan) ?></th>
                               <th class="text-right"><?=idr_currency($pendapatan_kendaraan) ?></th>
                               <th></th>
                           </tr>
                           
                           <tr>
                               <th colspan="3" class="text-center">Jumlah</th>
                               <th class="text-right"><?= idr_currency($produksi_penumpang+$produksi_kendaraan) ?></th>
                               <th class="text-right"><?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></th>
                               <th></th> 
                           </tr>
                       </table>
                       <div>Fee Administrasi Asuransi Jasa Raharja Putra 15,4% = <?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></div>
                       
                       <table class="table table-no-border full-width" align="center">
                            <tr>
                                <td class="text-center" style="padding-bottom: 0;width: 33%"></td>
                                <th class="text-center" style="width: 33%"></th>
                                <td class="text-center" style="padding-bottom: 0">.................., ...................................</td>
                            </tr>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center">Supervisor</th>
                            </tr>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                            </tr>
                            <tr>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0"></td>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0"></td>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0"></td>
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