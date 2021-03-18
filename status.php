<?php

    $room = $_REQUEST["r"];
    $user = $_REQUEST["u"];

 
    if ($handle = opendir('logs/' . $room)) { 

        while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {
                if(!strpos($entry, $user)){ 
                    $json_log = file_get_contents("logs/" . $room . "/" . $entry); 
                    $log = json_decode($json_log, true);
                    $userLog =  $log[0]['status'];
                    
                    echo $userLog;
                }
            }
        }

        closedir($handle);
    }

?>
       
    