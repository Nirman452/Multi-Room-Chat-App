<!DOCTYPE html>
<?php 
require_once 'chat.php';
?>



<html>
<head>
    <meta charset=utf-8 />
    <title>php-chat | Example | Client 1</title>
    <link rel="stylesheet" type="text/css" media="screen" href="reset.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="style.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">

    <!--Font Awesome-->
    <script src="https://kit.fontawesome.com/45a453ed2d.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="dialog" id="connectFormDialog">
        <form id="connectForm" onsubmit="return connectToChat();" class="chat-form" method="get">
            <input type="text" name="username" id="username" placeholder="username...">
            <input type="text" name="email" id="email" placeholder="email@example.com">
            <input type="text" name="room" id="room" placeholder="room...">
            <input type="submit" value="Connect to chat" neme="connect"/>
        </form>
    </section>
    <div id="message">
        <section id="messageDialog">
            <div class="messages-tools">
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <ul id="messageList">
            </ul>

            <div id="drop_file_zone" ondrop="upload_file(event)" ondragover="return false">

                <form name="message-form" id="messageForm" onsubmit="return sendChatMessage();">
                    <div class="input-group">
                        <input  type="text" id="text" name="message" placeholder="Unesite poruku..." 
                        onkeyup="updateChatTyping()" 
                        required oninvalid="this.setCustomValidity('Poruka ne smije biti prazna')"
                        oninput="setCustomValidity('');">
                        <input type="submit" value="Pošalji" id="button" name="send" onclick="setTimeout(function () { scrollMess(); }, 100), ajax();"/>
                    </div>
                </form>

                <div id="drag_upload_file">
                    <button onclick="file_explorer();">
                        <i class="fas fa-upload"></i>
                        Upload
                    </button>
                    <input type="file" id="selectfile" multiple>
                </div>
                <form action="log.php" method="post"><button type="submit" name="destroy">Destroy</button></form>
            </div>

              <div id="myModal" class="modal">

                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">&times;</span>
                        <h2>Error!</h2>
                    </div>
                    <div class="modal-body">
                         <p>Some file types are not supported!</p>
                         <p>Also, files cannot be larger than 10MB!</p>
                         <p>Please select another file to send.</p>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>

        </section>

    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="custom.js"></script>
</body>
</html>

<script>
    (function() {
        fetch('server.php');
    })();
</script>