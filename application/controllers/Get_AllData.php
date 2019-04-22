<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
// use Restserver\Libraries\REST_Controller;

class Get_AllData extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get(){
        $allData = $this->getAllData($this->db->get('data_tempat')->result());
        $this->response(array("result" => $allData, "msg" => 200));
    }

    function getAllData($dbRaw){
        $arrData = array();
            for ($i=0; $i < sizeof($dbRaw); $i++) { 
                $new_array = ["id" => $dbRaw[$i]->id, "namaTempat" =>  $dbRaw[$i]->namaTempat, "alamat" =>  $dbRaw[$i]->alamat];
                $arrData[] = $new_array;
            }
            return $arrData;
    }
}
       
?>