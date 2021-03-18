<?php
session_start();
if(!empty($_GET['username']) && isset($_GET['room']) && isset($_GET['email'])){
    
    $_SESSION['room']= $_GET['room'];
    $_SESSION['userName']= $_GET['username'];
    $_SESSION['userEmail']= $_GET['email'];
    
    $roomid = $_SESSION['room'];
    $username = $_SESSION['userName'];
    $mail = $_SESSION['userEmail'];


    if (!file_exists("logs/$roomid")) {
        $log = mkdir("logs/$roomid", 0777, true);
    }

    $status = array("room" => $roomid, "user" => $username, "email" => $mail, "status" => 1);
    $file = fopen("logs/$roomid"."/log_$roomid.$username.json", "w");

    fwrite($file,  json_encode($status) );
    fclose($file); 

}

if(isset($_POST['destroy'])){
    $roomid = $_SESSION['room'];
    $username = $_SESSION['userName'];
    $mail = $_SESSION['userEmail'];

    $file = "logs/$roomid"."/log_$roomid.$username.json";

    if(file_exists($file)){
        $jsonString = file_get_contents($file);
        $data = json_decode($jsonString, true);

        $data = array("room" => $roomid, "user" => $username, "email" => $mail, "status" => 0);

        $newJsonString = json_encode($data);
        file_put_contents($file, $newJsonString);
    }

    session_destroy(); 
    header('Location: index.php');
}
