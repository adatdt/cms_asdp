<script type="text/javascript">
class MyData {

    get loadData(){
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('transaction2/dueDateExtends') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                },
            },

         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "created_on", "orderable": true, "className": "text-left"},
                    {"data": "trans_number", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "route_name", "orderable": true, "className": "text-left"},
                    {"data": "extends_time", "orderable": true, "className": "text-right"},
                    {"data": "old_due_date", "orderable": true, "className": "text-left"},
                    {"data": "new_due_date", "orderable": true, "className": "text-left"},
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

    searchData(data)
    {
        var l = Ladda.create( document.getElementById('cari'))

        $.ajax({
            type:"post",
            data:data,
            dataType:"json",
            url : "<?php echo site_url()?>transaction2/dueDateExtends/searchData",
            beforeSend: function(){
                // unBlockUiId('box')
                l.start();
                $( "#search" ).prop( "disabled", true );
            },
            success:(x)=>{

                var html="";
                
                if(x.code==1)
                {

                    html +=this.formAdd(x.data);
                    console.log(html);

                    $("#myForm").html(html);    
                }
                else
                {
                    toastr.error(x.message, 'Gagal');

                    $("#myForm").html(html);
                }
                
            },
            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                l.stop()
                $( "#search" ).prop( "disabled", false );
            },

            complete: function(){
                // $('#box').unblock(); 
                l.stop()
                $( "#search" ).prop( "disabled", false );
            }            
        });
    }

    formAdd(x){

        var html =`
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Nomer Invoice <span class="wajib">*</span></label>
                            <input type="text" name="transNumber" class="form-control"  placeholder="Nomer Invoice" required value="${x.transNumber}" readonly>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nomer Booking<span class="wajib">*</span></label>
                            <input type="text" name="bookingNumber" class="form-control"  placeholder="Nomer Booking" required value="${x.bookingCode}" readonly>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama booking<span class="wajib">*</span></label>
                            <input type="text" name="customer" class="form-control"  placeholder="Nama Booking" required value="${x.customerName}" readonly>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-4 form-group">
                            <label>Servis<span class="wajib">*</span></label>
                            <input type="text" name="customer" class="form-control"  placeholder="Nama Booking" required value="${x.serviceName}" readonly>
                        </div>           

                        <div class="col-sm-4 form-group">
                            <label>Kelas Layanan<span class="wajib">*</span></label>
                            <input type="text" name="shipClass" class="form-control"  placeholder="Kelas Layanan" required id="shipClass" value="${x.shipClassName}" readonly>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Due Date<span class="wajib">*</span></label>
                            <input type="text" name="dueDate" class="form-control"  placeholder="Due Date" required id="dueDate" value="${x.dueDate}" readonly>
                        </div>                        
                        
                         <div class="col-sm-12 form-group"></div>
                        
                        <div class="col-sm-4 form-group">
                            <label>Rute <span class="wajib">*</span></label>
                            <input type="text" name="route" class="form-control"  placeholder="Rute" required id="route" value="${x.routeName}" readonly>
                        </div>


                        <div class="col-sm-4 form-group">
                            <label>Perpanjangan Due Date /Jam<span class="wajib">*</span></label>
                            <input type="text" name="extends" class="form-control"  placeholder="Perpanjangan Due Date /Jam" required id="extends" onkeypress="return isNumberKey(event)">
                        </div>                                                                                                                                     

                        <div class="col-sm-12"> <?php echo createBtnForm('Simpan'); ?></div>

                        
                    </div>
        `

        return html;
    }

}


</script>