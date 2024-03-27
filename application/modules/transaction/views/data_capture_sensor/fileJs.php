<script type="text/javascript">
class MyData {

    get loadData(){
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction/data_capture_sensor') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.searchData        =document.getElementById('searchData').value;
                    d.searchName        =$("#searchData").attr('data-name');
                    // d.shipClass = document.getElementById('shipClass').value;
                    d.port_origin = document.getElementById('port').value;
                    // d.route = document.getElementById('route').value;
                    // d.paymentDateFrom = document.getElementById('checkinDateFrom').value;
                    // d.paymentDateTo = document.getElementById('checkinDateTo').value;
                },
            },

         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "created_on", "orderable": false, "className": "text-center" },
                    {"data": "origin", "orderable": true, "className": "text-center" },
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "vehicle_class_name", "orderable": true, "className": "text-left"},
                    {"data": "length_cam", "orderable": true, "className": "text-right"},
                    {"data": "height_cam", "orderable": true, "className": "text-right"},
                    {"data": "width_cam", "orderable": true, "className": "text-right"},
                    {"data": "weighbridge", "orderable": true, "className": "text-right"},
                    {"data": "vehicle_length_cam", "orderable": true, "className": "text-left"},
                    {"data": "status_vehicle", "orderable": true, "className": "text-center"},
                    {"data": "id_image", "orderable": true, "className": "text-left"},
                    // {"data": "description", "orderable": true, "className": "text-left"},
                    // {"data": "old_vehicle_class_name", "orderable": true, "className": "text-left"},
                    // {"data": "old_fare", "orderable": true, "className": "text-right"},
                    // {"data": "new_vehicle_class_name", "orderable": true, "className": "text-left"},
                    // {"data": "new_fare", "orderable": true, "className": "text-right"},
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
            "searching": false,
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
                // console.log(allRow);
                if(allRow.json.recordsTotal)
                {
                    $('.download').prop('disabled',false);
                }
                else
                {
                    $('.download').prop('disabled',true);
                }
            }
        });

    }

    get reload() {
        $('#dataTables').DataTable().ajax.reload();
    }

    get init() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData;
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
    
    changeSearch(x,name)
	    {
	    	$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData").attr('data-name', name);

	    }

    route(data) {

        // console.log(data);
        $.ajax({

            type:"post",
            dataType:"json",
            url:"<?php echo site_url()?>transaction2/passangerReservation/getRoute",
            data:"port="+data.port,
            success : (x)=>{
                
                var html="<option value=''>Pilih</option>";

                if(x.length>0)
                {
                    for(var i=0; i<x.length; i++)
                    {
                        html +="<option value='"+x[i].id+"'>"+x[i].route_name+"</option>";
                    }

                }

                $("#route").html(html);
            }
        })
    }

}


</script>