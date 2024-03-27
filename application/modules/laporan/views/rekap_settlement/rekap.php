<div class="table-responsive">
	<table class="table table-bordered table-advance table-hover" width="100%" id="tbl_rekap_fs">
	 	<thead>
	 		<tr>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Date</th>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Bank</th>
	 			<th class="text-center" rowspan="2" style="vertical-align: middle;">Filename</th>
	 			<?php foreach ($list_status_rf as $k => $r) { ?>
	 				<th class="text-center" colspan="2" data-status="<?php echo $r->status_code ?>"><?php echo $r->status_name ?></th>
	 			<?php } ?>
	 			<!-- <th class="text-center" colspan="2" data-status="total">Total</th> -->
	 			<th class="text-center" colspan="2" data-status="totalkirim">Sudah dikirim</th>
	 		</tr>
	 		<tr>
	 			<?php for ($i=0; $i < (count($list_status_rf) + 1 ) * 2; $i++) { 
	 				if($i % 2 == 0){ ?>
	 					<th class="text-center">TRX</th>
	 			<?php }else{ ?>
	 					<th class="text-center">(Rp)</th>
	 			<?php } } ?>
	 		</tr>
	 	</thead>
	 	<tbody id="body_tbl_rekap_fs">
	 	</tbody>
	 	<tfoot id="foot_tbl_rekap_fs">
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	function detailRekapFS(json){
		rekap_fs = json.data.rekap_fs.data;
		total = json.data.rekap_fs.total;
		totalkirim = json.data.detail.total;
		var tot_trxSudahKirim = 0,
            tot_totSudahKirim = 0;
		
		// console.log(rekap_fs);
		
        colSpanBank = $($('#tbl_rekap_fs thead tr')[1])[0].children.length + 1;
        
		if(rekap_fs.length){
			detbankHtml = '';
			
	        for(z in rekap_fs){
	            detbankHtml += '<tr>\
	            <td class="text-center" style="vertical-align: middle;">'+rekap_fs[z].dates+'</td>\
	            <td class="text-center" style="vertical-align: middle;">'+rekap_fs[z].bank_name+'</td>\
	            <td class="text-center">'+rekap_fs[z].filename+'</td>';

	            st = rekap_fs[z].status;
	            
	            a = 3;
	            var trxSudahKirim = 0,
            		totSudahKirim = 0;
	            for(b in st){
	                s = $($('#tbl_rekap_fs thead tr th')[a]).data().status;
	                // console.log(st[s]);
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

	            // detbankHtml += '<td class="text-right">'+number_format(rekap_fs[z].total_trx)+'</td>';
	            // detbankHtml += '<td class="text-right">'+number_format(rekap_fs[z].total_nom)+'</td>';

	            detbankHtml += '<td class="text-right">'+number_format(trxSudahKirim)+'</td>';
	            detbankHtml += '<td class="text-right">'+number_format(totSudahKirim)+'</td>';
	            detbankHtml += '</tr>';
	        }
	    }else{
	    	col = colSpanBank+2;
	    	detbankHtml = '<tr>\
            <td colspan="'+col+'" align="center">Data not found</td>\
            </tr>'
	    }

	    // foot
	   	detFootBankHtml = '<tr style="background-color: #f1f4f7">';
       	detFootBankHtml += '<th colspan="3" class="text-center">Total</th>';

       	noTh = 3;
       	for (var i = (colSpanBank - 2) / 2; i >= 0; i--) {
	        th = $($('#tbl_rekap_fs thead tr th')[noTh]).data().status;

       		detFootBankHtml += '<th class="text-right">'+number_format(total.trx[th])+'</th>';
       		detFootBankHtml += '<th class="text-right">'+number_format(total.nominal[th])+'</th>';

       		noTh++;
       	}

       	detFootBankHtml += '</tr>';

	    $('#body_tbl_rekap_fs').html(detbankHtml);
	    $('#foot_tbl_rekap_fs').html(detFootBankHtml);

	    var span = 1;
	    var prevTD = "";
	    var prevTDVal = "";
	    $("#body_tbl_rekap_fs tr td:first-child").each(function() {
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