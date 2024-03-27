<div class="table-responsive">
	<table class="table table-bordered table-advance table-hover" width="100%" id="tbl_detail_bank">
	 	<thead>
	 		<tr>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Date</th>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Bank</th>
	 			<?php foreach ($list_status as $k => $r) { ?>
	 				<th class="text-center" colspan="2" data-status="<?php echo $r->status_code ?>"><?php echo $r->status_name ?></th>
	 			<?php } ?>
	 			<th class="text-center" colspan="2" data-status="total">Total</th>
	 		</tr>
	 		<tr>
	 			<?php for ($i=0; $i < (count($list_status) + 1) * 2; $i++) { 
	 				if($i % 2 == 0){ ?>
	 					<th class="text-center">TRX</th>
	 			<?php }else{ ?>
	 					<th class="text-center">(Rp)</th>
	 			<?php } } ?>
	 		</tr>
	 	</thead>
	 	<tbody id="body_tbl_detail_bank">
	 	</tbody>
	 	<tfoot id="foot_tbl_detail_bank">
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	function detailBankSettlement(json){
		detail_bank = json.data.detail_bank.data;
		total = json.data.detail_bank.total;

        colSpanBank = $($('#tbl_detail_bank thead tr')[1])[0].children.length + 1;

		if(detail_bank.length){
			detbankHtml = '';
	        for(z in detail_bank){
	            detbankHtml += '<tr>\
	            <td class="text-center" style="vertical-align: middle;">'+detail_bank[z].dates+'</td>\
	            <td class="text-center">'+detail_bank[z].bank_name+'</td>';

	            st = detail_bank[z].status;
	            
	            a = 2;
	            for(b in st){
	                s = $($('#tbl_detail_bank thead tr th')[a]).data().status;
	                detbankHtml += '<td class="text-right">'+number_format(st[s].trx)+'</td>';
	                detbankHtml += '<td class="text-right">'+number_format(st[s].nominal)+'</td>';
	                a++;
	            }

	            detbankHtml += '<td class="text-right">'+number_format(detail_bank[z].total_trx)+'</td>';
	            detbankHtml += '<td class="text-right">'+number_format(detail_bank[z].total_nom)+'</td>';
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
	        th = $($('#tbl_detail_bank thead tr th')[noTh]).data().status;

       		detFootBankHtml += '<th class="text-right">'+number_format(total.trx[th])+'</th>';
       		detFootBankHtml += '<th class="text-right">'+number_format(total.nominal[th])+'</th>';

       		noTh++;
       	}

       	detFootBankHtml += '</tr>';

	    $('#body_tbl_detail_bank').html(detbankHtml);
	    $('#foot_tbl_detail_bank').html(detFootBankHtml);

	    var span = 1;
	    var prevTD = "";
	    var prevTDVal = "";
	    $("#body_tbl_detail_bank tr td:first-child").each(function() {
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
