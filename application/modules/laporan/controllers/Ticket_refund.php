<?php

error_reporting(0);

class Ticket_refund extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Html2pdf');
        $this->load->model('Ticket_refund_model', 'model_');

        $this->_module     = 'laporan/ticket_refund';
        $this->report_name = "tiket_refund";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);             
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $get_data = $this->model_->get_data();
            echo json_encode($get_data);
        } else {

            $data = array(
                'home'           => 'Beranda',
                'url_home'       => site_url('home'),
                'title'          => 'Tiket Refund',
                'content'        => 'ticket_refund/index',
                'port'           => $this->model_->getport(),
                'class'          => $this->option_shift_class(),
                'download_pdf'   => checkBtnAccess($this->_module, 'download_pdf'),
                'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
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
        $data['datefrom']        = $this->input->get('datefrom');
        $data['dateto']          = $this->input->get('dateto');
        $data['port']            = $this->enc->decode($this->input->get('port'));
        $data['portname']        = $this->input->get('portname');
        $cek_sc 	             = $this->model_->getClassBySession();

		if ($cek_sc == false) {
			$data['ship_class']   = $this->enc->decode($this->input->get("ship_class"));
			$data['ship_classku'] = $this->input->get("ship_classku");
		} else {
			$data['ship_class']   = $cek_sc['id'];
			$data['ship_classku'] = $cek_sc['name'];
		}
        $fchannel                = $this->input->get('channel');

        $data['fchannel']        = $fchannel;

        if ($fchannel == 'web') {
            $channel = 'online';
        } elseif ($fchannel == 'ifcs') {
            $channel = 'ifcs';
        } else {
            $channel = 'semua';
        }

        $data['channel'] = $channel;
        $data['data']    = $this->model_->get_data();

        $this->load->view($this->_module . '/pdf', $data);
    }

    public function download_excel()
    {
        $datefrom        = $this->input->get('datefrom');
        $dateto          = $this->input->get('dateto');
        $portname        = $this->input->get('portname');
        $fchannel        = $this->input->get('channel');
        $cek_sc 	     = $this->model_->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

        if ($fchannel == 'web') {
            $channel = 'online';
        } elseif ($fchannel == 'ifcs') {
            $channel = 'ifcs';
        } else {
            $channel = 'semua';
        }

        $data = $this->model_->get_data();

        $file_name  = strtoupper("Laporan_tiket_refund_" . $portname . "_" . format_date($datefrom) . "_" . format_date($dateto) . "_" . $ship_classku);
        $sheetsName = 'Laporan Refund';
        $this->load->library('XLSExcel');

        $styleHeader = array(
            'height'     => 50,
            'font'       => 'Arial',
            'font-size'  => 14,
            'font-style' => 'bold',
            'valign'     => 'center',
            'halign'     => 'center'
        );

        $styleSearch = array(
            'font' => 'arial'
        );

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
            'widths'        => [160, 160, 160, 160, 160, 160, 160, 160],
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

        $writer->markMergedCell($sheetsName, 0, 0, 0, 7);
        $writer->markMergedCell($sheetsName, 1, 0, 1, 7);

        // Header Top
        $writer->writeSheetRow($sheetsName, array("LAPORAN TIKET REFUND"), $styleHeader);

        // Search Form
        $writer->writeSheetRow($sheetsName, array());

        $noSearchRow = 0;
        foreach ($arraySearch as $row) {
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 0, $noSearchRow + 2, 1);
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 2, $noSearchRow + 2, 7);
            $writer->writeSheetRow($sheetsName, $row, $styleSearch);
            $noSearchRow++;
        }


        $writer->writeSheetRow($sheetsName, array());

        $headerData1 = array(
            "Uraian",
            "Tarif",
            "Produksi",
            "Biaya Administrasi + Refund",
            "",
            "",
            "Charge Bank",
            "Pengembalian"
        );
        $headerData2  = array("", "", "", "Biaya Admin", "Biaya Refund", "Total", "", "");

        $no = 0;
        $noRow = 8;

        $cFare2 = 0;
        $cProduksi2 = 0;
        $cAdmFee2 = 0;
        $cRefundFee2 = 0;
        $cChargeAmount2 = 0;
        $cBRF2 = 0;
        $cTotalAmount2 = 0;

        foreach ($data as $row) {
            $noRow + $no;
            $writer->writeSheetRow($sheetsName, array($row['title'], "", "", "", "", "", "", ""), $styleHeaderTitle);
            $writer->markMergedCell($sheetsName, $noRow, 0, $noRow, 7);
            $noRow++;

            //Header
            $writer->writeSheetRow($sheetsName, $headerData1, $styleHeader);
            $writer->markMergedCell($sheetsName, $noRow, 0, $noRow + 1, 0);
            $writer->markMergedCell($sheetsName, $noRow, 1, $noRow + 1, 1);
            $writer->markMergedCell($sheetsName, $noRow, 2, $noRow + 1, 2);
            $writer->markMergedCell($sheetsName, $noRow, 3, $noRow, 5);
            $writer->markMergedCell($sheetsName, $noRow, 6, $noRow + 1, 6);
            $writer->markMergedCell($sheetsName, $noRow, 7, $noRow + 1, 7);
            $noRow++;


            $writer->writeSheetRow($sheetsName, $headerData2, $styleHeader);
            $noRow++;

            $cFare2 = 0;
            $cProduksi2 = 0;
            $cAdmFee2 = 0;
            $cRefundFee2 = 0;
            $cChargeAmount2 = 0;
            $cBRF2 = 0;
            $cTotalAmount2 = 0;

            foreach ($row['data'] as $rowA) {
                $writer->writeSheetRow($sheetsName, array($rowA['title'], "", "", "", "", "", "", ""), $styleHeaderTitle);
                $writer->markMergedCell($sheetsName, $noRow, 0, $noRow, 7);
                $noRow++;

                $cFare1 = 0;
                $cProduksi1 = 0;
                $cAdmFee1 = 0;
                $cRefundFee1 = 0;
                $cChargeAmount1 = 0;
                $cBRF1 = 0;
                $cTotalAmount1 = 0;
                foreach ($rowA['data'] as $k => $v) {
                    $cFare1 += $v->fare;
                    $cProduksi1 += $v->produksi;
                    $cAdmFee1 += $v->adm_fee;
                    $cRefundFee1 += $v->refund_fee;
                    $cChargeAmount1 += $v->charge_amount;
                    $cBRF1 += $v->bank_transfer_fee;
                    $cTotalAmount1 += $v->total_amount;

                    $writer->writeSheetRow($sheetsName, array($v->golongan, $v->fare, $v->produksi, $v->adm_fee, $v->refund_fee, $v->charge_amount, $v->bank_transfer_fee, $v->total_amount), $styles1);
                    $noRow++;
                }

                $writer->writeSheetRow($sheetsName, array("Subtotal", $cFare1, $cProduksi1, $cAdmFee1, $cRefundFee1, $cChargeAmount1, $cBRF1, $cTotalAmount1), $stylesfooter);
                $noRow++;

                $cFare2 += $cFare1;
                $cProduksi2 += $cProduksi1;
                $cAdmFee2 += $cAdmFee1;
                $cRefundFee2 += $cRefundFee1;
                $cChargeAmount2 += $cChargeAmount1;
                $cBRF2 += $cBRF1;
                $cTotalAmount2 += $cTotalAmount1;
            }


            $writer->writeSheetRow($sheetsName, array("Total " . $row['title'], $cFare2, $cProduksi2, $cAdmFee2, $cRefundFee2, $cChargeAmount2, $cBRF2, $cTotalAmount2), $stylesfooter);
            $noRow++;

            $cFare3 += $cFare2;
            $cProduksi3 += $cProduksi2;
            $cAdmFee3 += $cAdmFee2;
            $cRefundFee3 += $cRefundFee2;
            $cChargeAmount3 += $cChargeAmount2;
            $cBRF3 += $cBRF2;
            $cTotalAmount3 += $cTotalAmount2;


            $no++;
        }

        $writer->writeSheetRow($sheetsName, array("Total Refund", $cFare3, $cProduksi3, $cAdmFee3, $cRefundFee3, $cChargeAmount3, $cBRF3, $cTotalAmount3), $stylesfooter);


        // foreach ($rows as $row)
        //     $writer->writeSheetRow($sheetsName, $row);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
}
