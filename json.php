<?php

    if (isset($_POST["list"]) && isset($_POST["room"])) {
        $list = $_POST["list"];
        $room = $_POST["room"];
        $jsonFile = "JSONchat\chat" . $room . ".json";         

        if (file_exists($jsonFile)) {
            $content = file_get_contents($jsonFile);
            $content = rtrim($content,"]");
            $content .= ",".$list . "]";
            $save = fopen($jsonFile, "w");
            fwrite($save, $content); 
            fclose($save);        
        }else 
        {    
            $save = fopen($jsonFile,"w");
            fwrite($save,"[" . $list . "]");
            fclose($save);
        } 
    }
?>