<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once('topsis.php');
include_once('haversine.php');
require APPPATH . '/libraries/REST_Controller.php';
// use Restserver\Libraries\REST_Controller;

class Recommendations extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    function index_get(){
        $lat = $this->get('lat');
        $long = $this->get('long');
        $posUser = [$lat, $long];
        $choice = explode(" ", $this->get('choice'));
        $userWeight = explode(" ", $this->get('weight'));
        $hvsnResult = $this->haversine($posUser, $choice, $this->db->get('data_tempat')->result());
        $recommendation = $this->topsis($hvsnResult, $userWeight, $this->db->get('data_tempat')->result());
        $this->response(array("result" => $recommendation, 200));
    }

    function haversine($posUser, $userChoice, $arrData){
        $hvsn = new Haversine();
        $arrResult = array();
        $arrResult = $hvsn->math_haversine($posUser, $userChoice, $arrData);
        return $arrResult;
    }

    function topsis($hvsnResult, $userWeight, $dbRaw){
        $topsis = new TOPSIS();
        $arrResult = array();
        $arrResult = $topsis->math_topsis($hvsnResult, $userWeight, $dbRaw);
        return $arrResult;
    }
}
       
?>