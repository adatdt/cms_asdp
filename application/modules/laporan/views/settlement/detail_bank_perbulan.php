<div class="table-responsive">
	<table class="table table-bordered table-advance table-hover" width="100%" id="tbl_detail_bank_perbulan">
	 	<thead>
	 		<tr>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Bulan</th>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Bank</th>
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
	 	<tbody id="body_tbl_detail_bank_perbulan">
	 	</tbody>
	 	<tfoot id="foot_tbl_detail_bank_perbulan">
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	function detailBankSettlementPerbulan(json){
		detail_bank_perbulan = json.data.detail_bank_perbulan.data;
		total = json.data.detail_bank_perbulan.total;
		totalkirim = json.data.detail.total;
		var tot_trxSudahKirim = 0,
            tot_totSudahKirim = 0;
		
        colSpanBank = $($('#tbl_detail_bank_perbulan thead tr')[1])[0].children.length + 1;
        
		if(detail_bank_perbulan.length){
			detbankHtml = '';
			
	        for(z in detail_bank_perbulan){
	            detbankHtml += '<tr>\
	            <td class="text-center" style="vertical-align: middle;">'+detail_bank_perbulan[z].dates+'</td>\
	            <td class="text-center">'+detail_bank_perbulan[z].bank_name+'</td>';

	            st = detail_bank_perbulan[z].status;
	            
	            a = 2;
	            var trxSudahKirim = 0,
            		totSudahKirim = 0;
	            for(b in st){
	                s = $($('#tbl_detail_bank_perbulan thead tr th')[a]).data().status;
	                detbankHtml += '<td class="text-right">'+number_format(st[s].trx)+'</td>';
	                detbankHtml += '<td class="text-right">'+number_format(st[s].nominal)+'</td>';
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

	            detbankHtml += '<td class="text-right">'+number_format(detail_bank_perbulan[z].total_trx)+'</td>';
	            detbankHtml += '<td class="text-right">'+number_format(detail_bank_perbulan[z].total_nom)+'</td>';

	            detbankHtml += '<td class="text-right">'+number_format(trxSudahKirim)+'</td>';
	            detbankHtml += '<td class="text-right">'+number_format(totSudahKirim)+'</td>';
	            detbankHtml += '</tr>';
	        }
	    }else{
	    	col = colSpanBank+1;
	    	detbankHtml = '<tr>\
            <td colspan="'+col+'" align="center">Data not found</td>\
            </tr>'
	    }


	    // foot
	   	detFootBankHtml = '<tr style="background-color: #f1f4f7">';
       	detFootBankHtml += '<th colspan="2" class="text-center">Total</th>';

       	noTh = 2;
       	for (var i = (colSpanBank - 2) / 2; i >= 0; i--) {
	        th = $($('#tbl_detail_bank_perbulan thead tr th')[noTh]).data().status;

       		detFootBankHtml += '<th class="text-right">'+number_format(total.trx[th])+'</th>';
       		detFootBankHtml += '<th class="text-right">'+number_format(total.nominal[th])+'</th>';

       		noTh++;
       	}

       	detFootBankHtml += '</tr>';

	    $('#body_tbl_detail_bank_perbulan').html(detbankHtml);
	    $('#foot_tbl_detail_bank_perbulan').html(detFootBankHtml);

	    var span = 1;
	    var prevTD = "";
	    var prevTDVal = "";
	    $("#body_tbl_detail_bank_perbulan tr td:first-child").each(function() {
	      	var $this = $(this);
	      	if ($this.text() == prevTDVal) {
	         	span++;
		        if (prevTD != "") {
		            prevTD.attr("rowspan", span);
		            $this.remove();
		        }
	      	} else {
	         	prevTD     = $this;
	         	prevTDVal  = $this.text();
	        	span       = 1;
	      	}
	    });
	}
</script>