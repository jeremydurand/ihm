<?php

require_once 'Queue.php';
require_once 'Spool.php';

function timeStamp($date) {
    // Transformer date "texte" en "timestamp"
    $date2 = str_replace('-', ' ', $date);
    $date3 = str_replace('/', '-', $date2);
    return strtotime($date3); // retourne la date au format "secondes"
}

function size_file($octets) {
    $resultat = $octets;
    for ($i = 0; $i < 8 && $resultat >= 1024; $i++) {
        $resultat = $resultat / 1024;
    }
    if ($i > 0) {
        return preg_replace('/,00$/', '', number_format($resultat, 2, '.', ''))
                . ' ' . substr('KMGTPEZY', $i - 1, 1) . 'B';
    } else {
        return $resultat . ' B';
    }
}

function status_constant($status) {
    if ($status == 'ready') {
        $status = Spool::READY;
    } else if ($status == 'held' || $status == 'hold') {
        $status = Spool::HELD;
    } else if ($status == 'error') {
        $status = Spool::ERROR;
    } else {
        $status = Spool::PROCESSING;
    }
    return $status;
}

function parser($fd) {
    $queues = array();
    fgets($fd);
    while (!feof($fd)) {
        $line = fgets($fd);
//        $tmp = preg_split('/\s+/', $line);
        $tmp = explode("\t", $line);
        if(empty($tmp[1])) {
            continue;
        }
        if ($tmp[1] == 'queue') {
            $queues[] = parser_queue($tmp);
        } else if (count($tmp) >= 9) {
            end($queues);
            $lastQueue = &$queues[key($queues)];
            $lastQueue->addSpool(parser_spool($tmp));
        } else {
            // do nothing
        }
    }
    return $queues;
}

function parser_queue($tmp) {
    $tmp[0] = status_constant($tmp[0]);
    $queue = new Queue($tmp[2], $tmp[0]);
    return $queue;
}

function parser_spool($tmp) {
    $newDate = timeStamp($tmp[5]);
    $dev = $tmp[1];
    $tmp[1] = status_constant($tmp[1]);
    $spool = new Spool($tmp[3], $tmp[1], $tmp[2], $newDate, $tmp[7]);
    if ($spool->getStatus() == Spool::PROCESSING) {
        $spool->setDevice($dev);
    }
    return $spool;
}

function spoolsByQueue ($queue){
    $ready = 0 ;
    $held = 0;
    $error = 0;
    $processing = 0;
    $recap = array();
    $spoolListByQueue = $queue->getSpoolsByQueue();
    $compteur = count($spoolListByQueue);
    foreach ($spoolListByQueue as $spool) {
        if($spool->getStatus() == 0){
            $ready += 1;
        }elseif ($spool->getStatus() == 1) {
            $held += 1;
        }elseif ($spool->getStatus() == 2) {
            $error += 1;
        }else{
            $processing += 1;
        }
    }
    array_push($recap, $compteur, $processing, $ready, $held, $error);
    return $recap;
}

?>
