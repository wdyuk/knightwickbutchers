<?php

function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') 
{ 
    $theta = $longitude1 - $longitude2; 
    $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
    $distance = acos($distance); 
    $distance = rad2deg($distance); 
    $distance = $distance * 60 * 1.1515; 

    switch($unit) 
    { 
        case 'Mi': break; 
        case 'Km': $distance = $distance * 1.609344; 

    } 

    return (round($distance,2)); 

}

function getStaticBlock($blockIds, $type = 'MIDDLE')
{
    $blockHtml = '';
    
    foreach($blockIds as $blockId)
    {
        $blockData =  table_fetch_row('blocks', 'type = "' . $type . '" AND id = ' . $blockId, 'position ASC');

        if($blockData !== false)
        {
            $blockHtml .= $blockData['content'];
        }
    }
    
    return $blockHtml;
}

?>
