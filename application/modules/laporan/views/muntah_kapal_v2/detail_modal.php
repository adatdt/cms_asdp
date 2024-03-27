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
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">                       
                    <div class="portlet-body">
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <td rowspan="4" style="width: 20%;vertical-align: middle;text-align: center;border-right: none !important;"><img src="<?php echo base_url('assets/img/asdp.png') ?>" alt="ASDP" width="180px"></td>
                               <td rowspan="4" class="text-center bold" style="width: 50%;font-size: 14pt; line-height: 1.5;vertical-align: middle;padding: 8px 58px;border-left: none !important;">
                                   LAPORAN MUNTAH KAPAL
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
                                <td style="border-right: none !important;width: 15%">NAMA KAPAL</td>
                                <td style="border-left: none !important;width: 35%">: KMP. <?=$detail_trip->ship_name ?></td>
                                <td style="border-right: none !important;width: 15%">LINTASAN</td>
                                <td style="border-left: none !important;">: <?=$lintasan ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">CABANG</td>
                                <td style="border-left: none !important;">: <?=$detail_trip->port_name?></td>
                                <td style="border-right: none !important;">DERMAGA</td>
                                <td style="border-left: none !important;">: <?=$detail_trip->dock_name?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">PELABUHAN</td>
                                <td style="border-left: none !important;">: <?=$detail_trip->port_name?></td>
                                <td style="border-right: none !important;">TANGGAL</td>
                                <td style="border-left: none !important;">: <?=date("d M Y",strtotime($detail_trip->shift_date))?></td>
                            </tr>
                       </table>
                       
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <th class="text-center">NO</th>
                               <th class="text-center">JENIS TIKET</th>
                               <th class="text-center">TARIF</th>
                               <th class="text-center">PRODUKSI</th>
                               <th class="text-center">PENDAPATAN</th>
                           </tr>
                           <tr>
                               <td class="text-center">1</td>
                               <td colspan="4">PENUMPANG</td>
                           </tr>
                           <?php 

                            $produksi_penumpang = 0;
                            $pendapatan_penumpang = 0;

                            foreach ($detail_passenger as $key_pnp => $pnp) { 
                              $produksi_penumpang += $pnp->produksi;
                              $pendapatan_penumpang += $pnp->pendapatan;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $pnp->golongan?></td>
                                <td class="text-right"><?= idr_currency($pnp->harga) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->produksi) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->pendapatan) ?></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2" class="bold">Sub Jumlah</th>
                               <th class="text-right bold"><?= idr_currency($produksi_penumpang) ?></th>
                               <th class="text-right bold"><?= idr_currency($pendapatan_penumpang) ?></th>
                           </tr>
                            <tr>
                               <td class="text-center">2</td>
                               <td colspan="4">KENDARAAN</td>
                           </tr>
                           <?php 
                            $produksi_kendaraan = 0;
                            $pendapatan_kendaraan = 0;
                            foreach ($detail_vehicle as $key_vhc => $vhc) { 
                              $produksi_kendaraan += $vhc->produksi;
                              $pendapatan_kendaraan += $vhc->pendapatan;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $vhc->golongan?></td>
                                <td class="text-right"><?= idr_currency($vhc->harga) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->produksi) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->pendapatan) ?></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2" class="bold">Sub Jumlah</th>
                               <th class="text-right bold"><?= idr_currency($produksi_kendaraan) ?></th>
                               <th class="text-right bold"><?= idr_currency($pendapatan_kendaraan) ?></th>
                           </tr>
                           <tr>
                               <th colspan="3" class="text-center bold">Jumlah (Penumpang + Kendaraan)</th>
                               <th class="text-right bold"><?= idr_currency($produksi_penumpang+$produksi_kendaraan) ?></th>
                               <th class="text-right bold"><?= idr_currency($pendapatan_penumpang+$pendapatan_kendaraan) ?></th>
                           </tr>
                       </table>

                      <!--  <table class="table table-no-border full-width" align="center">
                            <tr>
                                <td class="text-center" style="padding-bottom: 0">Dibuat oleh,</td>
                                <th class="text-center"></th>
                                <td class="text-center" style="padding-bottom: 0">Mengetahui,</td>
                            </tr>
                            <tr>
                                <th class="text-center">Petugas Klaim</th>
                                <th class="text-center">Operator Pelayaran</th>
                                <th class="text-center">Supervisor</th>
                            </tr>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                            </tr>
                            <tr>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0">.........................................</td>
                            </tr>
                            <tr>
                                <td class="text-center">NIK. .................................</td>
                                <td class="text-center">NIK. .................................</td>
                                <td class="text-center">NIK. .................................</td>
                            </tr>
                       </table> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>