<script type="text/javascript">

    function getDataTicket()
    {

        $.ajax({
        type:"post",
        url:"<?php echo site_url() ?>transaction/refund_force_majeure/get_data_ticket",
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
                    var append="<div class='col-sm-4 form-group' id='plat'><label> No Plat</label><input type='text' value='"+x[0].plat_no+"' name='plat_no' class='form-control' placeholder='Tipe Penumpang' readonly ></div>";

                    // $("#append").append(append);
                    $(append).insertAfter("#append");
                    $("#label").html("Golongan");
                    $("[name='approval_code']").prop("readonly", false);
                }
                else
                {
                    $("#plat").remove();
                    $("#label").html("Tipe Penumpang");
                    $("[name='approval_code']").prop("readonly", false);
                }

                var ticket_number=$("[name='ticket_number']").val();

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
        $("[name='approval_code']").val("");
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
            $("[name='approval_code']").val("");
            $("#status").html("");
        });


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