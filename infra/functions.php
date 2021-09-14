<?php 

function convertDateBrToDateDb($date_br) 
{
    $date = DateTime::createFromFormat(
        'd/m/Y', $date_br
    );

    $date_db = $date->format('Y-m-d');

    return $date_db;
}

function convertHourSourceToHourDb($hour_source) 
{  
    $hour_db = str_replace("h", ":", $hour_source) . ':00';
    return $hour_db;
}

function requestCurlHtml($host)
{
    $process = curl_init($host);
    curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: text/html'));
    curl_setopt($process, CURLOPT_HEADER, 1);
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_POST, false);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
    $return_text = curl_exec($process);
    curl_close($process);

    return $return_text;
}

function showResults($captured_news, $recorded_news) 
{  
    echo '<hr>';
    echo "<h3>Not&iacute;cias capturadas: $captured_news</h3><br>";
    echo "<h3>Not&iacute;cias gravadas: $recorded_news</h3><hr>";
}
