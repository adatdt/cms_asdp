<script type="text/javascript">
    function get_dock()
    {
        $.ajax({
            type:"post",
            url:"<?php echo site_url()?>pelabuhan/schedule/get_dock",
            data: 'port='+$('#port').val(),
            dataType :"json",
            beforeSend:function(){
                unBlockUiId('box')
            },
            success:function(x){

                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length; i++)
                {
                    html +="<option value='"+x[i].id+"'>"+x[i].name+"</option>";                   
                }

                $("#dock").html(html);
                $("#class").html("<option value=''>Pilih</option>");
                // console.log(html);
            },

            complete: function(){
                $('#box').unblock(); 
            }

        });
    }

    function getShipClass()
    {
        $.ajax({
            type:"post",
            url:"<?php echo site_url()?>pelabuhan/schedule/get_ship_class",
            data:'dock='+document.getElementById("dock").value,
            dataType:"json",
            beforeSend:function(){
                unBlockUiId('box')
            },
            success:function(x)
            {
                // console.log(x);
                html="<option value='"+x.id+"'>"+x.name+"</option>";
                $("#class").html(html);  
            },
            complete: function(){
                $('#box').unblock(); 
            }
        });
    }

    $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
    });

    $(document).ready(function(){

        $("#port").on("change",function(){
            get_dock();
        });

        $("#dock").on("change",function(){
            getShipClass();

        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });


    });

</script>