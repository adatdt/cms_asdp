<div class="table-responsive">
	<table class="table table-bordered table-advance table-hover table-hidden" width="100%" id="tbl_detail_perbulan">
	 	<thead>
	 		<tr>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Bulan</th>
	 			<?php foreach ($list_status as $k => $r) { ?>
	 				<th class="text-center" colspan="2" data-status="<?php echo $r->status_code ?>"><?php echo $r->status_name ?></th>
	 			<?php } ?>
	 			<th class="text-center" colspan="2" data-status="total">Total</th>
	 			<th class="text-center" colspan="2" data-status="totalkirim">Sudah dikirim</th>
	 		</tr>
	 		<tr>
	 			<?php for ($i=0; $i < (count($list_status) + 2) * 2; $i++) { 
	 				if($i % 2 == 0){ ?>
	 					<th class="text-center">TRX</th>
	 			<?php }else{ ?>
	 					<th class="text-center">(Rp)</th>
	 			<?php } } ?>
	 		</tr>
	 	</thead>
	 	<tbody id="body_tbl_detail_perbulan">
	 	</tbody>
	 	<tfoot id="foot_tbl_detail_perbulan">
		</tfoot>
	</table>
	<table class="header-fixed"></table>
</div>

<script type="text/javascript">
	function detailSettlementPerbulan(json){
		detail_perbulan = json.data.detail_perbulan.data;
		total = json.data.detail_perbulan.total;
		totalkirim = json.data.detail_perbulan.total;
		var tot_trxSudahKirim = 0,
            tot_totSudahKirim = 0;
        colSpan = $($('#tbl_detail_perbulan thead tr')[1])[0].children.length + 1;

		if(detail_perbulan.length){
			detHtml = '';
	        for(z in detail_perbulan){
	            detHtml += '<tr>\
	            <td class="text-center">'+detail_perbulan[z].dates+'</td>';

	            var st = detail_perbulan[z].status;
	            
	            a = 1;
	            var trxSudahKirim = 0,
            		totSudahKirim = 0;
	            for(b in st){
	                s = $($('#tbl_detail_perbulan thead tr th')[a]).data().status;
	                detHtml += '<td class="text-right">'+number_format(st[s].trx)+'</td>';
	                detHtml += '<td class="text-right">'+number_format(st[s].nominal)+'</td>';
	                a++;

	                if (s >= 0) {
	                    trxSudahKirim += parseInt(st[s].trx);
	                    totSudahKirim += parseInt(st[s].nominal);
	                    tot_trxSudahKirim += parseInt(st[s].trx);
	                    tot_totSudahKirim += parseInt(st[s].nominal);
	                }
	            }
	            total.trx.totalkirim = tot_trxSudahKirim;
	            total.nominal.totalkirim = tot_totSudahKirim;

	            detHtml += '<td class="text-right">'+number_format(detail_perbulan[z].total_trx)+'</td>';
	            detHtml += '<td class="text-right">'+number_format(detail_perbulan[z].total_nom)+'</td>';

	            detHtml += '<td class="text-right">'+number_format(trxSudahKirim)+'</td>';
	            detHtml += '<td class="text-right">'+number_format(totSudahKirim)+'</td>';
	            detHtml += '</tr>';
	        }
	    }else{
	    	detHtml = '<tr>\
            <td colspan="'+colSpan+'" align="center">Data not found</td>\
            </tr>'
	    }


	    // foot
	   	detFootHtml = '<tr style="background-color: #f1f4f7">';
       	detFootHtml += '<th class="text-center">Total</th>';

       	noTh = 1;
       	for (var i = (colSpan - 2) / 2; i >= 0; i--) {
	        th = $($('#tbl_detail_perbulan thead tr th')[noTh]).data().status;

       		detFootHtml += '<th class="text-right">'+number_format(total.trx[th])+'</th>';
       		detFootHtml += '<th class="text-right">'+number_format(total.nominal[th])+'</th>';

       		noTh++;
       	}

       	detFootHtml += '</tr>';

	    $('#body_tbl_detail_perbulan').html(detHtml);
	    $('#foot_tbl_detail_perbulan').html(detFootHtml);
	}
</script>
