<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');
?>
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="portlet box blue">
      <div class="portlet-title">
        <div class="caption">
          <?php echo $title; ?>
        </div>
        <!--<div class="tools">-->
        <div class="pull-right btn-add-padding">
          <a href="<?php echo site_url('laporan/ticket_reservasi') ?>" class="btn btn-sm btn-warning">Kembali</a>
          </div>
        <!--</div>-->
      </div>
      <div class="portlet-body" style="padding-top: 0px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light" style="padding: 1px">
                            <div class="portlet-body">
                                <div class="row number-stats margin-bottom-30">
                                    <div class="col-md-4">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Nama Kapal </div>
                                                <div class="my-number"> <?php echo $header->name ?> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Tanggal Keberangkatan </div>
                                                <div class="my-number"> <?php echo date('d M Y',strtotime($header->schedule_date)) ?> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Total Pendapatan </div>
                                                <div class="my-number" id="total"> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <span id="btnpdf"> <a  class="tool-action btn btn-warning" id="export_tools">Pdf</a></span> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-striped" id="tblreservasi">
                    <thead>
                        <tr>
                            <!-- <th> No </th> -->
                            <!-- <th> Tanggal Keberangkatan </th> -->
                            <!-- <th> Nama Kapal </th> -->
                            <th> Golongan </th>
                            <th> Produksi </th>
                            <th> Pendapatan (Rp) </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  var reservasi = {
    loadData: function() {
      var numericColumn = [2,3];
      var buttonCommon = {
        exportOptions: {
          format: {
            body: function(data, column, row, node) {
              return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
            }
          }
        }
      };
      $('#tblreservasi').DataTable({
        "ajax": {
          "url": "<?php echo $url ?>",
          "type": "POST",
        },
        "serverSide": true,
        "processing": true,
        "columns": [
          // {"data": "payment_date", "orderable": false},
          // {"data": "created_on", "orderable": false},
          {"data": "golongan", "orderable": false},
          {"data": "produksi", "orderable": false, className: 'text-right'},
          {"data": "pendapatan", "orderable": false, className: 'text-right'},
        ],
        "language": {
          "aria": {
            "sortAscending": ": activate to sort column ascending",
            "sortDescending": ": activate to sort column descending",
          },
          "processing": "Proses.....",
          "emptyTable": "Tidak ada data",
//          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "info": "Total _TOTAL_ data",
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
        "searching": false,
        "lengthMenu": [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "all"]
        ],
        "pageLength": -1,
        "pagingType": "bootstrap_full_number",
       "order": [[1, "ASC"]],
        dom: 'B<"table-scrollable"t>ri',
        "buttons": [
          <?php if($download_excel){ ?>
            $.extend(true, {}, buttonCommon, {
              extend: 'excelHtml5'
            }),
          <?php } ?>

          <?php if($download_pdf){ ?>
            'pdf'
          <?php } ?>
        ],

        "fnDrawCallback": function(data) {
          $('#total').html('<h4>Rp.'+data.json.total_semua+'</h4>');
          console.log(data);
        },
        
        "initComplete": function(data) {
          // console.log(data);
          // $.each(data,function(i,e){
            // console.log(i)
          //   // total += e.json.pendapatan;
          // })
          // $('#total').html('Rp' + data.json.total_semua);
        },
      });

    },

    reload: function() {
      $('#tblreservasi').DataTable().ajax.reload();
    },
    init: function() {
      if (!jQuery().DataTable) {
        return;
      }

      this.loadData();
    }
  };

  jQuery(document).ready(function() {
    reservasi.init();

    $('.date').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      todayHighlight: true,
    }).on('changeDate', function(e) {
      //reservasi.reload();
    });

    $("#cari").on("click", function() {
      reservasi.reload();
    });
  });
</script>