<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
// use Restserver\Libraries\REST_Controller;

class Get_DataRecommend extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get(){
        $idChosen = $this->get('id');
        $tempat = $this->getDataChosen($idChosen, $this->db->get('data_tempat')->result());
        $this->response(array("result" => $tempat, 200));
    }

    function getDataChosen($idChosen, $dbRaw){
        $arrData = array();
        for ($i=0; $i < sizeof($dbRaw); $i++) { 
            if ($dbRaw[$i]->id == $idChosen) {
                $arrData[] = ["id" => $dbRaw[$i]->id, "namaTempat" =>  $dbRaw[$i]->namaTempat, "alamat" =>  $dbRaw[$i]->alamat, 
            "harga" => $dbRaw[$i]->harga, "rating" => $dbRaw[$i]->rating, "tahunBerdiri" => $dbRaw[$i]->tahunBerdiri];
            }
        }
        return $arrData;
    }
}
       
?> 