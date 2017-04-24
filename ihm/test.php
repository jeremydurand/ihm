<?php

$timestamp_debut = microtime(true);
        set_time_limit(0);
        require_once 'include.php';

//        $fd = fopen('SimplifyIpstat.txt', 'r');
//        $fd = fopen('SimplifyIpstat1000.txt', 'r');
        $fd = fopen('SimplifyIpstat3000.txt', 'r');
//        $fd = fopen('lpstat.txt', 'r');
//$fd = popen('map_lpstat.exe -t', 'r'); 
        $queues = parser($fd);
        $last = null;
        foreach ($queues as $queue) {

            foreach ($queue->getSpools() as $spool) {
                if($last != $queue){
                    $recap = spoolsByQueue($queue)[0] .' entrie(s) : '. spoolsByQueue($queue)[1] .' processing, ' 
                          .spoolsByQueue($queue)[2] .' ready, '. spoolsByQueue($queue)[3] .' held, '. spoolsByQueue($queue)[4] .' error</br>';
                    echo $recap;
                    $last = $queue;
                }else{
                    // afficher comme au dessus sans faire appel à la fonction spoolsByQueue (gain de temps)
                    echo $recap;
                }
            }
        }
        $timestamp_fin = microtime(true);
    $difference_ms = $timestamp_fin - $timestamp_debut;
    echo 'Exécution du script : ' . $difference_ms . ' secondes.';

