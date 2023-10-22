<?php 
class apiUtils {

function extractDate($millis) {
    $date = date('d-m-Y H:i:s', $millis / 1000);

    $date = explode('-', explode(' ', $date)[0]); 
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    return $day . "/" . $month . "/" . $year;

}

function extractDateWithMinus($millis) {
    $date = date('d-m-Y H:i:s', $millis / 1000);

    $date = explode('-', explode(' ', $date)[0]); 
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    return $day . "-" . $month . "-" . $year;

}

}
?>