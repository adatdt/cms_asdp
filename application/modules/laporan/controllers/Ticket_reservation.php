<?php

error_reporting(0);

class Ticket_reservation extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Html2pdf');
        $this->load->model('Ticket_reservation_model', 'model_');
        $this->_module     = 'laporan/ticket_reservation';
        $this->report_name = "tiket_reservation";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbAction = $this->load->database("dbAction", TRUE);        
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $get_data = $this->model_->get_data();
            echo json_encode($get_data);
        } else {

            $data = array(
                'home'          => 'Beranda',
                'url_home'      => site_url('home'),
                'title'         => 'Tiket Reservasi',
                'content'       => 'ticket_reservation/index',
                'port'          => $this->model_->getport(),
                'class'         => $this->option_shift_class(),
                'download_pdf'  => checkBtnAccess($this->_module, 'download_pdf'),
                'download_excel'=> checkBtnAccess($this->_module, 'download_excel'),
            );

            $this->load->view('default', $data);
        }
    }

    private function option_shift_class()
	{
		$shift_class = $this->model_->getClassBySession('option');
		foreach ($shift_class as $row) {
			if ($row['id'] != '') {
				$id = $this->enc->encode($row['id']);
			} else {
				$id = '';
			}
			$html .= '<option value="' . $id . '">' . $row['name'] . '</option>';
		}
		return $html;
	}

    public function download_pdf()
    {
        $data['datefrom']       = $this->input->get('datefrom');
        $data['dateto']         = $this->input->get('dateto');
        $data['port']           = $this->enc->decode($this->input->get('port'));
        $data['portname']       = $this->input->get('portname');
        $cek_sc 	            = $this->model_->getClassBySession();
		if ($cek_sc == false) {
			$data['ship_class']   = $this->enc->decode($this->input->get("ship_class"));
			$data['ship_classku'] = $this->input->get("ship_classku");
		} else {
			$data['ship_class']   = $cek_sc['id'];
			$data['ship_classku'] = $cek_sc['name'];
		}
        $data['fchannel']       = $this->input->get('channel');
        $data['data']           = $this->model_->get_data();

        $this->load->view($this->_module . '/pdf', $data);
    }

    public function download_excel()
    {
        $datefrom           = $this->input->get('datefrom');
        $dateto             = $this->input->get('dateto');
        $port               = $this->enc->decode($this->input->get('port'));
        $portname           = $this->input->get('portname');
        $cek_sc 	             = $this->model_->getClassBySession();
		if ($cek_sc == false) {
			// $shift_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			// $shift_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}
        $fchannel           = $this->input->get('channel');

        if ($fchannel) {
            $channel = $fchannel;
        } else {
            $channel = 'semua';
        }

        $data = $this->model_->get_data();


        $file_name = strtoupper("Laporan_tiket_reservation_" . $portname . "_" . format_date($datefrom) . "_" . format_date($dateto) . "_" . $ship_classku);
        $sheetsName = 'Laporan Reservation';
        $this->load->library('XLSExcel');
        $styleHeader = array(
            'height'    => 50,
            'font'      => 'Arial',
            'font-size' => 14,
            'font-style'=> 'bold',
            'valign'    => 'center',
            'halign'    => 'center'
        );
        $styleSearch = array(
            'font' => 'arial'
        );

        // $styles1 = array('height' => 50, 'widths' => [2, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 60], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');
        $styleHeader = array(
            'height'        => 30,
            'font'          => 'Arial',
            'font-size'     => 10,
            'font-style'    => 'bold',
            'valign'        => 'center',
            'halign'        => 'center',
            'border'        => 'left,right,top,bottom',
            'border-style'  => 'thin'
        );
        $styleHeaderTitle = array(
            'font'          => 'Arial',
            'font-size'     => 10,
            'font-style'    => 'bold',
            'border'        => 'left,right,top,bottom',
            'border-style'  => 'thin'
        );
        $styles1 = array(
            'widths'        => [160, 160, 160, 160, 160, 160],
            'border'        => 'left,right,top,bottom',
            'font'          => 'Arial',
            'border-style'  => 'thin'
        );
        $stylesfooter = array(
            'border'        => 'left,right,top,bottom',
            'font-style'    => 'bold',
            'font'          => 'Arial',
            'border-style'  => 'thin'
        );

        $arraySearch = array(
            array("Tanggal", "", format_date($datefrom) . " - " . format_date($dateto)),
            array("Pelabuhan", "", strtoupper($portname)),
            array("Kelas Layanan", "", strtoupper($ship_classku)),
            array("Sales Channel", "", strtoupper($channel)),
            array("Payment Channel", "", "(Semua/VA Permata/VA BNI/Alfamart/Yo-Mart/PT Pos/...)")
        );

        $writer = new XLSXWriter();
        $writer->markMergedCell($sheetsName, 0, 0, 0, 5);
        $writer->markMergedCell($sheetsName, 1, 0, 1, 5);

        // Header Top
        $writer->writeSheetRow($sheetsName, array("LAPORAN TIKET RESERVASI"), $styleHeader);

        // Search Form
        $writer->writeSheetRow($sheetsName, array());

        $noSearchRow = 0;
        foreach ($arraySearch as $row) {
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 0, $noSearchRow + 2, 1);
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 2, $noSearchRow + 2, 5);
            $writer->writeSheetRow($sheetsName, $row, $styleSearch);
            $noSearchRow++;
        }


        $writer->writeSheetRow($sheetsName, array());

        $headerData1 = array(
            "Uraian",
            "Tarif",
            "",
            "Produksi",
            "Pendapatan",
            ""
        );
        $headerData2  = array("", "", "", "", "", "");

        $no           = 0;
        $noRow        = 8;
        $cFare2       = 0;
        $cProduksi2   = 0;
        $cPendapatan2 = 0;

        foreach ($data as $row) {
            $noRow + $no;
            $writer->writeSheetRow($sheetsName, array($row['title'], "", "", "", "", ""), $styleHeaderTitle);
            $writer->markMergedCell($sheetsName, $noRow, 0, $noRow, 5);
            $noRow++;

            //Header
            $writer->writeSheetRow($sheetsName, $headerData1, $styleHeader);
            $writer->markMergedCell($sheetsName, $noRow, 1, $noRow, 2);
            $writer->markMergedCell($sheetsName, $noRow, 4, $noRow, 5);
            $noRow++;

            $cFare2       = 0;
            $cProduksi2   = 0;
            $cPendapatan2 = 0;

            foreach ($row['data'] as $rowA) {
                $writer->writeSheetRow($sheetsName, array($rowA['title'], "", "", "", "", ""), $styleHeaderTitle);
                $writer->markMergedCell($sheetsName, $noRow, 0, $noRow, 5);
                $noRow++;

                $cFare1       = 0;
                $cProduksi1   = 0;
                $cPendapatan1 = 0;

                foreach ($rowA['data'] as $k => $v) {
                    $cFare1       += $v->fare;
                    $cProduksi1   += $v->produksi;
                    $cPendapatan1 += $v->pendapatan;

                    $writer->writeSheetRow($sheetsName, array($v->golongan, $v->fare, "", $v->produksi, $v->pendapatan, ""), $styles1);
                    $writer->markMergedCell($sheetsName, $noRow, 1, $noRow, 2);
                    $writer->markMergedCell($sheetsName, $noRow, 4, $noRow, 5);
                    $noRow++;
                }

                $writer->writeSheetRow($sheetsName, array("Subtotal", $cFare1, "", $cProduksi1, $cPendapatan1, ""), $stylesfooter);
                $writer->markMergedCell($sheetsName, $noRow, 1, $noRow, 2);
                $writer->markMergedCell($sheetsName, $noRow, 4, $noRow, 5);
                $noRow++;

                $cFare2       += $cFare1;
                $cProduksi2   += $cProduksi1;
                $cPendapatan2 += $cPendapatan1;
            }

            $writer->writeSheetRow($sheetsName, array("Total " . $row['title'], $cFare2, "", $cProduksi2, $cPendapatan2, ""), $stylesfooter);
            $writer->markMergedCell($sheetsName, $noRow, 1, $noRow, 2);
            $writer->markMergedCell($sheetsName, $noRow, 4, $noRow, 5);
            $noRow++;

            $cFare3 += $cFare2;
            $cProduksi3 += $cProduksi2;
            $cPendapatan3 += $cPendapatan2;
            $no++;
        }

        $writer->writeSheetRow($sheetsName, array("Total reservation", $cFare3, "", $cProduksi3, $cPendapatan3, ""), $stylesfooter);
        $writer->markMergedCell($sheetsName, $noRow, 1, $noRow, 2);
        $writer->markMergedCell($sheetsName, $noRow, 4, $noRow, 5);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
}
