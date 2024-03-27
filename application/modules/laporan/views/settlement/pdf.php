<!-- Setting CSS bagian header/ kop -->
<style type="text/css">
    table {
        border-collapse: collapse;
    }

    table th, table td {
        padding: 5px 5px;
    }
    
    .tabel th,
    .tabel td {        
        border: 1px solid #000;
        font-size: 12px;
    }

    .tabel-min td, .tabel-min th {
        padding: 3px 2px;
        border: 1px solid #000;
        font-size: 8px;
    }
    
    .tabel th {
        font-weight: normal;
    }
    
    .tabel-no-border {
        border-collapse: collapse;
    }
    
    .border {
        border: 1px solid #000;
    }

    .no-borders td {
        border: 0 !important;
    }
    
    .full-width {
        width: 1024px;
    }
    
    .center {
        text-align: center;
    }
    
    .right {
        text-align: right;
    }
    
    .bold {
        font-weight: bold;
    }
    
    .italic {
        font-style: italic;
    }
    
    .no-border-right {
        border-right: none;
    }
    
    .no-border-left {
        border-left: none;
    }
    
    td.border-right {
        border-right: 1px solid #000
    }
</style>
<!-- Setting Margin header/ kop -->
<page backtop="32mm" backbottom="10mm" backleft="0mm" backright="0mm">
    <page_header>
        <table class="tabel full-width" align="center">
            <tr>
                <td rowspan="4" class="no-border-right" style="width: 20%">
                    <img src="assets/img/asdp.png" style="width:100px; height: auto">
                    <!-- <img src="http://172.16.0.11/asdp-admin/assets/img/asdp.png" style="width:100px; height: auto"> -->
                </td>
                <td rowspan="4" class="center bold" style="width: 50%;font-size:14px; line-height: 1.5; vertical-align: middle">
                    <?= strtoupper($data['post']->tab_name) ?>
                </td>
                <td style="width: 15%" class="no-border-right no-border-left">No. Dokumen</td>
                <td style="width: 15%">:</td>
            </tr>
            <tr>
                <td class="no-border-right no-border-left">Revisi</td>
                <td>: </td>
            </tr>
            <tr>
                <td class="no-border-right no-border-left">Berlaku Efektif</td>
                <td>: </td>
            </tr>
            <tr>
                <td class="no-border-right no-border-left">Halaman</td>
                <td>: [[page_cu]] dari [[page_nb]]</td>
            </tr>
        </table>
    </page_header>
    
    <table class="tabel-no-border full-width" align="center">
        <tbody class="border">
            <tr class="no-borders">
                <td class="left" style="width: 20%; border-right: none; border-bottom: none">Cabang</td>
                <td class="left" style="width: 30%; border-right: none; border-bottom: none">: <?php echo $port_name ?></td>
                <td class="left" style="width: 20%; border-right: none; border-bottom: none">Tanggal Berdasarkan</td>
                <td class="left" style="width: 30%; border-bottom: none">: <?php echo $data['post']->date_type_name ?></td> 
            </tr>
            <tr class="no-borders">
                <td class="left" style="width: 20%; border-right: none;">Bank</td>
                <td class="left" style="width: 30%; border-right: none;">: <?php echo $bank_name ?></td>
                <td class="left" style="width: 20%; border-right: none;">Periode</td>
                <td class="left" style="width: 30%">: <?php echo format_date($data['post']->start_date)." s/d ".format_date($data['post']->end_date) ?></td>                       
            </tr>
        </tbody>
    </table>
    <br>
    <?php
        if($data['post']->type == 1){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th class="center bold" style="width: 5%">NO</th>
            <th class="center bold" style="width: 45%">STATUS</th>
            <th class="center bold" style="width: 25%">TRANSAKSI</th>
            <th class="center bold" style="width: 25%">NOMINAL (Rp)</th>
        </tr>
        <?php            
            $total = 0;
            $trx = 0;
            foreach ($data['data']['data'] as $x => $value) { 
                $trx += $value->count;
                $total += $value->sum;
        ?>
            <tr>
                <td class="center">
                    <?= ($x+1) ?>
                </td>
                <td class="left">
                    <?= $value->status_name ?>
                </td>
                <td class="right">
                    <?= idr_currency($value->count) ?>
                </td>
                <td class="right">
                    <?= idr_currency($value->sum) ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td class="center bold" colspan="2">Jumlah Total</td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($trx) ?>
            </td>
            <td class="right bold" style="padding: 2px 8px">
                <?= idr_currency($total) ?>
            </td>
        </tr>
       
    </table>

    <?php } else if($data['post']->type == 2){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th rowspan="2" class="center bold" style="width: 5%">No</th>
            <th rowspan="2" class="center bold" style="width: 10%">Tanggal</th>
            <?php 
            foreach ($data['status'] as $key => $value) {
                echo '<th colspan="2" class="center bold" style="width: 14%;white-space: normal;overflow-wrap: break-word;">'.$value->status_name.'</th>';
            }
            ?>
            <th colspan="2" class="center bold" style="width: 15%">Total</th>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    echo '<th class="center bold" style="width: 5%">Trx</th>';
                }else{
                    echo '<th class="center bold" style="width: 9%">(Rp)</th>';
                }
            }
            ?>
            <th class="center bold" style="width: 5%">Trx</th>
            <th class="center bold" style="width: 10%">(Rp)</th>
        </tr>

        <?php             
            foreach ($data['data']['data'] as $x => $value) {                
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <td class="center"><?= $value->dates ?></td>
                <?php 
                for ($i=0; $i < count($data['status']); $i++){
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['trx']).'</td>';
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['nominal']).'</td>';
                }
                echo '<td class="right">'.idr_currency($value->total_trx).'</td>';
                echo '<td class="right">'.idr_currency($value->total_nom).'</td>';
                ?>
            </tr>
        <?php } ?>
       <tr>
            <td colspan="2" class="center bold">Total</td>
           <?php 
           foreach ($data['status'] as $s => $st) {
                echo '<td class="right bold">'.idr_currency($data['data']['total']['trx'][$data['status'][$s]->status_code]).'</td>';
                echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal'][$data['status'][$s]->status_code]).'</td>';
            }
            echo '<td class="right bold">'.idr_currency($data['data']['total']['trx']['total']).'</td>';
            echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal']['total']).'</td>';
            ?>
       </tr>
       
    </table>

    <?php } else if($data['post']->type == 3){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th rowspan="2" class="center bold" style="width: 5%">No</th>
            <th rowspan="2" class="center bold" style="width: 10%">Tanggal</th>
            <th rowspan="2" class="center bold" style="width: 10%">Bank</th>
            <?php 
            foreach ($data['status'] as $key => $value) {
                echo '<th colspan="2" class="center bold" style="width: 12%;white-space: normal;overflow-wrap: break-word;">'.$value->status_name.'</th>';
            }
            ?>
            <th colspan="2" class="center bold" style="width: 15%">Total</th>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    echo '<th class="center bold" style="width: 4%">Trx</th>';
                }else{
                    echo '<th class="center bold" style="width: 8%">(Rp)</th>';
                }
            }
            ?>
            <th class="center bold" style="width: 5%">Trx</th>
            <th class="center bold" style="width: 10%">(Rp)</th>
        </tr>

        <?php             
            foreach ($data['data']['data'] as $x => $value) {                
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <td class="center"><?= $value->dates ?></td>
                <td class="left"><?= $value->bank_name ?></td>
                <?php 
                for ($i=0; $i < count($data['status']); $i++){
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['trx']).'</td>';
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['nominal']).'</td>';
                }
                echo '<td class="right">'.idr_currency($value->total_trx).'</td>';
                echo '<td class="right">'.idr_currency($value->total_nom).'</td>';
                ?>
            </tr>
        <?php } ?>
       <tr>
            <td colspan="3" class="center bold">Total</td>
           <?php 
           foreach ($data['status'] as $s => $st) {
                echo '<td class="right bold">'.idr_currency($data['data']['total']['trx'][$data['status'][$s]->status_code]).'</td>';
                echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal'][$data['status'][$s]->status_code]).'</td>';
            }
            echo '<td class="right bold">'.idr_currency($data['data']['total']['trx']['total']).'</td>';
            echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal']['total']).'</td>';
            ?>
       </tr>
       
    </table>

    <?php } else if($data['post']->type == 4){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th class="center bold" style="width: 5%">No</th>
            <th class="center bold" style="width: 12%">Tgl Transaksi</th>
            <th class="center bold" style="width: 12%">Tgl Settle</th>
            <th class="center bold" style="width: 10%">TID</th>
            <th class="center bold" style="width: 15%">MID</th>
            <th class="center bold" style="width: 15%">Bank</th>
            <th class="center bold" style="width: 15%">Status</th>
            <th class="center bold" style="width: 16%">Nominal (Rp)</th>
            <!-- <th class="center bold" style="width: 15;word-wrap: break-word">Transcode</th>
            <th class="center bold" style="">Filename</th> -->
        </tr>
       

        <?php      
            $total = 0;       
            foreach ($data['data'] as $x => $value) {   
            $total += $value->amount;         
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <td class="left"><?= $value->transaction_date ?></td>
                <td class="left"><?= $value->settlement_date ?></td>
                <td class="left"><?=$value->terminal_id ?></td>
                <td class="left"><?=$value->merchant_id ?></td>
                <td class="left"><?=$value->bank_name ?></td>
                <td class="left"><?=$value->status_name ?></td>
                <td class="right"><?=idr_currency($value->amount) ?></td>                
            </tr>
            <tr>
                <td></td>
                <td>Transcode <span>:</span></td>
                <td colspan="6" style="width:78%;white-space: normal;overflow-wrap: break-word;">
                    <?php 
                    if (strlen($value->transaction_code) > 120) {
                        echo substr($value->transaction_code,0,120)." ".substr($value->transaction_code,120);
                    } else {
                        echo $value->transaction_code;
                    }
                    
                    ?>
                        
                    </td>
            </tr>
            <tr>
                <td></td>
                <td>Filename <span>:</span></td>
                <td colspan="6"><?=$value->filename ?></td>
            </tr>
        <?php } ?>
       <tr>          
            <td colspan="7" class="center bold">Total </td>
            <td class="right bold"><?= idr_currency($total) ?></td>
        </tr>
   
    </table>

     <?php } else if($data['post']->type == 5){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th rowspan="2" class="center bold" style="width: 5%">No</th>
            <th rowspan="2" class="center bold" style="width: 10%">Bulan</th>
            <?php 
            foreach ($data['status'] as $key => $value) {
                echo '<th colspan="2" class="center bold" style="width: 14%;white-space: normal;overflow-wrap: break-word;">'.$value->status_name.'</th>';
            }
            ?>
            <th colspan="2" class="center bold" style="width: 15%">Total</th>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    echo '<th class="center bold" style="width: 5%">Trx</th>';
                }else{
                    echo '<th class="center bold" style="width: 9%">(Rp)</th>';
                }
            }
            ?>
            <th class="center bold" style="width: 5%">Trx</th>
            <th class="center bold" style="width: 10%">(Rp)</th>
        </tr>

        <?php             
            foreach ($data['data']['data'] as $x => $value) {                
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <td class="center"><?= $value->dates ?></td>
                <?php 
                for ($i=0; $i < count($data['status']); $i++){
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['trx']).'</td>';
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['nominal']).'</td>';
                }
                echo '<td class="right">'.idr_currency($value->total_trx).'</td>';
                echo '<td class="right">'.idr_currency($value->total_nom).'</td>';
                ?>
            </tr>
        <?php } ?>
       <tr>
            <td colspan="2" class="center bold">Total</td>
           <?php 
           foreach ($data['status'] as $s => $st) {
                echo '<td class="right bold">'.idr_currency($data['data']['total']['trx'][$data['status'][$s]->status_code]).'</td>';
                echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal'][$data['status'][$s]->status_code]).'</td>';
            }
            echo '<td class="right bold">'.idr_currency($data['data']['total']['trx']['total']).'</td>';
            echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal']['total']).'</td>';
            ?>
       </tr>
       
    </table>

<?php } else if($data['post']->type == 6){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th rowspan="2" class="center bold" style="width: 5%">No</th>
            <th rowspan="2" class="center bold" style="width: 10%">Bulan</th>
            <th rowspan="2" class="center bold" style="width: 10%">Bank</th>
            <?php 
            foreach ($data['status'] as $key => $value) {
                echo '<th colspan="2" class="center bold" style="width: 12%;white-space: normal;overflow-wrap: break-word;">'.$value->status_name.'</th>';
            }
            ?>
            <th colspan="2" class="center bold" style="width: 15%">Total</th>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    echo '<th class="center bold" style="width: 4%">Trx</th>';
                }else{
                    echo '<th class="center bold" style="width: 8%">(Rp)</th>';
                }
            }
            ?>
            <th class="center bold" style="width: 5%">Trx</th>
            <th class="center bold" style="width: 10%">(Rp)</th>
        </tr>

        <?php             
            foreach ($data['data']['data'] as $x => $value) {                
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <td class="center"><?= $value->dates ?></td>
                <td class="left"><?= $value->bank_name ?></td>
                <?php 
                for ($i=0; $i < count($data['status']); $i++){
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['trx']).'</td>';
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['nominal']).'</td>';
                }
                echo '<td class="right">'.idr_currency($value->total_trx).'</td>';
                echo '<td class="right">'.idr_currency($value->total_nom).'</td>';
                ?>
            </tr>
        <?php } ?>
       <tr>
            <td colspan="3" class="center bold">Total</td>
           <?php 
           foreach ($data['status'] as $s => $st) {
                echo '<td class="right bold">'.idr_currency($data['data']['total']['trx'][$data['status'][$s]->status_code]).'</td>';
                echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal'][$data['status'][$s]->status_code]).'</td>';
            }
            echo '<td class="right bold">'.idr_currency($data['data']['total']['trx']['total']).'</td>';
            echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal']['total']).'</td>';
            ?>
       </tr>
       
    </table>

<?php } else if($data['post']->type == 7){ 
    ?>
    <table class="tabel full-width" align="center">
        <tr>
            <th rowspan="2" class="center bold" style="width: 5%">No</th>
            <th rowspan="2" class="center bold" style="width: 6%">Tanggal</th>
            <th rowspan="2" class="center bold" style="width: 12%">Filename</th>
            <th rowspan="2" class="center bold" style="width: 5%">Bank</th>
            <?php 
            foreach ($data['status'] as $key => $value) {
                echo '<th colspan="2" class="center bold" style="width: 12%;white-space: normal;overflow-wrap: break-word;">'.$value->status_name.'</th>';
            }
            ?>
            <th colspan="2" class="center bold">Total</th>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    echo '<th class="center bold" style="width: 4%">Trx</th>';
                }else{
                    echo '<th class="center bold" style="width: 7%">(Rp)</th>';
                }
            }
            ?>
            <th class="center bold" style="width: 4%">Trx</th>
            <th class="center bold" style="width: 7%">(Rp)</th>
        </tr>

        <?php             
            foreach ($data['data']['data'] as $x => $value) {                
        ?>
            <tr>
                <td class="center"><?= ($x+1) ?></td>
                <!-- <td class="center"><?= $value->dates ?></td> -->
                <td class="center"><?= wordwrap( $value->dates, 5, '<br />', true) ?></td>
                <!-- <td class="center"><?= $value->filename ?></td> -->
                <td class="center"><?= wordwrap( $value->filename, 10, '<br />', true) ?></td>
                <td class="center"><?= $value->bank_name ?></td>
                <?php 
                for ($i=0; $i < count($data['status']); $i++){
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['trx']).'</td>';
                    echo '<td class="right">'.idr_currency($value->status[$data['status'][$i]->status_code]['nominal']).'</td>';
                }
                echo '<td class="right">'.idr_currency($value->total_trx).'</td>';
                echo '<td class="right">'.idr_currency($value->total_nom).'</td>';
                ?>
            </tr>
        <?php } ?>
       <tr>
            <td colspan="4" class="center bold">Total</td>
           <?php 
           foreach ($data['status'] as $s => $st) {
                echo '<td class="right bold">'.idr_currency($data['data']['total']['trx'][$data['status'][$s]->status_code]).'</td>';
                echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal'][$data['status'][$s]->status_code]).'</td>';
            }
            echo '<td class="right bold">'.idr_currency($data['data']['total']['trx']['total']).'</td>';
            echo '<td class="right bold">'.idr_currency($data['data']['total']['nominal']['total']).'</td>';
            ?>
       </tr>
       
    </table>

    <?php } ?>

    

</page>
<!-- Memanggil fungsi bawaan HTML2PDF -->
<?php
$content = ob_get_clean();
$filename = 'settlement_';
try
{
  // setting paper
    $html2pdf = new HTML2PDF('L', 'A4', 'en', false, 'UTF-8', array(12, 10, 12, 8));
    // $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->setTestTdInOnePage(false);
    $html2pdf->writeHTML($content);
    $html2pdf->Output($filename.'.pdf');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}
?>
