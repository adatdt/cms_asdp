<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
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
        </div>

        <?php $now=date("Y-m-d"); $last_month=date('Y-m-d',strtotime("-1 month"))?>

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <!-- <div class="pull-right btn-add-padding" style="padding-left: 10px"><?php //echo $btn_add; ?></div> -->
                    <div class="pull-right btn-add-padding">
                        <button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>
                        <button class="btn btn-sm btn-warning download" id="download_pdf" target="_blank">Pdf</button>
                    </div>
                   
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
 
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal Daftar</div>
                                    <input type="text" class="form-control  input-small" id="dateFrom" value="<?php echo $last_month; ?>" readonly>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" class="form-control  input-small" id="dateTo" value="<?php echo $now; ?>" readonly>
                                </div>


                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Total Hari Terakhir Booking </div>
                                    <input type="text" class="form-control  input-small" onkeypress="return isNumberKey(event)" id="totalBooking" placeholder="Hari" value=30>
                                </div>


                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Status</div>
                                    <select id="status" class="form-control js-data-example-ajax select2 input-small" dir="" name="method">
                                        <option value="">Pilih</option>                                        
                                        <option value="<?php echo $this->enc->encode("0"); ?>">Tidak Aktif</option>
                                        <option value="<?php echo $this->enc->encode("1"); ?>">Aktif</option>
                                        <option value="<?php echo $this->enc->encode("-1"); ?>">Temp Banned</option>
                                        <option value="<?php echo $this->enc->encode("-2"); ?>">Permanent Banned</option>
                                    </select>
                                </div> 

                               <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Email
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Email','email')">Email</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Nama','name')">Nama</a>
                                            </li>                                            
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="email" name="searchData" id="searchData"> 
                                </div>   
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
                                <th>EMAIL</th>
                                <th>NO TELPON</th>  
                                <th>JUMLAH BOOKING <span class="headTotalBooking">30</span><br> HARI TERAKHIR</th>
                                <th>JUMLAH BOOKING <span class="headTotalBooking">30</span><br> HARI TERAKHIR SUKSES</th>  
                                <th>STATUS</th>
                                <th>TANGGAL REGISTRASI</th>
                                <th>TANGGAL BANNED EXPIRED</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AKSI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
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

class myData {
    loadData() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('master_data/memberBlock') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.status = document.getElementById('status').value;
                    d.totalBooking = document.getElementById('totalBooking').value;
                    d.searchName=$("#searchData").attr('data-name');
                    d.searchData=document.getElementById('searchData').value;
                },
            },

         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "full_name", "orderable": true, "className": "text-left"},
                    {"data": "email", "orderable": true, "className": "text-left"},
                    {"data": "phone_number", "orderable": true, "className": "text-left"},
                    {"data": "total_booking", "orderable": true, "className": "text-left"},
                    {"data": "total_booking_bayar", "orderable": true, "className": "text-left"},
                    {"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "tanggal_pendaftaran", "orderable": true, "className": "text-left"},
                    {"data": "blocking_expired", "orderable": true, "className": "text-left"},
                    {"data": "actions", "orderable": false, "className": "text-center"},

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
            "searching" : false,
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
            fnDrawCallback: function(allRow)
            {   

                if(allRow.json.recordsTotal)
                {
                    $('.download').prop('disabled',false);
                }
                else
                {
                    $('.download').prop('disabled',true);
                }

                $(".headTotalBooking").html(allRow.json.totalBooking)
            }
        });

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#dataTables').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    }

    reload()
    {
        $('#dataTables').DataTable().ajax.reload();
    }

    init()
    {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }

    changeSearch(x,name)
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);

    }

    formatDate=(date)=> {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;

        return [year, month, day].join('-');
    }
};

var table = new myData();
jQuery(document).ready(function () {
    table.init();

    $("#download_excel").click(function(event) {
            var dateFrom = document.getElementById('dateFrom').value;
            var dateTo = document.getElementById('dateTo').value;
            var status = document.getElementById('status').value;
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;

            window.location.href = "<?php echo site_url('master_data/member/downloadExcel?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&status=" + status + "&searchData=" + searchData + "&searchName=" + searchName;
    });

    $("#download_pdf").click(function(event) {
            var dateFrom = document.getElementById('dateFrom').value;
            var dateTo = document.getElementById('dateTo').value;
            var status = document.getElementById('status').value;
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;

            window.open("<?php echo site_url('master_data/member/downloadPdf?') ?>dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&status=" + status + "&searchData=" + searchData + "&searchName=" + searchName);
    });

    $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+6);
            someDate.getFullYear();
            let endDate=table.formatDate(someDate);
            // destroy ini firts setting
            $('#dateTo').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")
            // myData.reload();
        });


        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });


});
</script>
