<script type="text/javascript">
	class MyData{


    loadData() 
    {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporanv2/vaccineReport') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.startJam = document.getElementById('startJam').value;
                    d.searchData=document.getElementById('searchData').value;
                    d.shipClass=document.getElementById('shipClass').value;
                    d.masterStatus=document.getElementById('masterStatus').value;
                    d.vehicleClass=document.getElementById('vehicleClass').value;
                    d.passangerType=document.getElementById('passangerType').value;
                    d.statusValid=document.getElementById('statusValid').value;
                    d.service=document.getElementById('service').value;
                    d.searchName=$("#searchData").attr('data-name');
                },
            },

            "serverSide": true,
            "filter":false,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-right"},
                    {"data": "service_name", "orderable": true, "className": "text-center"},
                    {"data": "ship_class_name", "orderable": true, "className": "text-right"},
                    {"data": "vehicle_class_name", "orderable": true, "className": "text-right"},
                    {"data": "plat_no", "orderable": true, "className": "text-right"},
                    {"data": "passanger_type_name", "orderable": true, "className": "text-left"},
                    {"data": "name", "orderable": true, "className": "text-left"},
                    {"data": "type_id_name", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "age", "orderable": true, "className": "text-left"},
                    {"data": "gender", "orderable": true, "className": "text-left"},
                    {"data": "city", "orderable": true, "className": "text-left"},
                    {"data": "add_manifest_channel", "orderable": true, "className": "text-center"},
                    {"data": "depart_date", "orderable": true, "className": "text-left"},
                    {"data": "depart_time_start", "orderable": true, "className": "text-left"},
                    {"data": "description", "orderable": true, "className": "text-center"},
                    {"data": "ship_name", "orderable": true, "className": "text-left"},
                    {"data": "vaccine", "orderable": true, "className": "text-center"},
                    {"data": "vaccine_status_pl", "orderable": true, "className": "text-center"},
                    {"data": "testCovid", "orderable": true, "className": "text-left"},
                    {"data": "reason", "orderable": true, "className": "text-left", width : '50px' },
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
            scrollX:        true,
            paging:         true,
            fixedColumns:   {
                leftColumns: 3,
                // left: 1,
                // right: 1
            },            
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
    }

    reload(){
        $('#dataTables').DataTable().ajax.reload();
    }

    init() 
    {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }

    addInputFile(data)
    {
    	var html =`

            <div class="col-md-6 formInput${indexData} " >                            
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <label>Pilih File gambar <span class="wajib">*</span></label>
                    <div class="input-group ">

                    	<span class="input-group-addon btn  red btn-md btn-danger deleteFormInput" data-id="${indexData}" >
                    		Hapus Input
                    	</span>
                        <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                            <span class="fileinput-filename"> </span>
                        </div>
                        <span class="input-group-addon btn default btn-file">
                            <span class="fileinput-new"> Pilih File </span>
                            <span class="fileinput-exists"> Pilih File</span>
                            <input type="hidden"><input type="hidden"><input type="file" name="fileName[${indexData}]"  ></span>
                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                    </div>

                </div>
                <input type="hidden" name="checkFileEmpty[${indexData}]" value="${indexData}">
            </div>
            <div class="col-md-12 formInput${indexData} " ></div>

    	`

    	$(html).insertBefore("#fileInput");

    	$(".deleteFormInput").on("click",function(){
            var id=$(this).attr("data-id");
            $(`.formInput${id}`).remove();
        })        
    }

    getFileEdit(data)
    {
    	$.ajax({

    		url:"<?= site_url()?>pids/adsDisplay/getDetailFile",
    		data:data,
    		type:"post",
    		cache: false,
    		dataType:"json",
    		success:function(x)
    		{
    			var html=``;
    			for(var i in x)
    			{
    				html += myData.fileEdit(x[i]);
    				indexData++;
    			}

    			$(html).insertBefore("#fileInput");

		    	$(".deleteFormInput").on("click",function(){
		            var id=$(this).attr("data-id");
		            $(`.formInput${id}`).remove();
		        })                
    		},
            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            }
    	});

    }
    fileEdit(data)
    {
    	
        var html=`
        		<div class="col-md-12 formInput${indexData}"> <img src="<?=base_url() ?>${data.path_file}" width="100px" /> </div>
        		<div class="col-md-7 formInput${indexData}">
        			<input type="hidden" name="editFile[${indexData}]" value="${data.path_file}">                            
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <label>Pilih File gambar <span class="wajib">*</span></label>
                        <div class="input-group ">`

            if(indexData>0)
            {

            	html +=`<span class="input-group-addon btn  red btn-md btn-danger deleteFormInput" data-id="${indexData}" >
                    		Hapus Input
                    	</span>`
            }


            html   +=`      <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                <span class="fileinput-filename"> </span>
                            </div>
                            <span class="input-group-addon btn default btn-file">
                                <span class="fileinput-new"> Pilih File </span>
                                <span class="fileinput-exists"> Pilih File</span>
                                <input type="hidden"><input type="hidden"><input type="file" name="fileName[${indexData}]"  ></span>
                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Hapus </a>
                        </div>
                    </div>

                    <input type="hidden" name="checkFileEmpty[${indexData}]" value="${indexData}">                    
                </div>    
                
                <div class="col-md-12 formInput${indexData}"> </div>`



        return html;


                            
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

}		


</script>