<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/presentation.css"/>
        <link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/daterangepicker.css"/>
        <title>IHM</title>
    </head>
    <body>

        <?php
        $timestamp_debut = microtime(true);
        set_time_limit(0);
        require_once 'include.php';

        $fd = fopen('SimplifyIpstat.txt', 'r');
//        $fd = fopen('SimplifyIpstat1000.txt', 'r');
//        $fd = fopen('SimplifyIpstat3000.txt', 'r');
//        $fd = fopen('lpstat.txt', 'r');
//$fd = popen('map_lpstat.exe -t', 'r'); 
        $queues = parser($fd);
        $last = null;
?>
        
<div class="container-fluid">   
<?php
        echo '<table id="example" class="table table-striped table-hover display" cellspacing="0" width="100%">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th align="left">Queue Name</th>';
                        echo '<th align="left">Queue Name | Status</th>';
                        echo '<th align="center">Status</th>';
                        echo '<th align="center">Date and Time</th>';                        
                        echo '<th align="center">Name</th>';
                        echo '<th align="center">Size</th>';
                        echo '<th align="center">Id</th>';
                        echo '<th align="center">Control</th>';
                        echo '<th align="center">Queue Status</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tfoot>';
                    echo '<tr>';
                        echo '<th align="center">Queue Name</th>';
                        echo '<th align="center">Queue Name | Status</th>';
                        echo '<th align="center">Status</th>';
                        echo '<th align="center">Date and Time</th>';
                        echo '<th align="center">Name</th>';
                        echo '<th align="center">Size</th>';
                        echo '<th align="center">Id</th>';
                        echo '<th align="center"></th>';
                        echo '<th align="center">Queue Status</th>';
                    echo '</tr>';
                echo '</tfoot>';
            echo '<tbody>';

        foreach ($queues as $queue) {

            foreach ($queue->getSpools() as $spool) {
               
                    echo '<tr class="status_'. $spool->getStatusAsString() .'">';
                    echo '<td>' . $queue->getName() .'</td>';
                    if($last != $queue){    // A chaque fois que la queue change récapitule les informations de celle-ci
                        $recap = '<td>' . $queue->getName() . " | ". $queue->getStatusAsString() . " | ". spoolsByQueue($queue)[0] .' spool(s) : '. spoolsByQueue($queue)[1] .' processing, ' 
                          .spoolsByQueue($queue)[2] .' ready, '. spoolsByQueue($queue)[3] .' held, '. spoolsByQueue($queue)[4] .' error</td>';
                        echo $recap;
                        $last = $queue;
                    }else{
                    // afficher comme au-dessus sans faire appel à la fonction spoolsByQueue (gain de temps)
                        echo $recap;
                    }
                    echo '<td>' . $spool->getStatusAsString() . " " . $spool->getDevice() . '</td>';
                    echo '<td>' . date("d/m/Y H:i:s",$spool->getDateTime()) . '</td>';
                    echo '<td>' . $spool->getName() . '</td>';
                    echo '<td>' . size_file($spool->getSize()) . '</td>';
                    echo '<td>' . $spool->getId() . '</td>';
                    echo '<td><button type="button" class="btn btn-default btn-xs btn-success spool-free"><span class="glyphicon glyphicon-play"></span></button> '
                    . '<button type="button" class="btn btn-default btn-xs btn-warning spool-hold"><span class="glyphicon glyphicon-pause"></span></button> '
                            . '<button type="button" class="btn btn-default btn-xs btn-danger spool-cancel"><span class="glyphicon glyphicon-trash"></span></button></td>';
                    echo '<td>' . $queue->getStatusAsString() .'</td>';
                    echo '</tr>';
            }
        }
            echo '</tbody>';
        echo '</table>';
        ?>
</div>
<div class="container-fluid"> 
<?php
    $timestamp_fin = microtime(true);
    $difference_ms = $timestamp_fin - $timestamp_debut;
    echo 'Exécution du script : ' . round($difference_ms,2) . ' secondes.';
?>
</div>
        
        <script src="js/jquery-1.12.4.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/date-euro.js"></script>
        <script src="js/file-size.js"></script>
        <script src="js/moment.js"></script>
        <script src="js/daterangepicker.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/affichage.js"></script>
    </body>
</html>