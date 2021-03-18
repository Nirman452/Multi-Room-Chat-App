<?php 
require_once 'log.php';
?>

<script>
var chatUrl = 'ws://localhost:9911';

function displayChatMessage(from, message, timestamp) {
    // Ovdje je potrebno odrediti da li je u pitanju slika ili obični tekst, jer se inače formira span element i na osnovu toga uraditi ispis
    // To odrediti na osnovu toga da li postoji dio stringa "__img__" u poruci
    var node = document.createElement("li");
    var user = TempUser;
    node.style.marginBottom= "130px";
    node.style.marginTop= "-110px";

    if(from){
        if (user === from) 
            // Assign class 'messages-user' if current user send message
            node.classList.add("messages-user");
        else 
            // Assign class 'messages' if other user send message
            node.classList.add("messages");
            setTimeout(function () { scrollMess(); }, 100);
    }else{
        /* Assign class 'info' for information messages
        (user typing, user joined to chat, user leave chat, ...) */
        node.classList.add("info");

    }

    if (from) {
        var nameNode = document.createElement("p"); 
        nameNode.classList.add("sender-time")
        var nameTextNode = document.createTextNode(from);
        nameNode.appendChild(nameTextNode);
        node.appendChild(nameNode);
    }

    // Ne treba new array, sam će napraviti array
    // Problem je bio što kad se poruke učitavaju, a nije bio upload, onda nisu u JSON formatu, pa ne može uvijek biti JSON.parse
    // Ovo rješava ovaj problem
    var newFiles;
    try {
        newFiles = JSON.parse(message);
        newFiles.forEach(jsonParse);
    } catch (e) {
        jsonParse(message);
    } 

    function jsonParse(item){

        if(item === "" && user === from){
            errMessage();
        }

        if(item.indexOf('thumb_') !== -1 ){ 
            var messageNode = document.createElement("img");
            messageNode.src= item;
            messageNode.style.cursor = "pointer";

            messageNode.onclick = function(){
                var modal = document.createElement("div");
                modal.classList.add("image-modal","active");

                var modalBody = document.createElement("div");
                modalBody.classList.add("image-modal-body");

                var image = document.createElement("img");
                var path = item.replace("thumb_", "__img__");
                image.src = path;

                var closeButton = document.createElement("button");
                closeButton.classList.add("close-button");
    
                var icon = document.createElement("i");
                icon.classList.add("far", "fa-times-circle");
                closeButton.appendChild(icon);

                var overlay = document.createElement("div");
                overlay.classList.add("overlay", "active");

                modalBody.appendChild(closeButton);
                modalBody.appendChild(image);
                modal.appendChild(modalBody);
                node.appendChild(modal);
                node.appendChild(overlay);

                closeButton.onclick = function () {
                    modal.classList.remove("active");
                    overlay.classList.remove("active");
                };

                overlay.onclick = function () {
                    modal.classList.remove("active");
                    overlay.classList.remove("active");
                };
            }
        }else if(item.indexOf('__file__') !== -1 ){
            var icon = document.createElement("i");
            icon.classList.add("fas", "fa-download");

            var messageNode = document.createElement("p");
            var messageNodeLink = document.createElement("a");
            messageNodeLink.href= item;

            var downloadName = item.split("_");
            
            var messageTextNode = document.createTextNode(downloadName[5]); 

            messageNodeLink.appendChild(messageTextNode); 
            messageNode.appendChild(messageNodeLink);
            messageNode.appendChild(icon);
            messageNode.classList.add("message-body");
        }else{

            if (item === ""){
                //do nothing
            }else{
                var messageNode = document.createElement("p");
                     // If there is a sender assign class to that message
                if(from){
                messageNode.classList.add("message-body");
                var messageTextNode = document.createTextNode(item + " ");
                messageNode.appendChild(messageTextNode); 
                    // If there not, asiign class to information messages
                }else{
                    messageNode.classList.add("info-message");
                    var messageTextNode = document.createTextNode(item + " ");
                    messageNode.appendChild(messageTextNode); 
                }
            }

        }
        node.appendChild(messageNode);
    }

    if(from){
        var timeNode = document.createElement("span");
        var timeTextNode = document.createTextNode(convertToDate(timestamp));
        timeNode.appendChild(timeTextNode);
        nameNode.appendChild(timeNode); 
    }

    // To završava ovdje
    document.getElementById("messageList").appendChild(node);
}


function displayUserTypingMessage(from) {
    var nodeId = 'userTyping'+from.name.replace(' ','');
    var node = document.getElementById(nodeId);
    if (!node) {
        node = document.createElement("LI");
        node.id = nodeId;
        node.classList.add("user-typing");

        node.style.marginBottom= "130px";
        node.style.marginTop= "-110px";

        var messageTextNode = document.createTextNode(from.name + ' typing...');
        node.appendChild(messageTextNode);

        document.getElementById("messageList").appendChild(node);
    }
}

function removeUserTypingMessage(from) {
    var nodeId = 'userTyping' + from.name.replace(' ', '');
    var node = document.getElementById(nodeId);
    if (node) {
        node.parentNode.removeChild(node);
    }
}

function convertToDate(timestamp){
    var date = new Date(timestamp * 1000);
    var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear() + ' ' + ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2);
    return formattedDate;
}

var conn;

var TempRoom = '<?php echo $roomid; ?>' //room, ovako radi
var TempUser = '<?php echo $username; ?>'; //user

function connectToChat() {
    conn = new WebSocket(chatUrl);

    conn.onopen = function() {
        document.getElementById('connectFormDialog').style.display = 'none';
        document.getElementById('messageDialog').style.display = 'flex';

        var params = {
            'roomId': TempRoom,
            'userName': TempUser,
            'action': 'connect'
        };

        if(window.XMLHttpRequest){
            http = new XMLHttpRequest();
        }else{
            http = new ActiveXObject("Microsoft.XMLHTTP");
        }
        http.onreadystatechange = function(){
            if (this.readyState = 4 && this.status == 200){
                document.getElementById("messageList").innerHTML = this.responseText;
            } 
        }
        var room = params.roomId;
        var user = params.userName;
        http.open("GET","data.php?q="+room+"&u="+user,true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send();


        conn.send(JSON.stringify(params));
    };

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data); 
        if (data.hasOwnProperty('message') && data.hasOwnProperty('from')) {
            displayChatMessage(data.from.name, data.message, data.timestamp);
            
            console.log(userStatus(data.roomId, data.from.name));
        }
        else if (data.hasOwnProperty('message')) {
            displayChatMessage(null, data.message, data.timestamp);
        }
        else if (data.hasOwnProperty('type')) {
            if (data.type == 'list-users' && data.hasOwnProperty('clients')) {
                displayChatMessage(null, 'Ukupno je ' + data.clients.length + ' korisnika uključeno', data.timestamp);
            }
            else if (data.type == 'user-started-typing') {
                displayUserTypingMessage(data.from)
            }
            else if (data.type == 'user-stopped-typing') {
                removeUserTypingMessage(data.from);
            }
        }
    };

    conn.onerror = function(e) {
        console.log(e);
    };

    return false;
}

// Dodao varijablu file koja će reći da li se funkcija poziva za prikaz fajla ili chat-a
function sendChatMessage(file) {
    
    if(file){
        // Šaljemo fajl kao poruku
        // I ovdje je trebala izmjena zbog više fajlova
        var chatMessage = file; 
        try {
            chatMessage = JSON.parse(chatMessage);
            chatMessage.forEach(insertChatMessage);
        } catch (e) {
            insertChatMessage(chatMessage);
        }
    }else{
        // Šaljemo poruku kao poruku
        var chatMessage = document.getElementsByName("message")[0].value;
        insertChatMessage(chatMessage); 
    }

    return false;
    
}


var loaded = false;

function ajax(){

    if(loaded) return;

    $.ajax({

        url : 'mailer.php',
        type : 'POST',
        success : function (result) {
            console.log (result); 
        },
        error : function () {
            console.log ('error');
        }
    });

    loaded = true;
}

// Promijenio ovo u funkciju da se može pozivati više puta
function insertChatMessage(chatMessage){
    var d = new Date();
    var params = {
        // Ova vrsta nadovezivanja funkcionira ==> + "a";
        // chatMessage se šalje neovisno od toga da li je u pitanju fajl ili poruka
        'message': chatMessage,
        'action': 'message',
        'timestamp': d.getTime()/1000,
        'from' : TempUser,
        'roomId' : TempRoom
    }; 

    var list = JSON.stringify(params);
    if(window.XMLHttpRequest){
        http = new XMLHttpRequest();
    }else{
        http = new ActiveXObject("Microsoft.XMLHTTP");
    }
    var param = "list="+list+"&room="+params.roomId;
    http.open("POST","json.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(param);

    conn.send(JSON.stringify(params));

    document.getElementsByName("message")[0].value = '';
    
    return false;
}

function updateChatTyping() {
    var params = {};

    if (document.getElementsByName("message")[0].value.length > 0) {
        params = {'action': 'start-typing'};
        conn.send(JSON.stringify(params));
    }
    else if (document.getElementsByName("message")[0].value.length == 1) {
        params = {'action': 'stop-typing'};
        conn.send(JSON.stringify(params));
    }
}

function errMessage(){
    var modal = document.getElementById("myModal");

    var span = document.getElementsByClassName("close")[0];
    
    modal.style.display = "block";

    span.onclick = function() {
    modal.style.display = "none";
    }

    window.onclick = function(event) {
    if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}


function Upload() {
    var selectfile = document.getElementById("selectfile");
    if (typeof (selectfile.files) != "undefined") {
        var size = parseFloat(selectfile.files[0].size / 1024).toFixed(2);
        if(size >10240){
            
            errMessage();
        }
    } 
}

function scrollMess(){
    
    window.scrollTo(0,document.querySelector("#messageDialog").scrollHeight);
    document.getElementsByTagName("BODY")[0].style.marginTop = "150px";
}

function showOriginal(img) {

    var parent = img.parentNode;
    var src = img.getAttribute("src");
    var path = src.replace("thumb_", "__img__");

    var modal = document.createElement("div");
    modal.classList.add("image-modal", "active");

    var modalBody = document.createElement("div");
    modalBody.classList.add("image-modal-body");

    var image = document.createElement("img");

    image.src = path;

    var closeButton = document.createElement("button");
    closeButton.classList.add("close-button");

    var icon = document.createElement("i");
    icon.classList.add("far", "fa-times-circle");
    closeButton.appendChild(icon);

    modalBody.appendChild(closeButton);
    modalBody.appendChild(image);
    modal.appendChild(modalBody);

    var overlay = document.createElement("div");
    overlay.classList.add("overlay", "active");

    parent.appendChild(modal);
    parent.appendChild(overlay);

    closeButton.onclick = function () {
        modal.classList.remove("active");
        overlay.classList.remove("active");
    }

    overlay.onclick = function () {
        modal.classList.remove("active");
        overlay.classList.remove("active");
    }
}

function userStatus(room, user) {
    
    if(window.XMLHttpRequest){
        http = new XMLHttpRequest();
    }else{
        http = new ActiveXObject("Microsoft.XMLHTTP");
    }

    http.onreadystatechange = function(){
        if (this.readyState = 4 && this.status == 200){
            var response = this.responseText;
        } 
        
        return response;
        console.log(response);
    }

    
    http.open("POST","status.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send("r="+room+"&u="+user);
}
</script>