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
                                   FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN-PER TRIP
                               </td>
                               <td style="width: 15%; border-right: none !important;">No. Dokumen</td>
                               <td style="width: 15%; border-left: none !important;">: BPL-105.00.10</td>
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
                                <td style="border-left: none !important;width: 35%">: KMP. <?= strtoupper($detail_trip->ship_name)  ?></td>
                                <td style="border-right: none !important;width: 15%">LINTASAN</td>
                                <td style="border-left: none !important;">: <?= $detail_trip->trip." (".$detail_trip->ship_class.")"  ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">PERUSAHAAN</td>
                                <td style="border-left: none !important;">: <?= $detail_trip->company_name  ?></td>
                                <td style="border-right: none !important;">DERMAGA</td>
                                <td style="border-left: none !important;">: <?= $detail_trip->dock_name ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">CABANG</td>
                                <td style="border-left: none !important;">: <?= $detail_trip->port_name." (".$detail_trip->ship_class.")"  ?></td>
                                <td style="border-right: none !important;">TANGGAL</td>
                                <td style="border-left: none !important;">: <?= format_date(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date) ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">PELABUHAN</td>
                                <td style="border-left: none !important;">: <?= $detail_trip->port_name ?></td>
                                <td style="border-right: none !important;">JAM</td>
                                <td style="border-left: none !important;">: <?= format_time(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date) ?></td>
                            </tr>
                            <tr>
                                <!-- belom deploy tiket manual -->
                                <!-- <td style="border-right: none !important;">TIPE JADWAL TIKET</td>
                                <td style="border-left: none !important;">: <?= $ticketTypeku ?></td> -->
                                <td style="border-right: none !important;"></td>
                                <td style="border-left: none !important;"></td>
                                <td style="border-right: none !important;"></td>
                                <td style="border-left: none !important;"></td>
                            </tr>                            
                       </table>
                       
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <th class="text-center">NO</th>
                               <th class="text-center">JENIS TIKET</th>
                               <th class="text-center">TARIF</th>
                               <th class="text-center">PRODUKSI</th>
                               <th class="text-center">PENDAPATAN</th>
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

                            $totalTrip = 0;
                            $totalAmount = 0;
                            $totalAdmFee = 0;

                            foreach ($detail_passenger as $key_pnp => $pnp) { 
                              $totalTrip += $pnp->ticket_count;
                              $totalAmount += $pnp->total_amount;
                              $totalAdmFee += $pnp->adm_fee;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $pnp->passanger_type_name?></td>
                                <td class="text-right"><?= idr_currency($pnp->trip_fee) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->ticket_count) ?></td>
                                <td class="text-right"><?= idr_currency($pnp->total_amount) ?></td>
                                <td class="text-right"></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2" class="bold">Sub Jumlah</th>
                               <th class="text-right bold"><?= idr_currency($totalTrip) ?></th>
                               <th class="text-right bold"><?= idr_currency($totalAmount) ?></th>
                               <th></th>
                           </tr>
                            <tr>
                               <td class="text-center">2</td>
                               <td colspan="5">KENDARAAN</td>
                           </tr>
                           <?php 
                            $totalTripVehicle = 0;
                            $totalAmountVehicle = 0;
                            $totalAdmFeeVehicle = 0;
                            foreach ($detail_vehicle as $key_vhc => $vhc) { 
                              $totalTripVehicle += $vhc->ticket_count;
                              $totalAmountVehicle += $vhc->total_amount;
                              $totalAdmFeeVehicle += $vhc->adm_fee;
                            ?>
                            <tr>
                                <td></td>
                                <td><?= $vhc->vehicle_type_name?></td>
                                <td class="text-right"><?= idr_currency($vhc->trip_fee) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->ticket_count) ?></td>
                                <td class="text-right"><?= idr_currency($vhc->total_amount) ?></td>
                                <td class="text-right"></td>
                            </tr>
                           <?php } ?>
                           <tr>
                               <th class="text-center"></th>
                               <th colspan="2" class="bold">Sub Jumlah</th>
                               <th class="text-right bold"><?= idr_currency($totalTripVehicle) ?></th>
                               <th class="text-right bold"><?= idr_currency($totalAmountVehicle) ?></th>
                               <th></th>
                           </tr>
                           <tr>
                               <th colspan="3" class="text-center bold">Jumlah (Penumpang + Kendaraan)</th>
                               <th class="text-right bold"><?= idr_currency($totalTrip+$totalTripVehicle) ?></th>
                               <th class="text-right bold"><?= idr_currency($totalAmount+$totalAmountVehicle) ?></th>
                               <th></th> 
                           </tr>
                           <tr>
                               <td class="text-center">3</td>
                               <td colspan="5">BEA JASA PELABUHAN</td>
                           </tr>
                           <tr>
                               <td></td>
                               <td>a. Jasa Adm. Tiket </td>
                               <td colspan="3" class="text-right"><?php echo idr_currency($adm_tiket = $totalAdmFee+$totalAdmFeeVehicle) ?></td>
                               <td></td>
                           </tr>
                           <tr>
                               <td></td>
                               <td>b. Jasa Sandar</td>
                               <td colspan="3" class="text-right"><?= idr_currency($dock_fare->dock_service) ?></td>
                               <td></td>
                           </tr>
                           <tr>
                               <td></td>
                               <td>c. Jasa Kepil</td>
                               <td colspan="3" class="text-right"><?=idr_currency($jasa_kepil) ?></td>
                               <td></td>
                           </tr>
                           <tr>
                               <th class="text-center bold" colspan="2">Jumlah</th>
                               <th colspan="3" class="text-right bold"><?= idr_currency($bea = ($dock_fare->dock_service + $adm_tiket + $jasa_kepil)) ?></th>
                               <th></th>
                           </tr>
                       </table>

                       <table class="table table-no-border full-width" align="center">
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
                       </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $("#btndownload").click(function(event){

        window.location.href="<?php echo site_url('transaction/boarding/download/'.$code) ?>";
    });

    $(document).ready(function(){

        // $("#table").DataTable({

        //     "language": {
        //         "aria": {
        //             "sortAscending": ": activate to sort column ascending",
        //             "sortDescending": ": activate to sort column descending"
        //         },
        //           "processing": "Proses.....",
        //           "emptyTable": "Tidak ada data",
        //           "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        //           "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
        //           "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
        //           "lengthMenu": "Menampilkan _MENU_",
        //           "search": "Pencarian :",
        //           "zeroRecords": "Tidak ditemukan data yang sesuai",
        //           "paginate": {
        //             "previous": "Sebelumnya",
        //             "next": "Selanjutnya",
        //             "last": "Terakhir",
        //             "first": "Pertama"
        //         }
        //     },
        //     "lengthMenu": [
        //         [5,10, 25, 50, 100],
        //         [5,10, 25, 50, 100]
        //     ],
        //     "pageLength": 10,
        //     "pagingType": "bootstrap_full_number",
        //     "order": [[ 0, "asc" ]],
        //     // "initComplete": function () {
        //     //     var $searchInput = $('div.dataTables_filter input');
        //     //     var data_tables = $('#dataTables').DataTable();
        //     //     $searchInput.unbind();
        //     //     $searchInput.bind('keyup', function (e) {
        //     //         if (e.keyCode == 13 || e.whiche == 13) {
        //     //             data_tables.search(this.value).draw();
        //     //         }
        //     //     });
        //     // },
        // });

    })
</script>
