<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="dataTables">
    	<thead>
    		<tr>
    			<th>No</th>
    			<th>Transaction Date</th>
    			<th>Settlement Date</th>
                <th>TID</th>
                <th>MID</th>
    			<th>Bank Name</th>
    			<th>Status</th>
                <!-- <th class="none">Return Filename</th> -->
                <th>Nominal</th>
                <th class="none">Transcode</th>
                <th class="none">Filename</th>
    		</tr>
    	</thead>
    	<tbody>
    	</tbody>
        <!-- <tfoot>
            <tr>
                <th colspan="7" style="text-align:right">Total:</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot> -->
    </table>
</div>

<h4 class="text-right"><b id="total_amount">Total 0</b></h4>

<script type="text/javascript">
	var TableDatatablesResponsive = function () {
        var initTable = function () {
            
            var table = $('#dataTables');
            var oTable = table.dataTable({
                "ajax": {
                    "url": "<?php echo $urlDatatables ?>",
                    "type": "POST",
                    "data": function (d) {
                        d.start_date = $('#start_date_input').val();
                        d.end_date = $('#end_date_input').val();
                        d.date_type = $('#date_type').val();
                        d.status = $('#status').val();
                        // d.card = $('#card').val();
                        // d.corridor = $('#corridor').val();
                        d.date_type_name = $("#date_type option:selected").text();
                        // d.card_name = $("#card option:selected").text();
                        // d.corridor_name = $("#corridor option:selected").text();
                        d.st = $('#st').val();
                        d.st_name = $("#st option:selected").text();
                    },
                },
                "serverSide": true,
                "processing": true,
                "searching": false,
                "order": [[1, 'DESC']],
                "columns": [
                    {"data": "no", "orderable": false, "className": "text-center", "width": 20},
                    {"data": "transaction_date", "orderable": true, "className": "nowrap"},
                    {"data": "settlement_date", "orderable": true, "className": "nowrap"},
                    {"data": "terminal_id", "orderable": false},
                    {"data": "merchant_id", "orderable": false},
                    {"data": "bank_name", "orderable": false},
                    {"data": "status_name", "orderable": false, "className": "nowrap"},
                    
                    // {"data": "return_file_name", "orderable": false},
                    {"data": "amount", "orderable": false, "className": "text-right"},
                    {"data": "transaction_code", "orderable": false},
                    {"data": "filename", "orderable": false},
                ],

                fnDrawCallback: function(data) {  
                    params = data.oAjaxData;

                    if(data.json.recordsTotal){
                        $('.download').css('display','block');
                    }else{
                        $('.download').css('display','none');
                    }

                    $('#total_amount').html('Total '+data.json.total);
                },

                // "footerCallback": function ( row, data, start, end, display ) {
                //     var api = this.api(), data;
         
                //     // Remove the formatting to get integer data for summation
                //     var intVal = function ( i ) {
                //         return typeof i === 'string' ?
                //             i.replace(/[\$.]/g, '')*1 :
                //             typeof i === 'number' ?
                //                 i : 0;
                //     };
         
                //     // Total over all pages
                //     total = api
                //         .column( 7 )
                //         .data()
                //         .reduce( function (a, b) {
                //             return intVal(a) + intVal(b);
                //         }, 0 );
         
                //     // Total over this page
                //     pageTotal = api
                //         .column( 7, { page: 'current'} )
                //         .data()
                //         .reduce( function (a, b) {
                //             return intVal(a) + intVal(b);
                //         }, 0 );
         
                //     // Update footer
                //     $( api.column( 7 ).footer() ).html(
                //         'Rp'+pageTotal +' ( Rp'+ total +')'
                //     );
                // }
            });
        }

        return {
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable();
            }
        };
    }();
</script>
