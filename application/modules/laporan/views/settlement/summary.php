<div class="table-responsive">
    <table class="table table-bordered table-advance table-hover" width="100%" id="summary_settlement">
        <thead>
            <tr>
                <th colspan="2">Status</th>
                <th class="text-center">Total Transaction</th>
                <th class="text-center">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody id="body_summary_settlement">           
        </tbody>
        <tfoot id="foot_tbl_summary">
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    function listSummary(json){
        d = json.data.summary.data;
        dTotal = json.data.summary.total;
        var trxSudahKirim = 0,
            totSudahKirim = 0;
        if(d.length){
            sumBody = '';
            for(x in d){
                if (d[x].status_code < 0) {
                    sumBody += '<tr>';
                    sumBody += '<td colspan="2" class="bold">'+d[x].status_name+'</td>';
                    sumBody += '<td class="text-right bold">'+number_format(d[x].count)+'</td>';
                    sumBody += '<td class="text-right bold">'+number_format(d[x].sum)+'</td>';
                    sumBody += '</tr>';
                }
            }
            for(x in d){
                if (d[x].status_code >= 0) {
                    trxSudahKirim += parseInt(d[x].count);
                    totSudahKirim += parseInt(d[x].sum);
                }
            }
            sumBody += '<tr class="parent">';
            sumBody += '<td colspan="2" class="bold">Sudah dikirim</td>';
            sumBody += '<td class="text-right bold">'+number_format(trxSudahKirim)+'</td>';
            sumBody += '<td class="text-right bold">'+number_format(totSudahKirim)+'</td>';
            sumBody += '</tr>';
            for(x in d){
                if (d[x].status_code >= 0) {
                    sumBody += '<tr class="child">';
                    sumBody += '<td></td>';
                    sumBody += '<td>'+d[x].status_name+'</td>';
                    sumBody += '<td class="text-right">'+number_format(d[x].count)+'</td>';
                    sumBody += '<td class="text-right">'+number_format(d[x].sum)+'</td>';
                    sumBody += '</tr>';
                    trxSudahKirim += parseInt(d[x].count);
                    totSudahKirim += parseInt(d[x].sum);
                }
            }
        }else{
            colSpan = corrCode.length + 1;
            sumBody = '<tr>\
            <td colspan="'+colSpan+'" align="center">Data not found</td>\
            </tr>'
        }

        sumFoot = '<tr style="background-color: #f1f4f7;">';
        sumFoot += '<th colspan="2" class="text-center">Total</th>';
        sumFoot += '<th class="text-right">'+number_format(dTotal.total_volume)+'</th>';
        sumFoot += '<th class="text-right">'+number_format(dTotal.total_revenue)+'</th>';
        sumFoot += '</tr>';

        $('#body_summary_settlement').html(sumBody);
        $('#foot_tbl_summary').html(sumFoot);

        $('tr.parent')  
            .css("cursor", "pointer")  
            .click(function () {  
                $(this).siblings('.child').toggle();  
            })
    }


</script>