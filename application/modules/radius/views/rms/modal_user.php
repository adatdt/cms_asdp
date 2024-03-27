<style type="text/css">
    #modalUserWeb {
        left: 35% !important; 
        margin-left: -15% !important; 
        width: 60% !important;
    }

</style>
<!-- Modal -->
<div class="modal fade  " id="modalUserWeb" role="dialog" aria-labelledby="modalUserWebTitle" aria-hidden="true">
  <!-- <div class="modal-dialog modal-dialog-centered" role="document"> -->

    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Tambah User WEB dan Mobile dikecualikan</h5>
        </div>
        <div class="modal-body">
           <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 form-group">     
                        <input type="text" class="form-control in-group" data-role="tagsinput"  name="getEmailAdd" id="getEmailAdd" placeholder="Masukan email">                        
                                   
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>&nbsp;
            <button type="button " id="searchEmailBtn" class="btn btn-primary btn-md  " >Simpan</button>                
        </div>
    </div>
        
    <!-- </div> -->
</div>    
<script>
    let dataEmail =  new Set(); 
//    console.log(dataEmail)
    $(document).ready(function(){
            
        const tableWebUser = $('#tableWebUser').DataTable({
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
            'columnDefs': [
                    {
                        "targets": 2, // your case first column
                        "className": "text-center",
                        "width": "2%"
                }
            ],
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "searching":true,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div #tableWebUser_filter input');
                var data_tables = $('#tableWebUser').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // seting numbering
        tableWebUser.on( 'draw.dt', function () {
            var PageInfo = $('#tableWebUser').DataTable().page.info();
                tableWebUser.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                } );
            } );     
        $(`#searchEmailBtn`).on("click", function(){
            // let  tagEmail = $("#getEmailAdd").tagsinput('items');
            let  tagEmail = $("#getEmailAdd").tagsinput('items');
            // console.log(tagEmail)
                $.ajax({
                    url : `<?= site_url("radius/rms/getUser") ?>`,
                    dataType :`json`,
                    type:`post`,
                    data: `tagEmail=${tagEmail}`,
                    beforeSend: function() {
                        unBlockUiId('modalUserWeb')
                    },
                    success:function(x)
                    {
                        if(x.code == 1)
                        {
                            toastr.success(x.message, 'Sukses');
                            getEmailAdd = x.data['email'];

                            getEmailAdd.forEach(element => {
                                dataEmail.add(element);
                            });
                            
                            $('#modalUserWeb').modal('toggle');

                            let email = myData.getDataExeption(dataEmail);
                            $("#getEmailAdd").tagsinput('removeAll');
                            tableWebUser.clear();
                            tableWebUser.rows.add(email).draw();

                        }
                        else
                        {
                            // console.log(x.data['massage'])                        
                            toastr.error(x.data['massage'], 'Gagal');
                            // console.log(dataEmail);
                        }
                        // console.log(dataEmail); 
                    },
                    error: function() {
                        toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                    },

                    complete: function() {
                        $('#modalUserWeb').unblock();
                    }                
                })

        });
    })

    function deleteUserWeb(data){
        $(document).ready(function(){
            dataEmail.delete(data);
            let email = myData.getDataExeption(dataEmail);
            const tableWebUser2 = $('#tableWebUser').DataTable();
            tableWebUser2.clear();
            tableWebUser2.rows.add(email).draw();
        })
    } 

</script>