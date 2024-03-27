<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* -----------------------
* CLASS NAME : LabelPidsModel
* -----------------------
*
* @author     adat <adatdt@gmail.com>
* @copyright  2021
*
*/

class VaccineReportModel extends MY_Model{

    public function __construct() {
    parent::__construct();
        $this->_module   = 'pids/adsDisplay';
    }

    public function dataList(){
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $port= $this->enc->decode($this->input->post('port'));
        $masterStatus= $this->enc->decode($this->input->post('masterStatus'));
        $shipClass= $this->enc->decode($this->input->post('shipClass'));
        $service= $this->enc->decode($this->input->post('service'));
        $passangerType= $this->enc->decode($this->input->post('passangerType'));
        $vehicleClass= $this->enc->decode($this->input->post('vehicleClass'));
        $statusValid= $this->enc->decode($this->input->post('statusValid'));
        $dateTo= trim($this->input->post('dateTo'));
        $dateFrom= trim($this->input->post('dateFrom'));
        $startJam= trim($this->input->post('startJam'));
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        // $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
        $searchData = $this->input->post('searchData');
        $searchName = $this->input->post('searchName');
        $iLike        = trim(str_replace(array("'",'"'),"",$searchData));


        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));


        $field = array(
            0 =>'created_on',
            1=>"booking_code",
            2=>"ticket_number",
            3=>"port_name",
            4=>"service_name",
            5=>"ship_class_name",
            6=>"vehicle_class_name",
            7=>"plat_no",
            8=>"passanger_type_name",
            9=>"name",
            10=>"type_id_name",
            11=>"id_number",
            12=>"age",
            13=>"gender",
            14=>"city",
            15=>"add_manifest_channel",
            16=>"depart_date",
            17=>"depart_time_start",
            18=>"description",
            19=>"ship_name",
            20=>"vaccine",
            21=>"vaccine_status_pl",
            22=>"ticket_number",
            23=>"reason",            
        );        

        $order_column = $field[$order_column];

        // $where = " WHERE vc.status is not null ";
        $where = " WHERE vc.status is not null and bk.depart_date >= '". $dateFrom . "' and bk.depart_date < '" . $dateToNew . "'";

        if(!empty($port))
        {
            $where .=" and ( bp.origin={$port}) ";
        }

        if(!empty($shipClass))
        {
            $where .=" and ( bp.ship_class={$shipClass}) ";
        }        

        if(!empty($service))
        {
            $where .=" and ( bp.service_id={$service}) ";
        }          

        if(!empty($masterStatus))
        {
            $where .=" and ( st.description='{$masterStatus}') ";
        }              

        if(!empty($passangerType))
        {
            $where .=" and ( bp.passanger_type_id='{$passangerType}') ";
        }                                              

        if(!empty($vehicleClass))
        {
            $where .=" and ( bv.vehicle_class_id='{$vehicleClass}') ";
        }                                              

        // if(!empty($statusValid))
        // {

        //     if($statusValid=='valid')
        //     {

        //         $where .=" and ( vc.vaccine_status_pl>0) ";
        //     }
        //     else
        //     {
        //         $where .=" and ( vc.vaccine_status_pl<1 or  vc.vaccine_status_pl is null  ) ";

        //     }
        // } 


        if(!empty($statusValid))
        {

            if($statusValid=='validated')
            {

                $where .=" and ( (vc.vaccine='f' and vc.vaccine_status_pl>0) or vc.vaccine='t')";
            }
            else
            {
                $where .=" and ( vc.vaccine='f'   and  (vc.vaccine_status_pl<1 or vc.vaccine_status_pl is null ) or vc.vaccine is null ) ";

            }
        } 

        

        if(!empty($startJam))
        {
            $where .=" and ( bk.depart_time_start between '{$startJam}:00' and '{$startJam}:59' ) ";   
        }

        if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .="and ( bp.booking_code ilike '%".$iLike."%')";
            }
            else if($searchName=='ticketNumber')
            {
                $where .="and ( vc.ticket_number ilike '%".$iLike."%')";   
            }
            else if($searchName=='platNo')
            {
                $where .="and ( bv.id_number ilike '%".$iLike."%')";   
            }
            else if($searchName=="name")
            {
                $where .="and ( bp.name ilike '%".$iLike."%')";      
            }
            else if($searchName=="idNo")
            {
                $where .="and ( bp.id_number ilike '%".$iLike."%')";       
            }            
            else
            {
              $where .="";
            }
          
        }

        $sql  = $this->getQry($where);

        // die($sql ); exit;
        $sqlCount  = $this->getQryCount($where);

        $query         = $this->dbView->query($sql);
        // $records_total = $query->num_rows();

        $records_total = $this->dbView->query($sqlCount)->row()->count_data;
        $sql      .= " ORDER BY ".$order_column." {$order_dir}";


        if($length != -1){
          $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i    = ($start + 1);

        foreach ($rows_data as $row) {
            $id_enc=$this->enc->encode($row->id);
            $row->number = $i;

            // jika fieldnya vaksin f, vaccine_status_pl tidak null atau keisi 0  
            if($row->vaccine=='f' and ($row->vaccine_status_pl !=null or $row->vaccine_status_pl !="" ))
            {

                if($row->vaccine_status_pl>0)
                {                
                    $row->vaccine=success_label("validated");
                    // $row->vaccine="validated";
                    $row->vaccine_status_pl=$row->vaccine_status_pl;
                    $row->reason="";

                }
                else
                {
                    $row->vaccine=failed_label("not validated"); 
                    // $row->vaccine="not validated";      
                    $row->vaccine_status_pl="";

                    if($row->under_age=='t')
                    {

                        $row->reason=$row->under_age_reason;
                    }


                }
            }
            else
            {
                if($row->vaccine=='t')
                {
                    $row->vaccine=success_label("validated");
                    // $row->vaccine="validated";
                    $row->vaccine_status_pl=$row->vaccine_status_pl;
                }
                else // jika  null atau keisi 0 dengan vaccine false
                {
                    $row->vaccine=failed_label("not validated");
                    // $row->vaccine="not validated";            
                    $row->vaccine_status_pl="";

                    if($row->under_age=='t')
                    {

                        $row->reason=$row->under_age_reason;
                    }
                }

            }

            if(empty($row->add_manifest))
            {
                $row->add_manifest_channel="-";
            }

            $row->testCovid=$this->getTestCovid($row->ticket_number);
            $row->ticket_number="<a href='".site_url('transaction/ticket_tracking/index/'.$row->ticket_number)."' >$row->ticket_number</a>";

            $row->no=$i;

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        
        return array(
          'draw'           => $draw,
          'recordsTotal'   => $records_total,
          'recordsFiltered'=> $records_total,
          'data'           => $rows
        );



    }

    function searchForId($vaccineStatus, $array) {

        $data=array();
        foreach ($array as $key => $val) {
            if ($val->vaccine == $vaccineStatus) {
                $data[]=$val;
            }
        }
        return $data;
     }    

    public function download()
    {


        $port= $this->enc->decode($this->input->get('port'));
        $shipClass= $this->enc->decode($this->input->get('shipClass'));
        $service= $this->enc->decode($this->input->get('service'));
        $masterStatus= $this->enc->decode($this->input->get('masterStatus'));
        $passangerType= $this->enc->decode($this->input->get('passangerType'));
        $vehicleClass= $this->enc->decode($this->input->get('vehicleClass'));
        $statusValid=$this->enc->decode($this->input->get('statusValid'));
        $dateTo= trim($this->input->get('dateTo'));
        $dateFrom= trim($this->input->get('dateFrom'));
        $startJam= trim($this->input->get('startJam'));
        $searchData = $this->input->get('searchData');
        $searchName = $this->input->get('searchName');
        $iLike        = trim(str_replace(array("'",'"'),"",$searchData));        

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        
        // $where = " WHERE vc.status is not null ";
        $where = " WHERE vc.status is not null and bk.depart_date >= '". $dateFrom . "' and bk.depart_date < '" . $dateToNew . "'";

        if(!empty($port))
        {
            $where .=" and ( bp.origin={$port}) ";
        }

        if(!empty($shipClass))
        {
            $where .=" and ( bp.ship_class={$shipClass}) ";
        }        

        if(!empty($service))
        {
            $where .=" and ( bp.service_id={$service}) ";
        }

        if(!empty($masterStatus))
        {
            $where .=" and ( st.description='{$masterStatus}') ";
        }

        if(!empty($passangerType))
        {
            $where .=" and ( bp.passanger_type_id='{$passangerType}') ";
        }                                              

        if(!empty($vehicleClass))
        {
            $where .=" and ( bv.vehicle_class_id='{$vehicleClass}') ";
        }                                              


        if(!empty($startJam))
        {
            $where .=" and ( bk.depart_time_start between '{$startJam}:00' and '{$startJam}:59' ) ";   
        } 

        if(!empty($statusValid))
        {

            if($statusValid=='valid')
            {

                $where .=" and ( vc.vaccine_status_pl>0) ";
            }
            else
            {
                $where .=" and ( vc.vaccine_status_pl<1 or  vc.vaccine_status_pl is null  ) ";

            }
        }                                                      



        if(!empty($port))
        {
            $where .=" and ( bp.origin={$port}) ";
        }

        if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .="and ( bp.booking_code ilike '%".$iLike."%')";
            }
            else if($searchName=='ticketNumber')
            {
                $where .="and ( vc.ticket_number ilike '%".$iLike."%')";   
            }
            else if($searchName=='platNo')
            {
                $where .="and ( bv.id_number ilike '%".$iLike."%')";   
            }
            else if($searchName=="name")
            {
                $where .="and ( bp.name ilike '%".$iLike."%')";      
            }
            else if($searchName=="idNo")
            {
                $where .="and ( bp.id_number ilike '%".$iLike."%')";       
            }            
            else
            {
              $where .="";
            }
          
        }  

        $qry=$this->getQry($where." order by bk.depart_date asc ");

        // die($qry); exit;

        $result=$this->dbView->query($qry)->result();
        
        $data=array();

        foreach ($result as $key => $row) {

            // if($row->vaccine=='t')
            // {
            //     $row->vaccine=" validated ";
            // }
            // else
            // {
            //     $row->vaccine=" not validated ";   
            // }

            if($row->vaccine=='f' and ($row->vaccine_status_pl !=null or $row->vaccine_status_pl !="" ))
            {
                if($row->vaccine_status_pl>0)
                {                
                    $row->vaccine="validated";
                    $row->reason="";
                }
                else
                {
                    $row->vaccine="not validated";      
                    $row->vaccine_status_pl="";

                    if($row->under_age=='t')
                    {
                        $row->reason=$row->under_age_reason;
                    }
                }
            }
            else
            {
                if($row->vaccine=='t')
                {
                    $row->vaccine="validated";
                }
                else
                {
                    $row->vaccine="not validated"; 
                    if($row->under_age=='t')
                    {
                        $row->reason=$row->under_age_reason;
                    }  
                }

            }

            if(empty($row->add_manifest))
            {
                $row->add_manifest_channel="-";
            }

            $row->testCovid=$this->getTestCovid($row->ticket_number);


            $data[]=$row;
        }

        
        return $data;
    }
    public function getTestCovid($ticketNumber)
    {
        $qry= " select * from app.t_trx_test_covid where ticket_number='{$ticketNumber}' and status=1 order by date asc ";
        $qryCount= " select count(id) as count_data from app.t_trx_test_covid where ticket_number='{$ticketNumber}' and status=1 ";
        
        $getData=$this->dbView->query($qry)->result();
        $getCount=$this->dbView->query($qryCount)->row();

        $data=array();

        if($getCount->count_data>0)
        {
            foreach ($getData as $key => $value) {
                $data[]="- Tes : ".$value->type.",  Tanggal Test: ".format_date($value->date)." ({$value->result}) ";
            }
        }

        return implode("<br>",$data);
    }

    public function getQry($where)
    {
        $qry="
                SELECT
                  tmvp.under_age_reason,
                  vc.created_on,
                  vc.id,
                  bp.booking_code,
                  vc.ticket_number,
                  p.name as port_name,
                  sv.name as service_name,
                  sc.name as ship_class_name,
                  vcl.name as vehicle_class_name,
                  bv.id_number as plat_no,
                  pt.name as passanger_type_name,
                  bp.name,
                  ti.name as type_id_name,
                  bp.id_number,
                  bp.age,
                  bp.gender,
                  bp.city,
                  bp.add_manifest,
                  bp.add_manifest_channel,
                  bk.depart_date,
                  bk.depart_time_start,
                  st.description,
                  sp.name as ship_name,
                  vc.vaccine,
                  vc.vaccine_status_pl,
                  vc.under_age,
                  apd.question_text as reason
                from app.t_trx_vaccine vc
                left join app.t_trx_booking_passanger bp on vc.ticket_number=bp.ticket_number
                left join app.t_mtr_port p on bp.origin=p.id
                left join app.t_mtr_passanger_type_id ti on bp.id_type=ti.id
                left join app.t_mtr_service sv on bp.service_id=sv.id
                left join app.t_mtr_ship_class sc on bp.ship_class=sc.id
                left join app.t_trx_booking_vehicle bv on  bp.booking_code=bv.booking_code
                left join app.t_mtr_vehicle_class vcl on bv.vehicle_class_id=vcl.id
                left join app.t_mtr_passanger_type pt on bp.passanger_type_id=pt.id
                left join app.t_trx_booking bk on bp.booking_code=bk.booking_code 
                left join app.t_mtr_status st on bp.status=st.status and tbl_name='t_trx_booking_passanger'
                left join app.t_trx_boarding_passanger brp on bp.ticket_number=brp.ticket_number
                left join app.t_trx_open_boarding ob on brp.boarding_code=ob.boarding_code
                left join app.t_mtr_ship sp on ob.ship_id=sp.id
                left join app.t_mtr_assessment_param_detail apd on vc.reason=apd.id
                left join app.t_mtr_vaccine_param tmvp on  tmvp.id=vc.vaccine_param_id
                {$where}

        ";

        // die($qry); exit;
        return $qry;
    }


    public function getQryCount($where)
    {
        $qry="
                SELECT
                  count(vc.id) as count_data
                from app.t_trx_vaccine vc
                left join app.t_trx_booking_passanger bp on vc.ticket_number=bp.ticket_number
                left join app.t_mtr_port p on bp.origin=p.id
                left join app.t_mtr_passanger_type_id ti on bp.id_type=ti.id
                left join app.t_mtr_service sv on bp.service_id=sv.id
                left join app.t_mtr_ship_class sc on bp.ship_class=sc.id
                left join app.t_trx_booking_vehicle bv on  bp.booking_code=bv.booking_code
                left join app.t_mtr_vehicle_class vcl on bv.vehicle_class_id=vcl.id
                left join app.t_mtr_passanger_type pt on bp.passanger_type_id=pt.id
                left join app.t_trx_booking bk on bp.booking_code=bk.booking_code 
                left join app.t_mtr_status st on bp.status=st.status and tbl_name='t_trx_booking_passanger'
                left join app.t_trx_boarding_passanger brp on bp.ticket_number=brp.ticket_number
                left join app.t_trx_open_boarding ob on brp.boarding_code=ob.boarding_code
                left join app.t_mtr_ship sp on ob.ship_id=sp.id
                left join app.t_mtr_assessment_param_detail apd on vc.reason=apd.id
                left join app.t_mtr_vaccine_param tmvp on  tmvp.id=vc.vaccine_param_id
                {$where}

        ";

        return $qry;
    }    

    public function select_data($table, $where)
    {
        return $this->dbView->query("select * from $table $where");
    }

    public function insert_data($table,$data)
    {
        $this->db->insert($table, $data);
    }

    public function insert_data_batch($table,$data)
    {
        $this->db->insert_batch($table, $data);
    }

    public function update_data($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    public function delete_data($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->delete($table, $data);
    }


}
