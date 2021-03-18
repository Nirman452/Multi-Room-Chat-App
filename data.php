<?php
    require_once 'log.php';

    $roomId = $_REQUEST["q"]; // Room ID
    $user = $_REQUEST["u"]; // User

    $dir = "JSONchat/";
    $files = scandir($dir); 


    for($j=2; $j<count($files);$j++){
        $str = file_get_contents($dir . $files[$j]);
        $room = substr($files[$j],4,-5);
        $json = json_decode($str, true);

        if($roomId==$room){
            for($i = 0; $i < count($json);$i++){
                $name = $json[$i]['from'];
                $message = $json[$i]['message'];
                $datetime = $json[$i]['timestamp'];

                // Provjera za fajl
                if(strpos($message,'thumb_') !== false){ 
                    $newMessage = str_replace(array('[',']','"'),"",$message); 
                    echo "<li class=". ($user === $name ? 'messages-user' : 'messages') ."><p class='sender-time'>" . $name . "<span>" . date('d/m/Y H:i:s', $datetime) . "</span></p><img src='" . $newMessage . "' onclick='showOriginal(this);'></li>";
                }elseif(strpos($message,'__file__') !== false){

                    $download = explode('_', $message);

                    $newMessage = str_replace(array('[',']','"'),"",$message);
                    echo "<li class=". ($user === $name ? 'messages-user' : 'messages') ."><p class='sender-time'>" . $name . "<span>" . date('d/m/Y H:i:s', $datetime) . "</span></p><p class='message-body'><a href='" . $newMessage . "'>" . $download[5] . "<i class='fas fa-download'></i></a></p></li>";
                }else{
                    if(!empty($message)){ 
                    echo "<li class=" . ($user === $name ? 'messages-user' : 'messages') .  "><p class='sender-time'>" . $name . "<span>" . date('d/m/Y H:i:s', $datetime) . "</span></p><p class='message-body'>" . $message . "</p></li>";
                    }
                }
            }
        }
    }
?>

