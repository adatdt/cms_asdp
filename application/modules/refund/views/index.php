<link href="<?php echo base_url()?>assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
<style> 

.checkbox label:after, 
.radio label:after {
    content: '';
    display: table;
    clear: both;
}

.checkbox .cr,
.radio .cr {
    position: relative;
    display: inline-block;
    border: 1px solid #a9a9a9;
    border-radius: .25em;
    width: 1.3em;
    height: 1.3em;
    /* float: left; */
    margin-right: .5em;
}

.radio .cr {
    border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute;
    font-size: .8em;
    line-height: 0;
    top: 50%;
    left: 20%;
}

.radio .cr .cr-icon {
    margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none;
}

.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg);
    opacity: 0;
    transition: all .3s ease-in;
}

.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg);
    opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5;
}

.table thead {
    text-align: center;
}

.table .btn {
    margin: 5px !important;
}

    th, td { white-space: nowrap; }

table.dataTable tbody th,
table.dataTable tbody td {
    white-space: nowrap;
}

div.dataTables_wrapper
{
    /*width: 800px;*/
    margin: 0 auto;
}

div.DTFC_LeftBodyWrapper table 
{
    
    margin-bottom: 0 !important;
}
div.DTFC_LeftBodyLiner
{
    overflow-x:hidden 
}

.DTFC_LeftBodyWrapper
{
    margin-top : -10px !important;
}

#dataTables_processing
{
    z-index: 1;
}

.bg-icon{
    /* background-color:#337ab7; */
    /* background-color:#0089ff;
    border-radius:25px; */
    background-color:#0089ff;
    border-radius:25px !important;
    padding:3px;
}

/* .usahaCell{
    background-color:#cbeded;
} */
  
</style> 

<?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
<div class="page-content-wrapper">
    <div class="page-content">
    	<div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent . '">' . $parent; ?></a>
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
        
        <br>
        <!-- start: Gate In: Summary -->
        <div class="portlet box blue">
        	<div class="portlet-title">
				<div class="caption"><?php echo $title; ?></div>
                <div class="pull-right btn-add-padding">
                    <?php echo $approve ?>                                   
                </div>
                <div class="pull-right btn-add-padding" style="padding-right: 5px;">
                    <?php echo $downloadExcel; ?>                                   
                </div>
                <div class="pull-right btn-add-padding" style="padding-right: 5px;">
                    <?php echo $downloadPdf; ?>                                   
                </div>                                
            </div>
            <div class="portlet-body">
				<div class="form-inline">
					<div class="input-group">
						<div class="input-group-addon">Tanggal Refund</div>
						<input class="form-control input-small date" id="dateFrom" placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $last_week ?>" readonly>
                        <div class="input-group-addon">s/d</div>
                        <input class="form-control input-small date" id="dateTo" placeholder="YYYY-MM-DD" autocomplete="off" value="<?php echo $now ?>" readonly>
					</div>

                    <div class="input-group">
                        <div class="input-group-addon">Pelabuhan</div>
                        <?php echo form_dropdown("port",$port,""," class='form-control input-small select2' id='port' ") ?>
                    </div>

                    <div class="input-group">
                        <div class="input-group-addon">Status Refund</div>
                        <?php echo form_dropdown("status",$status,""," class='form-control input-small select2' id='status' ") ?>
                    </div>

                    <!-- <div class="input-group">
                        <div class="input-group-addon">SLA > (Hari)</div>
                        <select id="sla" class="form-control select2 input-small" dir="" name="sla">
                                <option value="">Pilih</option>
                                <?php for ($i = 1; $i <= 30; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php } ?>
                        </select>
                    </div> -->
                    
                    <div class="input-group">
                        <div class="input-group-addon">Jenis Refund</div>
                        <?php echo form_dropdown("refund_type", $refund_type, "", " class='form-control input-small select2' id='refund_type' ") ?>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-group-addon">Layanan</div>
                        <?php echo form_dropdown("ship_class", $ship_class, "", " class='form-control input-small select2' id='ship_class' ") ?>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-group-addon">Status Approved By</div>
                        <?php echo form_dropdown("approvedBy", $approvedBy, "", " class='form-control input-small select2' id='approvedBy' ") ?>
                    </div>

                    <div class="input-group pad-top">
                        <div class="input-group-btn">
                            <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Refund
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:;" onclick="myData.changeSearch('Kode Refund','refundCode')">Kode Refund</a>
                                </li>
                                <li>
                                    <a href="javascript:;" onclick="myData.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                </li>                                                                                        
                                <li>
                                    <a href="javascript:;" onclick="myData.changeSearch('Nama PJ','passName')">Nama PJ</a>
                                </li>                                                    
                                <li>
                                    <a href="javascript:;" onclick="myData.changeSearch('No. Rekening','accountNumber')">No. Rekening</a>
                                </li>                                                        
                                                         
                            </ul>
                        </div>
                        <!-- /btn-group -->
                        <input type="text" class="form-control" placeholder="Cari Data" data-name="refundCode" name="searchData" id="searchData" autocomplete="off"> 
                    </div>                          

                    <div class="input-group pad-top">
                        <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                            <span class="ladda-label">Cari</span>
                            <span class="ladda-spinner"></span>
                        </button>
                    </div>                     

				</div>
                <br />

                <div>
                    <table class="table table-bordered table-hover" id="dataTables">

                        <thead>
                            <tr>
                                <th  rowspan="2">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="checkAll" id="checkAll" >
                                            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                        </label>
                                    </div>
                                </th>


                                <th  rowspan="2"> >SLA </th>
                                <th  rowspan="2">NO</th>
                                <th  rowspan="2">KODE BOOKING</th>
                                <th  rowspan="2">NAMA</th>
                                <th  rowspan="2">NO HP</th>
                                <th  rowspan="2">TANGGAL REFUND</th>
                                <th  rowspan="2">KODE REFUND</th>
                                <th  rowspan="2">JENIS REFUND</th>
                                <th  rowspan="2">ASAL</th>
                                <th  rowspan="2">TUJUAN</th>
                                <th  rowspan="2">LAYANAN</th>
                                <th  rowspan="2">JENIS PJ</th>
                                <th  rowspan="2">NO POLISI KENDARAAN</th>
                                <th  rowspan="2">GOLONGAN</th>
                                <th  rowspan="2">NO. REKENING</th>
                                <th  rowspan="2">NAMA PEMILIK REKENING</th>
                                <th  rowspan="2">BANK</th>
                                <th  rowspan="2">HARGA TIKET</th>
                                <th  rowspan="2">BIAYA ADMINISTRASI</th>
                                <th  rowspan="2">BIAYA REFUND</th>
                                <th  rowspan="2">BIAYA TRANSFER</th>
                                <th  rowspan="2">JUMLAH POTONGAN</th>
                                <th  rowspan="2">PENGEMBALIAN DANA</th>
                                <th  rowspan="2">STATUS REFUND</th>
                                
                                <!-- <th  colspan="6"  style='background-color: #ffff6e'>PROSES APPROVAL CONTACT CENTER/ CUSTOMER SERVICE</th>
                                <th  colspan="6"  style='background-color: #ffed95' >PROSES APPROVAL DIVISI USAHA</th>
                                <th  colspan="6" style='background-color: #ffbc8d'>PROSES APPROVAL DIVISI KEUANGAN</th> -->

                                <th  colspan="6"  >PROSES APPROVAL CONTACT CENTER/ CUSTOMER SERVICE</th>
                                <th  colspan="6"   >PROSES APPROVAL DIVISI USAHA</th>
                                <th  colspan="6" >PROSES APPROVAL DIVISI KEUANGAN</th>
                                <th  colspan="2">SLA PENYELESAIAN</th>
                                <th  rowspan="2">AKSI</th>
                            </tr>
                            <tr>
                                <th >STATUS</th>
                                <th >USER</th>
                                <th >TANGGAL</th>
                                <th >SLA HARI KERJA</th>
                                <th >KETERANGAN</th>
                                <th >CATATAN</th>
                                <th >STATUS</th>
                                <th >USER</th>
                                <th >TANGGAL</th>
                                <th >SLA HARI KERJA </th>
                                <th >KETERANGAN</th>
                                <th >CATATAN</th>
                                <th >STATUS</th>
                                <th >USER</th>
                                <th >TANGGAL</th>
                                <th >SLA HARI KERJA</th>
                                <th >KETERANGAN</th>
                                <th >CATATAN</th>
                                <th >DURASI SLA</th>
                                <th >KETERANGAN</th>
                            </tr>                        
                        </thead>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php include "fileJs.php" ?>
<script type="text/javascript">

    var myData = new MyData(); 
    $(document).ready(function () {
    	myData.init();

    	$('.date').datepicker({
    		format: 'yyyy-mm-dd',
    		changeMonth: true,
    		changeYear: true,
    		autoclose: true,
    		todayHighlight: true,
    		
    	})

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
            endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

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
        });

        $("#btn").click(function(){
            let count = $('input.myCheck:checked').length;
            if (count < 1) {
                toastr.error('Tidak ada data yg dipilih', 'Gagal');
            }
            else {
                // myData.approveData();
                // showModal('<?php echo site_url('refund/approve/') ?>'+ value);
                var idApprove=[];
                $('input.myCheck:checkbox:checked').each(function () {
                    idApprove.push($(this).val());
                });

                url = '<?php echo site_url('refund/approve') ?>'
                $.magnificPopup.open({
                    items: {
                        src: url
                    },
                    modal: true,
                    type: 'ajax',
                    tLoading: '<i class="fa fa-refresh fa-spin"></i> Mohon tunggu...',
                    showCloseBtn: false,
                    ajax: {
                        settings: {
                            type: 'POST',
                            data: { 
                                idApprove: idApprove
                            }
                        }
                    }
                });
            }
        })

        $("#checkAll").change(function(){

            if($(this).is(":checked"))
            {
                $(".myCheck").prop('checked',true)
            }
            else
            {
                $(".myCheck").removeAttr('checked')
            }
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click",function(){
            $(this).button('loading');
            myData.reload('#dataTables');
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });

        $("#downloadExcel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port_origin=$("#port").val();
            var refund_type=$("#refund_type").val();
            var searchData=$('#searchData').val();
            var searchName=$("#searchData").attr('data-name');              

            window.location.href="<?php echo site_url('refund/downloadExcelGrid?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port_origin+"&refund_type="+refund_type+"&searchData="+searchData+"&searchName="+searchName;
        });

        $("#downloadPdf").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port_origin=$("#port").val();
            var refund_type=$("#refund_type").val();
            var searchData=$('#searchData').val();
            var searchName=$("#searchData").attr('data-name');              

            window.open("<?php echo site_url('refund/downloadPdf?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port_origin+"&refund_type="+refund_type+"&searchData="+searchData+"&searchName="+searchName,'_blank');
        });                                


    });

</script>


<script src="<?php echo base_url()?>assets/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>