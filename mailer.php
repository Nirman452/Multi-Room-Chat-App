<?php
require_once 'log.php';

require 'vendor/autoload.php';

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
                
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

$roomid = $_SESSION['room'];
$username = $_SESSION['userName'];
$userEmail = $_SESSION['userEmail'];


if ($handle = opendir("logs/$roomid")) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {

            $new = str_replace("'", "", "\logs\'$roomid'\'$entry'");

            $strJsonFileContents = file_get_contents(getcwd(). $new);
            $array = json_decode($strJsonFileContents, true);
          
           echo "<br>USER is: ".($array['user'])." mail is: ".($array['email'])." status is: ".($array['status']);

            if(in_array('"status":0', $array)){
                $offlineUserMail = $array['email'];
            
                echo "<br>email od onlajn korisnika ".$userEmail." za oflajn korisnika ".$offlineUserMail;
                
                try {
                    //Server settings
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';   
                    $mail->SMTPAuth   = true;                               
                    $mail->Username   = 'elvis.ahm.p@gmail.com';  
                    $mail->Password   = 'fxsnzjnqwlsxqwfs'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
                    $mail->Port       = 587;         
                
                    //Recipients
                    $mail->setFrom('elvis.ahm.p@gmail.com', 'Chat informacije');
                    $mail->addAddress($offlineUserMail);
                    $mail->addReplyTo('elvis.ahm.p@gmail.com', 'Information');
                    $mail->addCC('cc@example.com');
                    $mail->addBCC('bcc@example.com');
                
                    // Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Imate novu neprocitanu poruku!';
                    $mail->Body    = 'Dobili ste novu poruku od korisnika <b>'.$username.'</b>';
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                
                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

            } 
        }
        
    
    }

    closedir($handle);
}
