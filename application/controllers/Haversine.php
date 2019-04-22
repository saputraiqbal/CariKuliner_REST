<?php
class Haversine{
    public function math_haversine($posUser, $userChoice, $arrData){
        define("EARTH", 6378);
        $distance=[];
        for ($count=0; $count < count($userChoice); $count++) { 
            for ($i=0; $i < count($arrData); $i++) { 
                $db_id = $arrData[$i]->id;
                $count_id = $userChoice[$count];
                if($db_id == $count_id){
                    $latUser = deg2rad($posUser[0]);
                    $lonUser = deg2rad($posUser[1]);
                    $latData = deg2rad($arrData[$i]->latTempat);
                    $lonData = deg2rad($arrData[$i]->lonTempat);
    
                    $delta_lat = $latData - $latUser;
                    $delta_long = $lonData - $lonUser;
                    
                    $temp_c = 2 * asin(sqrt(pow(sin($delta_lat/2), 2) + 
                        cos($latData) * cos($latUser) * pow(sin($delta_long/2), 2)));
                    $temp_d =  $temp_c * EARTH;
                    $temp_result = ["id" => $arrData[$i]->id, "results" => $temp_d];
                    $distance[]=$temp_result;
                }
            }
        }
        // foreach ($arrData as $value) {
            
        // }
        // $distance[]=$userChoice;
        return $distance;
    }
}
?>