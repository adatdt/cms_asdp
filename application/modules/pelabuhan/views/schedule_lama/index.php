<div class="page-content-wrapper">
  <div class="page-content">
    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li><?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?><i class="fa fa-circle"></i></li>
        <li><span><?php echo $title; ?></span></li>
      </ul>

      <div class="page-toolbar">
        <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
          <span class="thin uppercase hidden-xs" id="datetime"></span>
          <script type="text/javascript">window.onload = date_time('datetime');</script>
        </div>
      </div>
    </div>

    <div class="my-div-body">
      <div class="portlet box blue-madison">
        <div class="portlet-title">
          <div class="caption"><?php echo $title; ?></div>
          <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
        </div>
        <div class="portlet-body" >
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 mar-bottom">
                <div class="input-group">
                  <div class="input-group-addon">Pelabuhan Asal</div>
                  <?php echo form_dropdown('', $port, '', 'class="form-control" data-placeholder="Pilih Pelabuhan Asal" id="origin" style="width: 100%"'); ?>
                </div>
              </div>
              <div class="col-md-4 mar-bottom">
                <div class="input-group">
                  <div class="input-group-addon">Pelabuhan Tujuan</div>
                  <select class="form-control" data-placeholder="Pilih Pelabuhan Tujuan" id="destination" style="width: 100%"></select>
                </div>
              </div>
              <div class="col-md-4 mar-bottom">
                <!-- <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Nama Pelabuhan" id="search" autocomplete="off">
                  <span class="input-group-btn"> -->
                    <button type="button" class="btn btn-sm btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                  <!-- </span>
                </div> -->
              </div>
            </div>
          </div>

          <table class="table table-bordered table-hover " id="dataTables">
            <thead>
              <tr>
                <th>No</th>
                <th>Pelabuhan Keberangkatan</th> 
                <th>Pelabuhan Tujuan</th> 
                <th>Action</th>
              </tr>
            </thead>
            <tfoot></tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var tDataTables = function() {
    var initTable1 = function() {
      var table = $('#dataTables');

      // begin first table
      table.dataTable({
        "ajax": {
          "url": "<?php echo site_url('pelabuhan/schedule') ?>", "type": "POST",
          "data": function(d) {
            d.origin     = $("#origin").val();
            d.destinaton = $("#destination").val();
            // d.search = $("#search").val();
          }, 
        },

        "serverSide": true,
        "processing": true,
        "searching": false,
        "columns": [
          {"data": "number", "orderable": false, "className": "text-center", "width": 20},
          {"data": "destination_name", "orderable": true, "className": "text-left"},
          {"data": "origin_name", "orderable": true, "className": "text-left"},
          // {"data": "ship_name", "orderable": true, "className": "text-left", "width":"50%"},
          {"data": "actions", "orderable": false, "className": "text-center"}
        ],

        // Internationalisation. For more info refer to http://datatables.net/manual/i18n
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

        // "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
        "lengthMenu": [
          [10, 15, 25, -1],
          [10, 15, 25, "All"] // change per page values here
        ],

        // set the initial value
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",

        // "columnDefs": [{
        //   "targets": [1,2],
        //   render: $.fn.dataTable.render.text()
        // }],
        "order": [
          [1, "asc"]
        ], 

        // set first column as a default sort by asc
        // users keypress on search data
        "initComplete": function() {
          var $searchInput = $('div.dataTables_filter input');
          var data_tables = $('#dataTables').DataTable();
          $searchInput.unbind();
          $searchInput.bind('keyup', function(e) {
            if (e.keyCode == 13) {
              data_tables.search(this.value).draw();
            }
          });
        },
      });
    }

    return {
      //main function to initiate the module
      init: function() {
        if (!jQuery().dataTable) {
          return;
        }
        initTable1();
      }
    };
  }();

  jQuery(document).ready(function() {
    tDataTables.init();

    $('#origin').select2()
    $('#destination').select2()

    $('#origin').change(function(){
      val = $(this).val();
      $.ajax({
        url         : 'schedule/port_destination',
        data        : {id : val},
        type        : 'POST',
        dataType    : 'json',

        beforeSend: function(){},

        success: function(json) {
          $("#destination").html('');
          $('#destination').select2({
            data: json.data
          })

          $('.select2').removeClass('select2-container--bootstrap')
          $('.select2').addClass('select2-container--default')
        },

        error: function() {
          toastr.error('Silahkan Hubungi Administrator', 'Gagal');
        },

        complete: function(){}
      });
    })

    // $('#search').keydown(function(e) {
    //   if (e.which == 13){
    //     $('#dataTables').DataTable().ajax.reload();   
    //   }
    // })

    $('#cari').click(function(e) {
      $(this).button('loading');
      $('#dataTables').DataTable().ajax.reload();
      $('#dataTables').on('draw.dt', function() {
        $("#cari").button('reset');
      });
    })
  })
</script>