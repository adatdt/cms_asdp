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
                                   FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT
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
                                <td style="border-left: none !important;width: 35%">: <?php echo strtoupper($param[3])  ?></td>
                                <td style="border-right: none !important;width: 15%">SHIFT</td>
                                <td style="border-left: none !important;">: <?php echo strtoupper($param[7])  ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">PELABUHAN</td>
                                <td style="border-left: none !important;">: <?php echo strtoupper($param[4])  ?></td>
                                <td style="border-right: none !important;">REGU</td>
                                <td style="border-left: none !important;">: <?php echo $param[8] ?></td>
                            </tr>
                            <tr>
                                <td style="border-right: none !important;">LINTASAN</td>
                                <td style="border-left: none !important;">: <?php echo strtoupper($param[5]).' - '.strtoupper($param[6])  ?></td>
                                <td style="border-right: none !important;">TANGGAL</td>
                                <td style="border-left: none !important;">: <?php echo format_date($param[0]) ?></td>
                            </tr>
                       </table>
                       
                       <table class="table table-333 table-bordered full-width" align="center">
                           <tr>
                               <th class="text-center">NO</th>
                               <th class="text-center">JENIS TIKET</th>
                               <th class="text-center">TARIF <br/>(PAS)</th>
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

                            <?php foreach ($pass['data'] as $pnp) { ?>
                              <tr>
                                  <td></td>
                                  <td>
                                      <?php echo $pnp->name?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($pnp->entry_fee) ?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($pnp->ticket_count) ?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($pnp->total_amount) ?>
                                  </td>
                                  <td class="text-right"></td>
                              </tr>
                          <?php } ?>
                          <tr>
                              <th class="center"></th>
                              <th colspan="2" class="bold">Sub Jumlah</th>
                              <th class="text-right bold"><?php echo idr_currency($pass['produksi']) ?></th>
                              <th class="text-right bold"><?php echo idr_currency($pass['pendapatan']) ?></th>
                              <th></th>
                          </tr>

                           
                            <tr>
                               <td class="text-center">2</td>
                               <td colspan="5">KENDARAAN</td>
                           </tr>
                           <?php foreach ($veh['data'] as $det) { ?>
                              <tr>
                                  <td></td>
                                  <td>
                                      <?php echo $det->name?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($det->entry_fee) ?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($det->ticket_count) ?>
                                  </td>
                                  <td class="text-right">
                                      <?php echo idr_currency($det->total_amount) ?>
                                  </td>
                                  <td class="text-right"></td>
                              </tr>
                          <?php } ?>
                          <tr>
                              <th class="center"></th>
                              <th colspan="2" class="bold">Sub Jumlah</th>
                              <th class="text-right bold"><?php echo idr_currency($veh['produksi']) ?></th>
                              <th class="text-right bold"><?php echo idr_currency($veh['pendapatan']) ?></th>
                              <th></th>
                          </tr>
                          <tr>
                              <th colspan="3" class="bold center">Jumlah</th>
                              <th class="text-right bold"><?php echo idr_currency($pass['produksi']+$veh['produksi']) ?></th>
                              <th class="text-right bold"><?php echo idr_currency($pass['pendapatan']+$veh['pendapatan']) ?></th>
                              <th></th> 
                          </tr>
                       </table>

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
                                <td class="text-center" style="text-decoration: underline; padding-bottom: 0"><?php echo $param[9] ?></td>
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

<script type="text/javascript">


    // function printContent(el){
    //   var restorepage = document.body.innerHTML;
    //   var printcontent = document.getElementById(el).innerHTML;
    //   document.body.innerHTML = printcontent;
    //   window.print();
    //   document.body.innerHTML = restorepage;
    // }

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
