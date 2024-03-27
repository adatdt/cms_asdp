<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">

                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Boarding Code</div>
                                        <p class="ribbon-content"><?php echo $data_boarding->boarding_code; ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Kapal</div>
                                        <p class="ribbon-content" id="status"><?php echo $data_boarding->kapal;  ?></p>
                                    </div>
                                </div>


                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Pelabuhan</div>
                                        <p class="ribbon-content"><?php echo $data_boarding->pelabuhan; ?></p>
                                    </div>
                                </div>

                

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Dermaga</div>
                                        <p class="ribbon-content"><?php echo $data_boarding->dermaga;  ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-md-12">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                      <p></p>
                                            <table class="table table-bordered table-striped   table-hover" id="table_siwasops">
                                                <thead>
                                                    <tr>
                                                        <th colspan="16" style="text-align: left">DATA LOG SIWASOPS</th>
                                                    </tr>
                                                    <tr>
                                                        <th>NO</th>
                                                        <th>STATUS</th>
                                                        <th>KETERANGAN</th>
                                                        <th>WAKTU PENGIRIMAN</th>
                                                        <th>TYPE</th>
                                                        <th>REQUEST</th>
                                                        <th>RESPONSE</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="data_body">
                                                    <?php $no1 = 1;
                                                    foreach ($data_log as $key => $value) {  ?>

                                                        <tr>
                                                            <td><?php echo $no1; ?></td>
                                                            <td><?php echo ($value->status == 1) ? success_label('Success') : failed_label('Failed'); ?></td>
                                                            <td><?php echo $value->description; ?></td>
                                                            <td><?php echo empty($value->created_on) ? "" : format_dateTimeHis($value->created_on); ?></td>
                                                            <td>
                                                                <?php if($value->type == 1) {
                                                                    echo "Login";
                                                                } else if($value->type == 2) {
                                                                    echo "Send Manifest";
                                                                }
                                                                 ?>
                                                            </td>
                                                            <td><?php echo $value->request; ?></td>
                                                            <td><?php echo $value->response; ?></td>
                                                        </tr>
                                                    <?php $no1++;
                                                    } ?>
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                            <!-- </div> -->
                                </div>

                                
                                </div>
                            </div>
                        </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>

            </div>
        </div>
    </div>
</div>

    <script type="text/javascript"> 

        $(document).ready(function() {

            // $("#btnapprove").click(function(event){

            //     approve();
            // });

            $("#table_siwasops").DataTable({

                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "processing": "Proses.....",
                    "emptyTable": "Tidak ada data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "lengthMenu": "Menampilkan _MENU_",
                    "search": "Pencarian :",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Selanjutnya",
                        "last": "Terakhir",
                        "first": "Pertama"
                    }
                },
                "lengthMenu": [
                    [5, 10, 25, 50, 100],
                    [5, 10, 25, 50, 100]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [
                    [0, "asc"]
                ],
            });



            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            });

        })
    </script>