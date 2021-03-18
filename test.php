<?php
    $dir = "JSONchat/";
    $files = scandir($dir);
    for($j=2; $j<count($files);$j++){
        $str = file_get_contents($dir . $files[$j]);
        echo "<p>Chatroom: " . substr($files[$j],4,-5) . "<p>";
        $json = json_decode($str, true);

        // var_dump($json);

        for($i = 0; $i < count($json);$i++){
            $name = $json[$i]['from'];
            $message = $json[$i]['message'];
            $datetime = $json[$i]['timestamp'];
            echo $name . ": " . $message . ", " . date('d/m/Y H:i:s', $datetime) . "<br>";
        }
    }
?>

