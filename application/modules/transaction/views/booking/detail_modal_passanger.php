<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <div class="row">

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Kode Booking</div>
                                        <p class="ribbon-content"><?php echo $booking_code?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary uppercase">Total Penumpang</div>
                                        <p class="ribbon-content"><?php 
                                                                        // echo $header_data[0]->total_passanger
                                                                        echo count((array)$passanger) 

                                                                    ?></p>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-xs-12">
                                    <div class="mt-element-ribbon bg-grey-steel">
                                        <div class="ribbon ribbon-color-primary ">TOTAL HARGA (Rp.)</div>
                                        <p class="ribbon-content"><?php echo idr_currency($header_data[0]->grand_total) ?></p>
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
                                    <ul class="nav nav-tabs " role="tablist">
                                        <li class="nav-item active">
                                                <a class="label label-primary " data-toggle="tab" href="#penumpang">Data Penumpang</a>
                                        </li>
                                    </ul>
                  
                                    <div class="tab-content " >
                                        <!-- Data Penumpang jalan kaki -->
                                        <div class="tab-pane active" id="penumpang" role="tabpanel" >
                                            <table class="table table-bordered table-striped   table-hover" id="dataTables2" >
                                                <thead>
                                                    <tr>
                                                        <th colspan="16" style="text-align: left">DATA PENUMPANG</th>
                                                    </tr>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NOMER BOOKING</th>
                                                    <th>NOMER TIKET</th>
                                                    <th>NOMER IDENTITAS</th>
                                                    <th>JENIS IDENTITAS</th>
                                                    <th>NAMA</th>
                                                    <th>ALAMAT</th>
                                                    <th>JENIS KELAMIN</th>
                                                    <th>USIA</th>
                                                    <th>SERVIS</th>
                                                    <th>TIPE PENUMPANG</th>
                                                    <th>TIPE KAPAL</th>
                                                    <th>KEBERANGKATAN</th>
                                                    <th>TUJUAN</th>
                                                    <th>TARIF (Rp.)</th>
                                                </tr>
                                                </thead>
                                                <tbody id="data_body">
                                                    <?php $no2=1; foreach($passanger as $key=>$value) { ?>
                                                    <tr>
                                                        <td><?php echo $no2 ?></td>
                                                        <td><?php echo $value->booking_code ?></td>
                                                        <td><?php echo $value->ticket_number ?></td>
                                                        <td><?php echo $value->id_number ?></td>
                                                        <td><?php echo $value->identity_name ?></td>
                                                        <td><?php echo $value->name ?></td>
                                                        <td><?php echo $value->city ?></td>
                                                        <td><?php echo $value->gender ?></td>
                                                        <td><?php echo $value->age ?></td>
                                                        <td><?php echo $value->service_name ?></td>
                                                        <td><?php echo $value->passenger_type_name ?></td>
                                                        <td><?php echo $value->shift_class_name ?></td>
                                                        <td><?php echo $value->origin_name ?></td>
                                                        <td><?php echo $value->destination_name ?></td>
                                                        <td align="right"><?php echo idr_currency($value->fare) ?></td> 
                                                    </tr>
                                                <?php $no2++; } ?>

                                                </tbody>

                                                <tfoot></tfoot>
                                            </table>
                                        </div>

                                    </div>      
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

    var passanger={loadData: function(){

            $("#dataTables2").DataTable({            
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
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "order": [[ 0, "asc" ]],
                "ordering": false,
            });
        },

        reload: function() {
            $('#dataTables2').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }


    }


    $(document).ready(function(){
        passanger.init();

    });
</script>