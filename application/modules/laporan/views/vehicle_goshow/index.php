<?php
/*
  Document   : index
  Created on : Oct 16, 2018 1:30:28 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
?>
<div class="page-content-wrapper">
  <div class="page-content">

    <!--    <div class="page-bar">
          <ul class="page-breadcrumb">
            <li>
    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
              <i class="fa fa-circle"></i>
            </li>
            <li>
              <span><?php echo $title; ?></span>
            </li>
          </ul>
    
          <div class="page-toolbar">
            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
              <span class="thin uppercase hidden-xs" id="datetime"></span>
              <script type="text/javascript">window.onload = date_time('datetime');</script>
            </div>
          </div>
        </div>-->

    <!--<br>-->
    <!-- start: Gate In: Summary -->
    <div class="portlet box blue">
      <div class="portlet-title">
        <div class="caption">
          <?php echo $title; ?>
        </div>
      </div>
      <div class="portlet-body">

        <div class="row">
          <div class="col-md-12">
            <div class="form-inline">
              <div class="input-group">
                <div class="input-group-addon">Tanggal Transaksi</div>
                <input class="form-control input-small date" id="datefrom" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                <!--<div class="input-group-addon"><i class="icon-calendar"></i></div>-->
                <!--</div>-->

                <!--<div class="input-group">-->
                <div class="input-group-addon"> s/d </div>
                <input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                <div class="input-group-addon"><i class="icon-calendar"></i></div>
              </div>

              <button type="button " class="btn btn-info ladda-button"   data-style="expand-left" id="cari" ><span class="ladda-label">Cari</span></button>
    <!--          <span id='export_tools'> <a href="javascript:;" data-action="0" class="tool-action btn btn-warning" id="export_tools"><i class="icon-doc"></i> Export</a></span>-->
            </div>   
          </div>  
        </div> 


        <br />

        <table class="table table-bordered table-hover table-striped" id="tblshipincome">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th>Waktu Transaksi</th>
              <th>Shift</th>
              <th>Petugas</th>
              <th>Nomor Polisi</th>
              <th>Golongan</th>
              <th>Panjang (mm)</th>
              <th>Tinggi (mm)</th>
              <th>Berat (kg)</th>
              <th>Tarif (Rp)</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
          <tfoot>

          </tfoot>
        </table>
      </div>
    </div>
    <!-- end: Gate In: Summary -->

  </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/ladda/spin.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/global/plugins/ladda/ladda.min.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assets/global/plugins/ladda/ladda-themeless.min.css') ?>">
<script type="text/javascript">

  var shipincome = {
    loadData: function() {
      var numericColumn = [6, 7, 8, 9];
      var buttonCommon = {
        exportOptions: {
          format: {
            body: function(data, column, row, node) {
//              console.log(data.replace(/\./g, ''));
              return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
            }
          }
        }
      };
      $('#tblshipincome').DataTable({
        "ajax": {
          "url": "<?php echo site_url('laporan/vehicle_goshow/get_list') ?>",
          "type": "POST",
          "data": function(d) {
            d.datefrom = document.getElementById('datefrom').value;
            d.dateto = document.getElementById('dateto').value;
            // d.dateto = document.getElementById('dateto').value;
          },
        },
        "serverSide": true,
        "processing": true,
        "columns": [
          {"data": "no", "orderable": false, "searchable": false, "className": "text-center"},
          {"data": "tx_date", "orderable": true},
          {"data": "shift", "orderable": false},
          {"data": "officer", "orderable": false},
          {"data": "id_number", "orderable": false},
          {"data": "class_name", "orderable": false},
          {"data": "length", "orderable": false, "className": 'text-right'},
          {"data": "height", "orderable": false, "className": 'text-right'},
          {"data": "weight", "orderable": false, "className": 'text-right'},
          {"data": "fare", "orderable": false, "className": 'text-right'},
        ],
        "language": {
          "aria": {
            "sortAscending": ": activate to sort column ascending",
            "sortDescending": ": activate to sort column descending",
          },
          "processing": "Proses.....",
          "emptyTable": "Tidak ada data",
          "info": "Total _TOTAL_ data",
          "infoEmpty": "Total 0 data",
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
//        "searching": false,
        "lengthMenu": [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "all"]
        ],
        "pageLength": 50,
        "pagingType": "bootstrap_full_number",
//        "order": [[1, "asc"]],
        "initComplete": function() {
          var $searchInput = $('div.dataTables_filter input');
          var data_tables = $('#tblshipincome').DataTable();
          $searchInput.unbind();
          $searchInput.bind('keyup', function(e) {
            if (e.keyCode == 13 || e.whiche == 13) {
              data_tables.search(this.value).draw();
            }
          });
        },
//        dom: 'lfrtip',
        dom: 'Br<"table-scrollable"t>i',
//        dom : "<'row'<'col-md-12 col-sm-12'Brt>>" +
//              "<'row'<'col-md-5 col-sm-5'i>>",
        "buttons": [
          <?php if($download_excel){ ?>
            $.extend(true, {}, buttonCommon, {
              extend: 'excelHtml5'
            }),
          <?php } ?>

          <?php if($download_pdf){ ?>
            $.extend(true, {}, {}, {
              extend: 'pdfHtml5',
              orientation: 'landscape',
              customize: function(doc) {
  //              doc.styles.title = {
  //                color: 'white',
  //                fontSize: '40',
  //                background: 'blue',
  //                alignment: 'center'
  //              }

                doc.content[1].table.widths = [20, 100, 50, 100, 70, 70, 80, 65, 65, 70];
  //              console.log(doc);
                var iColumns = $('#tblshipincome thead th').length;

                var rowCount = document.getElementById("tblshipincome").rows.length;

                for (i = 0; i < rowCount; i++) {
                  doc.content[1].table.body[i][0].alignment = 'center';
                  doc.content[1].table.body[i][iColumns - 1].alignment = 'right';
                  doc.content[1].table.body[i][iColumns - 2].alignment = 'right';
                  doc.content[1].table.body[i][iColumns - 3].alignment = 'right';
                  doc.content[1].table.body[i][iColumns - 4].alignment = 'right';

                }
              }
            }),
          <?php } ?>
        ],
//        buttons: [
//          {
//            extend: 'excel',
//          },
//        ]
      });

    },
    reload: function() {
      $('#tblshipincome').DataTable().ajax.reload();
    },
    init: function() {
      if (!jQuery().DataTable) {
        return;
      }

      this.loadData();
    }
  };

  jQuery(document).ready(function() {
    // Chart.init();
    shipincome.init();

//    $('#ship_id').select2({width:"100%"}, {height:"500px"});
    //   $('.select2-selection').css('height', '34px')


    $('#datefrom').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      endDate: new Date(),
    }).on('changeDate',function(e) {
      $('#dateto').datepicker('setStartDate', e.date)
    });

    $('#dateto').datepicker({
      format: 'yyyy-mm-dd',
      changeMonth: true,
      changeYear: true,
      autoclose: true,
      startDate: $('#datefrom').val(),
      endDate: new Date(),
    }).on('changeDate',function(e) {
      $('#datefrom').datepicker('setEndDate', e.date)
    });

    var l;
    $("#cari").click(function() {
      l = Ladda.create(this);
      l.start();
      shipincome.reload();

      $('#tblshipincome').on('draw.dt', function() {
        l.stop();
      });

    });

    setTimeout(function() {
      $('.menu-toggler').trigger('click');
    }, 1);

//    Ladda.bind('#cari');

  });
</script>