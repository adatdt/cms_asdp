<script type="text/javascript">

    var countAppend=0;
    function getDataTicket()
    {
        // $.ajax({
        //     type:"post",
        //     url:"<?php echo site_url() ?>transaction/force_majeure/get_data_ticket",
        //     data:"search="+$("#search").val(),
        //     dataType:"json",
        //     beforeSend:function(){
        //         unBlockUiId('box')
        //     },
        //     success:function(x)
        //     {
        //         // console.log(x);

        //         if(x[1].code == 1){

        //             $("[name='ticket_number']").val(x[0].ticket_number);
        //             $("[name='booking_code']").val(x[0].booking_code);
        //             $("[name='name']").val(x[0].name);
        //             $("[name='gender']").val(x[0].gender);
        //             $("[name='service']").val(x[0].service);
        //             $("[name='passanger_type']").val(x[0].type);
        //             $("#status").html("Status : <span class='label label-success'>"+x[0].status+"</span>");


        //             if(x[0].plat_no!="")
        //             {
        //                 var appen="<label> No Plat</label><input type='text' value='"+x[0].plat_no+"' name='plat_no' class='form-control' placeholder='Tipe Penumpang' readonly >";

        //                 $("#plat").html(appen);
        //                 $("#label").html("Golongan");
        //             }
        //             else
        //             {
        //                 $("#plat").html("");
        //                 $("#label").html("Tipe Penumpang");
        //             }
        //         }
        //         else
        //         {
        //             toastr.error(x[1].message, 'Gagal');
        //             clearData()
        //             // console.log(x[1].message);

        //         }
        //     },
        //     complete: function(){
        //         $('#box').unblock(); 
        //     }
        // });
        var t=$("#data_temp").DataTable();
        $.ajax({
        type:"post",
        url:"<?php echo site_url() ?>transaction/force_majeure/get_data_ticket",
        data:"search="+$("#search").val(),
        dataType:"json",
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x)
        {
            // console.log(x);

            if(x[1].code == 1){

                $("[name='ticket_number']").val(x[0].ticket_number);
                $("[name='booking_code']").val(x[0].booking_code);
                $("[name='name']").val(x[0].name);
                $("[name='gender']").val(x[0].gender);
                $("[name='service']").val(x[0].service);
                $("[name='passanger_type']").val(x[0].type);
                $("#status").html("Status : <span class='label label-success'>"+x[0].status+"</span>");


                if(x[0].plat_no!="")
                {
                    var appen="<label> No Plat</label><input type='text' value='"+x[0].plat_no+"' name='plat_no' class='form-control' placeholder='Tipe Penumpang' readonly >";

                    $("#plat").html(appen);
                    $("#label").html("Golongan");
                }
                else
                {
                    $("#plat").html("");
                    $("#label").html("Tipe Penumpang");
                }

                var ticket_number=$("[name='ticket_number']").val();

                countAppend++;
                var no=countAppend-1

                // console.log(countAppend);

                // mendapatkan value data 
                var array=document.getElementsByClassName("y");

                // cheking apakah nomer tiket sudah ditampung
                var checkCount=0;
                for(var i=0; i<array.length; i++)
                {
                    if(array[i].value==x[0].ticket_number)
                    {
                        checkCount++;
                    }
                }

                if(checkCount>0)
                {
                    toastr.error("Data sudah ditampung", 'Gagal Tampung');
                }
                else
                {
                    // menambahkan row dan data apada
                    t.row.add( [
                        "",
                        countAppend,
                        x[0].ticket_number+"<input type='hidden' class='y' name='td_ticket["+no+"]' value='"+x[0].ticket_number+"'><input type='hidden' name='td_booking_code["+no+"]' value='"+x[0].booking_code+"'>",
                        x[0].booking_code,
                        x[0].name,
                        x[0].service
                    ] ).draw( false );

                    // menambahkan nomer pada datatables
                    t.on( 'order.dt search.dt', function () {
                        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                            cell.innerHTML = i+1;
                        } );
                    } ).draw();
         
                }


            }
            else
            {
                toastr.error(x[1].message, 'Gagal');
                clearData()
                // console.log(x[1].message);

            }
        },
        complete: function(){
            $('#box').unblock(); 
        }
    });
    }

    function clearData()
    {
        $("[name='ticket_number']").val("");
        $("[name='booking_code']").val("");
        $("[name='name']").val("");
        $("[name='gender']").val("");
        $("[name='service']").val("");
        $("[name='passanger_type']").val("");
        $("[name='plat_no']").val("");
        $("#status").html("");

    }


    $(document).ready(function(){

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        // $("#cari").on("click",function(){
        //     getDataTicket();
        // });

        $("[name='clear']").on('click', function(){
            $("[name='ticket_number']").val("");
            $("[name='booking_code']").val("");
            $("[name='name']").val("");
            $("[name='gender']").val("");
            $("[name='service']").val("");
            $("[name='passanger_type']").val("");
            $("[name='plat_no']").val("");
            $("[name='search']").val("");
            $("#status").html("");
        });

        var t=$("#data_temp").DataTable({
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
            "order": [[ 1, "desc" ]],
            "columnDefs": [ {
                // "searchable": false,
                "orderable": false,
                "targets": [0,2,3,4,5]
                },
                {
                "targets": [ 1 ],
                    "visible": false
                }

            ],
        });

        // $("[name='tampung']").on("click",function(){
        //     var ticket_number=$("[name='ticket_number']").val();

        //     if(ticket_number=="")
        //     {
        //         toastr.error("Data Kosong", 'Gagal');
        //     }
        //     else
        //     {
        //         countAppend++;
        //         var x=countAppend-1
        //         var service=$("[name='service']").val();
        //         var name=$("[name='name']").val();
        //         var booking_code=$("[name='booking_code']").val();

        //         // menambahkan row dan data apada
        //         t.row.add( [
        //             "",
        //             countAppend,
        //             ticket_number+"<input type='hidden' class='y' name='td_ticket["+x+"]' value='"+ticket_number+"'><input type='hidden' name='td_booking_code["+x+"]' value='"+booking_code+"'>",
        //             booking_code,
        //             name,
        //             service
        //         ] ).draw( false );

        //         // menambahkan nomer pada datatables
        //         t.on( 'order.dt search.dt', function () {
        //             t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        //                 cell.innerHTML = i+1;
        //             } );
        //         } ).draw();
         

        //         // mendapatkan value data 
        //         var array=document.getElementsByClassName("y");

        //         // cheking apakah nomer tiket sudah ditampung
        //         var checkCount=0;
        //         for(var i=0; i<array.length; i++)
        //         {
        //             if(array[i].value==ticket_number)
        //             {
        //                 checkCount++;
        //             }
        //         }

        //         // if(checkCount>0)
        //         // {
        //         //     toastr.error("Data sudah ditampung", 'Gagal');
        //         // }
        //         // else
        //         // {
        //         //     $("#table_tampung").prepend(data);
        //         //     clearData()
        //         // }

        //         // $("#table_tampung").prepend(data);
                

        //     }
        // });

        $("#cari").on("click",function(){
            getDataTicket();
        });

        $("[name='search']").on('keypress',function(e) {
            if(e.which == 13) {
                getDataTicket();
            }
        });

        $('#data_temp tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                // t.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );

        $('#hapus').click( function () {
            t.rows('.selected').remove().draw( false );

        } );

    })
</script>