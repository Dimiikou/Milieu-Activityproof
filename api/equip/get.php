<?php
require '../../common/php/config.php';
require '../../common/php/equipManager.php';
$equipManager = new equipManager();

$member = $_GET['member'];

$equip = $equipManager->getEquip($member);

if (isset($member)) {
    $equipArray = array();

    if ($equipManager->getEquipAmount($member) > 0) {
        while ($row = mysqli_fetch_assoc($equip)) {
            $equipArray[] = $row;
        }
            
        echo json_encode($equipArray);
    } else {
        echo json_encode($equipArray);
    }
}

?>