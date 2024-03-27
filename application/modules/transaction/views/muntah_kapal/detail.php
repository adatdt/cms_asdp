<div class="col-md-12 col-md-offset-0" >
    <div class="portlet box blue" id="box" >
        <?php echo headerForm($title) ?>
        <div class="portlet-body" >

            <div class="row">
                <div class="col-md-12">
                       
                    <div class="portlet-body">
                        <div class="table-toolbar">
                            <!-- <div class="row">

                                <div class="col-md-3">
                                   
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Kode Booking</h4>
                                        <div class="widget-thumb-wrap">
                                           
                                            <div class="widget-thumb-body">
                                               
                                                <span class="widget-thumb-body-stat" data-counter="counterup" >
                                                    <?php echo $this->enc->decode($id); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>

                                <div class="col-md-3">
                                  
                                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                                        <h4 class="widget-thumb-heading">Total Penumpang</h4>
                                        <div class="widget-thumb-wrap">
                                          
                                            <div class="widget-thumb-body">
                                               
                                                <span class="widget-thumb-body-stat" data-counter="counterup" id="total"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                        </div>
                        <div class="col-md-12">
                            <!-- <input class=" form-control input-small pull-right"> -->
                        </div>

                        <div class="col-md-12">
                                <table class="table table-striped table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th colspan="16" style="text-align: left">DATA PENUMPANG</th>
                                        </tr>
                                        <tr>
                                            <th>NO</th>
                                            <th>NOMOR BOOKING</th>
                                            <th>NOMOR TIKET</th>
                                            <th>NAMA</th>
                                            <th>NO. ID</th>
                                            <th>LAYANAN</th>
                                        </tr>
                                    </thead>
                                    <tbody id="data_body">                                
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            <!-- </div> -->
                        </div>

                        <div class="col-md-12">
                            <!-- <input class=" form-control input-small pull-right"> -->
                        </div>

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

    function get_data(){
        $.ajax({
            type:"post",
            url :"<?php echo site_url()?>transaction/muntah_kapal/listDetail",
            data:"id=<?php echo $id ?>",
            dataType:"json",
            success:function(x){

                var html="";
                var no=1;
                var total=0;
                for(var i=0; i<x.length;i++)
                {
                    html +="<tr>"+
                            "<td>"+no+"</td>"+
                            "<td>"+x[i].booking_code+"</td>"+
                            "<td>"+x[i].ticket_number+"</td>"+
                            "<td>"+x[i].name+"</td>"+
                            "<td>"+x[i].id_number+"</td>"+
                            "<td>"+x[i].service_name+"</td>"+
                           
                            "</tr>";
                            no++;

                            total += x[i].fare << 0;
                }
             // console.log(html);


                $("#data_body").html(html); 
                $("#total").html(x.length); 

                $("#total_fare").html(addCommas(total)); 
            }
        });
    }
    var table= {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('transaction/muntah_kapal/listDetail') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.id = '<?php echo $id; ?>';
                    },
                },
             
                "serverSide": true,
                "processing": true,
                "columns": [
                        {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                        {"data": "booking_code", "orderable": true, "className": "text-left"},
                        {"data": "ticket_number", "orderable": true, "className": "text-left"},
                        {"data": "name", "orderable": true, "className": "text-left"},
                        {"data": "id_number", "orderable": true, "className": "text-left"},
                        {"data": "service_name", "orderable": true, "className": "text-left"}
                ],
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
                "order": [[ 0, "desc" ]],
                "initComplete": function () {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                },
            });

            $('#export_tools > li > a.tool-action').on('click', function() {
                var data_tables = $('#dataTables').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#dataTables').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }

            this.loadData();
        }
    };

    jQuery(document).ready(function () {
        table.init();

        // setTimeout(function() {
        //     $('.menu-toggler').trigger('click');
        // }, 1);
        
    });
    // $(document).ready(function(){

    //     get_data();

    //     $("#dataTables2").DataTable({            
    //         "language": {
    //             "aria": {
    //                 "sortAscending": ": activate to sort column ascending",
    //                 "sortDescending": ": activate to sort column descending"
    //             },
    //               "processing": "Proses.....",
    //               "emptyTable": "Tidak ada data",
    //               "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
    //               "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
    //               "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
    //               "lengthMenu": "Menampilkan _MENU_",
    //               "search": "Pencarian :",
    //               "zeroRecords": "Tidak ditemukan data yang sesuai",
    //               "paginate": {
    //                 "previous": "Sebelumnya",
    //                 "next": "Selanjutnya",
    //                 "last": "Terakhir",
    //                 "first": "Pertama"
    //             }
    //         },
    //         "lengthMenu": [
    //             [10, 25, 50, 100],
    //             [10, 25, 50, 100]
    //         ],
    //         "pageLength": 10,
    //         "pagingType": "bootstrap_full_number",
    //         "order": [[ 0, "desc" ]],
    //     });
    // })


</script>
