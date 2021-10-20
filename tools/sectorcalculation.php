<?php
//Sector data global variable
global $sectordata;
$sectordata = array(
    array("name" => "Air Travel",
    "allocation" => 0
    ),
    array("name" => "Basic Materials",
    "allocation" => 0
    ),
    array("name" => "Communication Services",
    "allocation" => 0
    ),
    array("name" => "Conglomerates",
    "allocation" => 0
    ),
    array("name" => "Consumer Cyclical",
    "allocation" => 0
    ),
    array("name" => "Consumer Defensive",
    "allocation" => 0
    ),
    array("name" => "Energy",
    "allocation" => 0
    ),
    array("name" => "Financial",
    "allocation" => 0
    ),
    array("name" => "Financial Services",
    "allocation" => 0
    ),
    array("name" => "Healthcare",
    "allocation" => 0
    ),
    array("name" => "Industrial Goods",
    "allocation" => 0
    ),
    array("name" => "Industrials",
    "allocation" => 0
    ),
    array("name" => "Real Estate",
    "allocation" => 0
    ),
    array("name" => "Services",
    "allocation" => 0
    )
    );

//Function to search a multidimensional arrays name for string
function search_multidim_array($string, $inArray){
foreach ($inArray as $key=>$val){
    if($val['name'] === $string){
        return $key;
    }
    
}
return null;
}

//Function to calculate the allocations into each sector
function spit_sectors_toJson($listofSectorData){
    global $sectordata;
    //$listofSectorData -> List populated with the sector of each stock
    foreach($listofSectorData as $sectorRow){
        $outid = search_multidim_array($sectorRow, $sectordata);
        if($outid === null){
        echo "Error while calculating sector information";
        } else{
        $sectordata[$outid]["allocation"] += 1;
        }
    }
    return $sectordata;
}

?>