<?php
    class TOPSIS{
        public function math_topsis($hvsnResult, $userWeight, $dbTempat){
            $arrTopsis = array();
            for ($count=0; $count < count($hvsnResult); $count++) { 
                for ($i=0; $i < count($dbTempat); $i++) { 
                    $db_id = $dbTempat[$i]->id;
                    $count_id = $hvsnResult[$count]["id"];
                    if($db_id == $count_id){
                        $new_array = ["id" => $dbTempat[$i]->id, "jarak" =>  $hvsnResult[$count]["results"], "harga" =>  $dbTempat[$i]->harga,
                        "rating" => $dbTempat[$i]->rating, "umur" =>  (date("Y") - $dbTempat[$i]->tahunBerdiri)];
                        $arrTopsis[] = $new_array;
                    }
                }
            }
            $rNorm = $this->normR($arrTopsis);
            $vNormWeight = $this->normWeightV($rNorm, $userWeight);
            $idealSolution = $this->ideals($vNormWeight);
            $spMeasure = $this->separationMeasure($idealSolution, $vNormWeight);
            $adjacency = $this->altAdjacency($spMeasure);
            for ($i=0; $i < sizeof($arrTopsis); $i++) { 
                if($arrTopsis[$i]["id"] == $adjacency){
                    $results = ["id" => $arrTopsis[$i]["id"], "nama" => $dbTempat[$i]->namaTempat, "jarak" => $arrTopsis[$i]["jarak"], 
                    "lat" => $dbTempat[$i]->latTempat, "long" => $dbTempat[$i]->lonTempat];
                    break;
                }
            }
            return $results;
        }

        public function normR($arrTopsis){
            $divider = array();
            $r_norm = array();
            $temp = [array_column($arrTopsis, "jarak"), array_column($arrTopsis, "harga"), 
            array_column($arrTopsis, "rating"), array_column($arrTopsis, "umur")];
            for ($j=0; $j < sizeof($temp); $j++) { 
                $a = 0;
                for ($i=0; $i < sizeof($temp[$j]); $i++) { 
                    $a += pow($temp[$j][$i], 2);
                }
                $divider[] = sqrt($a);
            }
            $j = 0;
            foreach ($arrTopsis as $key1 => $val1) {
                $row_norm = array();
                $i = 0;
                foreach ($val1 as $key2 => $val2) {
                    if ($key2 == "id") {
                        continue;
                    } else {
                        $row_norm[] = $val2 / $divider[$i];
                    }
                    $i += 1;
                }
                $r_norm []= ["id" => $arrTopsis[$j]["id"], "jarak" => $row_norm[0], "harga" => $row_norm[1], "rating" => $row_norm[2], "umur" => $row_norm[3]];
                $j += 1;
            }
            return $r_norm;
        }

        public function normWeightV($rNorm, $userWeight){
            $v_normWeight = array();
            $j = 0;
            foreach ($rNorm as $key1 => $val1) {
                $v = array();
                $i = 0;
                foreach ($val1 as $key2 => $val2) {
                    if ($key2 == "id") {
                        continue;
                    } else {
                        $v[] = $userWeight[$i] * $val2;
                    }
                    $i += 1;
                }
                $v_normWeight[] = ["id" => $rNorm[$j]["id"], "jarak" => $v[0], "harga" => $v[1], "rating" => $v[2], "umur" => $v[3]];
                $j += 1;
            }
            return $v_normWeight;
        }

        public function ideals($vNormWeight){
            $ideals = array();
            $temp = [array_column($vNormWeight, "jarak"), array_column($vNormWeight, "harga"), 
            array_column($vNormWeight, "rating"), array_column($vNormWeight, "umur")];
            $ideals[0] = [min($temp[0]), min($temp[1]), max($temp[2]), max($temp[3])];
            $ideals[1] = [max($temp[0]), max($temp[1]), min($temp[2]), min($temp[3])];
            return $ideals;
        }

        public function separationMeasure($ideals, $vNormWeight){
            $sp_measure = array();
            for ($k=0; $k < sizeof($ideals); $k++) { 
                $sp = array();
                $j = 0;
                foreach ($vNormWeight as $key1 => $val1) {
                    $s = 0;
                    $i = 0;
                    foreach ($val1 as $key2 => $val2) {
                        if ($key2 == "id") {
                            continue;
                        } else {
                            $s += pow($val2 - $ideals[$k][$i], 2);
                        }
                        $i += 1;
                    }
                    $sp[] = ["id" => $vNormWeight[$j]["id"], "separationMeasure" => sqrt($s)];
                    $j += 1;
                }
                $sp_measure[] = $sp; 
            }
            return $sp_measure;
        }

        public function altAdjacency($sp_measure){
            $adj = array();
            for ($i=0; $i < sizeof($sp_measure[0]); $i++) { 
                $adj[] = ["id" => $sp_measure[0][$i]["id"], "result" => $sp_measure[1][$i]["separationMeasure"] / ($sp_measure[1][$i]["separationMeasure"]+$sp_measure[0][$i]["separationMeasure"])];
            }
            usort($adj, function($a, $b){
                return $b["result"] <=> $a["result"];
            });
            // $result = ["first" => $adj[0], "last" => $adj[sizeof($adj)-1]];
            return $adj[0]["id"];
        }
    }
?>