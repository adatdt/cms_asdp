<div class="table-responsive">
    <table class="table table-bordered table-advance table-hover" width="100%" id="summary_settlement">
        <thead>
            <tr>
                <th>Status</th>
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

        if(d.length){
            sumBody = '';
            for(x in d){
                sumBody += '<tr>';
                sumBody += '<td>'+d[x].status_name+'</td>';
                sumBody += '<td class="text-right">'+number_format(d[x].count)+'</td>';
                sumBody += '<td class="text-right">'+number_format(d[x].sum)+'</td>';
                sumBody += '</tr>';
            }
        }else{
            colSpan = corrCode.length + 1;
            sumBody = '<tr>\
            <td colspan="'+colSpan+'" align="center">Data not found</td>\
            </tr>'
        }

        sumFoot = '<tr style="background-color: #f1f4f7;">';
        sumFoot += '<th class="text-center">Total</th>';
        sumFoot += '<th class="text-right">'+number_format(dTotal.total_volume)+'</th>';
        sumFoot += '<th class="text-right">'+number_format(dTotal.total_revenue)+'</th>';
        sumFoot += '</tr>';

        $('#body_summary_settlement').html(sumBody);
        $('#foot_tbl_summary').html(sumFoot);
    }
</script>