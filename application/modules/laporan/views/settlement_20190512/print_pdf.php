<?php

class PDF extends FPDF {
    function Content($data = null) {
        $font = 'Arial';
        $setTitleFile = 'Settlement ('.$data['post']->tab_name.')'.'_Card['.strtolower($data['post']->card_name).']_Corridor['.strtolower($data['post']->corridor_name).'] '.$data['post']->date_type_name.'_'.$data['post']->start_date.'-'.$data['post']->end_date;

        $this->SetTitle($setTitleFile);
        $this->SetAuthor($data['author']);
        $this->SetCreator('TJ');
        $this->SetSubject($data['post']->tab_name);
        $this->SetKeywords('');

        //pdf summary
        if($data['post']->type == 1){
            $this->AddPage();
            $this->setFont($font, 'b', 15);
            $this->cell(30, 6, 'Settlement ('.$data['post']->tab_name.')', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, 'Card['.strtolower($data['post']->card_name).'] Corridor['.strtolower($data['post']->corridor_name).']', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, ''.$data['post']->date_type_name.' '.$data['post']->start_date.'-'.$data['post']->end_date.'', 0, 0, 'L', 0);
            $this->Ln(10);
            $this->SetFillColor(220,220,220);
            $this->SetTextColor(0, 0, 0);
            $this->setFont($font, '', 10);

            $thead = array('Status','Total Transaction','Nominal (Rp)');
            $widths = array(65,60);
            foreach ($thead as $key => $value) {
                $w = $widths[1];

                if($key == 0){
                    $w = $widths[$key];
                }

                $this->cell($w, 6, $thead[$key], 1, 0, 'C', 1);
            }

            $this->Ln(6);

            $SetWidths = array();
            $SetAligns = array();

            for ($i=0; $i < 3; $i++) { 
                $w = $widths[1];
                $align = 'R';

                if($i == 0){
                    $w = $widths[0];
                    $align = 'L';
                }

                $SetWidths[] = $w;
                $SetAligns[] = $align;
            }
            $this->SetWidths($SetWidths);
            $this->SetAligns($SetAligns);

            foreach ($data['data']['data'] as $key => $row) {
                $this->Row(array(
                    $row->status_name,
                    number_format($row->count,0,',','.'),
                    number_format($row->sum,0,',','.')
                ));
            }

            $volume = number_format($data['data']['total']['total_volume'],0,',','.');
            $revenue = number_format($data['data']['total']['total_revenue'],0,',','.');

            $thead2 = array('Total',$volume,$revenue);
            foreach ($thead2 as $key => $value) {
                $w = $widths[1];
                $aligns = 'R';

                if($key == 0){
                    $w = $widths[$key];
                    $aligns = 'C';
                }

                $this->cell($w, 6, $thead2[$key], 1, 0, $aligns, 1);
            }
        }

        //pdf detail summary
        elseif($data['post']->type == 2){
            $this->AddPage('L');
            $this->setFont($font, 'b', 15);
            $this->cell(30, 6, 'Settlement ('.$data['post']->tab_name.')', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, 'Card['.strtolower($data['post']->card_name).'] Corridor['.strtolower($data['post']->corridor_name).']', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, ''.$data['post']->date_type_name.' '.$data['post']->start_date.'-'.$data['post']->end_date.'', 0, 0, 'L', 0);
            $this->Ln(10);
            $this->SetFillColor(220,220,220);
            $this->SetTextColor(0, 0, 0);
            $this->setFont($font, '', 10);

            $widths = array(28,41);

            $this->cell($widths[0], 12, 'Date', 1, 0, 'C', 1);

            foreach ($data['status'] as $key => $value) {
                $this->cell($widths[1], 6, $value->status_name, 1, 0, 'C', 1);
            }

            $this->cell($widths[1], 6, 'Total', 1, 0, 'C', 1);
            $this->Ln(6);

            $this->Cell($widths[0]);

            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    $this->cell($widths[1]/2, 6, 'TRX', 1, 0, 'C', 1);
                }else{
                    $this->cell($widths[1]/2, 6, 'NOM', 1, 0, 'C', 1);
                }
            }
            $this->cell($widths[1]/2, 6, 'TRX', 1, 0, 'C', 1);
            $this->cell($widths[1]/2, 6, 'NOM', 1, 0, 'C', 1);
            $this->Ln(6);

            $SetWidths = array();
            $SetAligns = array();
            for ($i=0; $i < count($data['status']) * 2 + 3; $i++) { 
                $w = $widths[1]/2;
                $align = 'R';

                if($i == 0){
                    $w = $widths[0];
                    $align = 'C';
                }

                $SetWidths[] = $w;
                $SetAligns[] = $align;
            }
            $this->SetWidths($SetWidths);
            $this->SetAligns($SetAligns);

            $b = 0;
            $trx = array();
            $nom = array();

            foreach ($data['data']['data'] as $x => $row) {
                $dataRow = array($row->dates);

                for ($i=0; $i < count($data['status']); $i++){
                    array_push($dataRow, number_format($row->status[$data['status'][$i]->status_code]['trx'],0,',','.'));
                    array_push($dataRow, number_format($row->status[$data['status'][$i]->status_code]['nominal'],0,',','.'));
                }

                array_push($dataRow,number_format($row->total_trx,0,',','.'),number_format($row->total_nom,0,',','.'));

                $this->Row($dataRow);
            }

            $rowTotal = array('Total');
            foreach ($data['status'] as $s => $st) {
                array_push($rowTotal, number_format($data['data']['total']['trx'][$data['status'][$s]->status_code],0,',','.'));
                array_push($rowTotal, number_format($data['data']['total']['nominal'][$data['status'][$s]->status_code],0,',','.'));
            }

            array_push($rowTotal, number_format($data['data']['total']['trx']['total'],0,',','.'));
            array_push($rowTotal, number_format($data['data']['total']['nominal']['total'],0,',','.'));
            
            $this->setFont($font, 'b', 10);
            $this->Row($rowTotal);
        }

        //pdf detail bank
        elseif($data['post']->type == 3){
            $this->AddPage('L','Legal');
            $this->setFont($font, 'b', 15);
            $this->cell(30, 6, 'Settlement ('.$data['post']->tab_name.')', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, 'Card['.strtolower($data['post']->card_name).'] Corridor['.strtolower($data['post']->corridor_name).']', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, ''.$data['post']->date_type_name.' '.$data['post']->start_date.'-'.$data['post']->end_date.'', 0, 0, 'L', 0);
            $this->Ln(10);
            $this->SetFillColor(220,220,220);
            $this->SetTextColor(0, 0, 0);
            $this->setFont($font, '', 9);

            $widths = array(28,45);

            $this->cell($widths[0], 12, 'Date', 1, 0, 'C', 1);
            $this->cell($widths[0], 12, 'Card', 1, 0, 'C', 1);

            foreach ($data['status'] as $key => $value) {
                $this->cell($widths[1], 6, $value->status_name, 1, 0, 'C', 1);
            }

            $this->cell($widths[1], 6, 'Total', 1, 0, 'C', 1);
            $this->Ln(6);

            $this->Cell($widths[0]*2);

            for ($i=0; $i < count($data['status']) * 2; $i++) { 
                if($i % 2 == 0){
                    $this->cell($widths[1]/2, 6, 'TRX', 1, 0, 'C', 1);
                }else{
                    $this->cell($widths[1]/2, 6, 'NOM', 1, 0, 'C', 1);
                }
            }
            $this->cell($widths[1]/2, 6, 'TRX', 1, 0, 'C', 1);
            $this->cell($widths[1]/2, 6, 'NOM', 1, 0, 'C', 1);
            $this->Ln(6);

            $SetWidths = array();
            $SetAligns = array();
            for ($i=0; $i < count($data['status']) * 2 + 4; $i++) { 
                $w = $widths[1]/2;
                $align = 'R';

                if($i == 0 || $i == 1){
                    $w = $widths[0];
                    $align = 'C';
                }

                $SetWidths[] = $w;
                $SetAligns[] = $align;
            }
            $this->SetWidths($SetWidths);
            $this->SetAligns($SetAligns);

            $b = 0;
            $trx = array();
            $nom = array();
            $dataRow = array();
            
            foreach ($data['data']['data'] as $x => $row) {
                $dataRow = array($row->dates,$row->card_type_var);
                // array_push($dataRow,'');

                for ($i=0; $i < count($data['status']); $i++){
                    $dataRow[] = number_format($row->status[$data['status'][$i]->status_code]['trx'],0,',','.');
                    $dataRow[] = number_format($row->status[$data['status'][$i]->status_code]['nominal'],0,',','.');
                }

                $dataRow[] = number_format($row->total_trx,0,',','.');
                $dataRow[] = number_format($row->total_nom,0,',','.');

                $this->Row($dataRow);
            }

            $rowTotal = array('','Total');

            foreach ($data['status'] as $s => $st) {
                $rowTotal[] = number_format($data['data']['total']['trx'][$data['status'][$s]->status_code],0,',','.');
                $rowTotal[] = number_format($data['data']['total']['nominal'][$data['status'][$s]->status_code],0,',','.');
                $s++;
            }

            $rowTotal[] = number_format($data['data']['total']['trx']['total'],0,',','.');
            $rowTotal[] = number_format($data['data']['total']['nominal']['total'],0,',','.');
            
            $this->setFont($font, 'b', 9);
            $this->Row($rowTotal);
        }

        //pdf detail all
        elseif($data['post']->type == 4){
            $this->AddPage('L');
            $this->setFont($font, 'b', 15);
            $this->cell(30, 6, 'Settlement ('.$data['post']->tab_name.')', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, 'Card['.strtolower($data['post']->card_name).'] Corridor['.strtolower($data['post']->corridor_name).']', 0, 0, 'L', 0);
            $this->Ln(6);
            $this->cell(30, 6, ''.$data['post']->date_type_name.' '.$data['post']->start_date.'-'.$data['post']->end_date.'', 0, 0, 'L', 0);
            $this->Ln(10);
            $this->SetFillColor(220,220,220);
            $this->SetTextColor(0, 0, 0);
            $this->setFont($font, '', 10);

            $thead = array('No','Transaction Date','Settlement Date','TID','MID','Card','Status','Transaction Code','Filename','Return Filename','Nominal');

            $SetWidths = array();
            $SetAligns = array();

            foreach ($thead as $key => $value) {
                $w = strlen($value) + 18;                
                $SetAligns[$key] = 'L';

                if($key == 0){
                    $w = 10;
                    $SetAligns[$key] = 'C';
                }

                if($key == 1 || $key == 2){
                    $SetAligns[$key] = 'C';
                }

                if($key == 10){
                    $SetAligns[$key] = 'R';
                    $w = strlen($value) + 15;
                }

                $SetWidths[$key] = $w;

                $this->cell($w, 6, $value, 1, 0, 'C', 1);
            }

            $this->Ln(6);  

            $this->setFont($font, '', 10);
            $this->SetWidths($SetWidths);
            $this->SetAligns($SetAligns);

            $total = 0;

            foreach ($data['data'] as $x => $row) {
                $dataRow = array(
                    $x+1,
                    $row->transaction_date,
                    $row->settlement_date,
                    $row->terminal_id,
                    $row->merchant_id,
                    $row->card_type_var,
                    $row->status_name,
                    $row->transaction_code,
                    $row->filename,
                    $row->return_file_name,
                    number_format($row->amount,0,',','.')
                );

                $total += $row->amount;
                
                $this->Row($dataRow);
            }

            // $width = array_pop($SetWidths);
            // print_r($width);

            $this->SetWidths(array(array_sum($SetWidths)-22,22));
            $this->SetAligns(array('C','R'));
            $last = array('Total',number_format($total,0,',','.'));

            $this->Row($last);             
        }
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
            $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
          $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
          $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
          $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
          $c = $s[$i];
          if ($c == "\n") {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
          }
          if ($c == ' ')
            $sep = $i;
          $l+=$cw[$c];
          if ($l > $wmax) {
            if ($sep == -1) {
              if ($i == $j)
                $i++;
            }
            else
              $i = $sep + 1;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
          }
          else
            $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->Content($data);
$pdf->Output('Settlement ('.$data['post']->tab_name.')'.'_Card['.strtolower($data['post']->card_name).']_'.$data['post']->date_type_name.'_'.$data['post']->start_date.'-'.$data['post']->end_date.".pdf", 'I');
