<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline">
                    <div class="row">
                        <div class="col-md-12"> 
                            
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">PELABUHAN </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="0">All</option>
                                    <option value="1">Merak</option>
                                    <option value="2">Bakauheni</option>
                                    <option value="3">Gilimanuk</option>
                                    <option value="4">Ketapang</option>
                                </select>
                            </div>
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">LOKET </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="0">1</option>
                                    <option value="1">2</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">TANGGAL</div>
                                <input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div> 
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">REGU </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="1">ALL</option>
                                    <option value="2">I</option>
                                    <option value="3">II</option>
                                    <option value="3">III</option>
                                    <option value="4">IV</option>
                                </select>
                            </div>
                            <div class="input-group select2-bootstrap-prepend">
                                <div class="input-group-addon">SHIFT </div>
                                <select id="select2-button-addons-single-input-group-sm" class="form-control js-data-example-ajax select2" dir="">
                                    <option value="1">ALL</option>
                                    <option value="1">Pagi</option>
                                    <option value="2">Siang</option>
                                    <option value="3">Malam</option>
                                </select>
                            </div> 
                            <button class="btn btn-danger">Cari</button>                            
                        </div>
                    </div>
                </div>                
                <table class="table table-bordered table-hover table-striped" id="tblrevenue">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>REGU</th>                            
                            <th>SHIFT</th>
                            <th>LOKET</th>
                            <th>ACTION</th>
                        </tr>                       
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">01/02/2019</td>
                            <td class="text-center">I</td>
                            <td class="text-center">Pagi</td>
                            <td class="text-center">1</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">01/02/2019</td>
                            <td class="text-center">I</td>
                            <td class="text-center">Pagi</td>
                            <td class="text-center">2</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">02/02/2019</td>
                            <td class="text-center">I</td>
                            <td class="text-center">Pagi</td>
                            <td class="text-center">1</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">02/02/2019</td>
                            <td class="text-center">I</td>
                            <td class="text-center">Sore</td>
                            <td class="text-center">2</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">02/02/2019</td>
                            <td class="text-center">II</td>
                            <td class="text-center">Malam</td>
                            <td class="text-center">2</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">03/02/2019</td>
                            <td class="text-center">III</td>
                            <td class="text-center">Pagi</td>
                            <td class="text-center">1</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>
                        <tr>
                            <td class="text-center">01/02/2019</td>
                            <td class="text-center">IV</td>
                            <td class="text-center">Pagi</td>
                            <td class="text-center">1</td>
                            <td class="text-center"><span class="btnpdf"> <a  class="tool-action btn btn-danger btn-sm" id="export_tools">Pdf</a></span></td>
                        </tr>

                    </tbody>                    
                </table>
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
    $(".btnpdf").click(function(event){
        window.open("<?php echo site_url('laporan/penyerahan_hasil_penjualan/download_pdf') ?>");
    });

var shipincome= {
    loadData: function() {
        
        $('#tblrevenue').DataTable({
            "bStateSave": true,
            "bInfo": false,
            "searching":false,
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "paging": false,
            "order": [[0, "asc" ]],
            "fixedHeader": {
                "headerOffset": $('.navbar-fixed-top').outerHeight()
            }
        });

        $('#export_tools2  > a.tool-action').on('click', function() {
            var data_tables = $('#tblrevenue').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tblrevenue').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

jQuery(document).ready(function () {
    shipincome.init();

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
    
    $("#cari").on("click",function(){
        $(this).button('loading');
        shipincome.reload();
        $('#tblrevenue').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>