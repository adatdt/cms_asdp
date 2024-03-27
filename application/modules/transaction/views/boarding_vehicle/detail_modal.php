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
                                        <p class="ribbon-content"><?php echo $this->enc->decode($id); ?></p>
                                    </div>
                                </div>

                            </div>

                        </div>
<!--                         <div class="col-md-12">
                            <input class=" form-control input-small pull-right" placeholder="Cari" name="search" id="search">
                        </div> -->
                        <div class="col-md-12">
                            <!-- <div class="table-scrollable"> -->
                                <table class="table table-striped table-bordered table-hover" id="dataTables2">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA PENUMPANG KENDARAAN</th>
                                        </tr>
                                        <tr>

                                            <th>NO</th>
                                            <th>TANGGAL <br>BOARDING</th>

                                            <?php if($gs!="gs") { ?>
                                            <th>NOMER <br>TIKET</th>
                                            <?php } ?>

                                            <th>PELABUHAN</th>
                                            <th>DERMAGA</th>
                                            <th>PENUMPANG <br>KENDARAAN</th>
                                            <th>UMUR</th>
                                            <th>JENIS <br>KELAMIN</th>
                                            <th>SERVIS</th>
                                            <th>TIPE PENUMPANG</th>
                                            <th>LAYANAN</th>
                                            <th>PERANGKAT <br>BOARDING</th>
                                            <th>KETERANGAN</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody id="data_body">
                                        <?php $no=1; foreach ($detail as $key=>$value ) { ?>
                                        <tr>
                                        <td><?php echo $no ?></td>
                                        <td><?php echo empty($value->boarding_date)?"":format_dateTime($value->boarding_date) ?></td>
                                        <?php if($gs!="gs") { ?>
                                        <td><?php echo $value->ticket_number ?></td>
                                        <?php } ?>
                                        <td><?php echo $value->port_name ?></td>
                                        <td><?php echo $value->dock_name ?></td>
                                        <td><?php echo $value->passanger_name ?></td>
                                        <td><?php echo $value->age ?></td>
                                        <td><?php echo $value->gender ?></td>
                                        <td><?php echo $value->service_name ?></td>
                                        <td><?php echo $value->passanger_type_name ?></td>
                                        <td><?php echo $value->ship_class_name ?></td>
                                        <td><?php echo $value->boarding_device_terminal ?></td>
                                        <td><?php echo $value->manifest_data_from ?></td>
                                        </tr> 
                                        <?php $no++; } ?>                            
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        <!-- </div> -->

                    </div>
                </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + '.' + '$2');
        }
        return x1 + x2;
    }

    $(document).ready(function(){


        $("#dataTables2").dataTable({

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
            "initComplete": function () {
	                var $searchInput = $('div#dataTables2_filter input');
	                var data_tables = $('#dataTables2').DataTable();
	                $searchInput.unbind();
	                $searchInput.bind('keyup', function (e) {
	                    if (e.keyCode == 13 || e.whiche == 13) {
	                        data_tables.search(this.value).draw();
	                    }
	                });
	            },           
        })

    })
</script>