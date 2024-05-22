<html>
    <head>
        <style>
            input, button { padding: 10px; }
        </style>
    </head>
    <body>
        <input type="text" id="message" />
        <button onclick="transmitMessage()">Send</button>
        <div class="content"></div>
        <script>
            var socketId = "<?php echo $_GET['room'] ?? "default"; ?>";

            var socket  = new WebSocket('wss://socket.app-api.alexsofonea.com/');
            
            var message = document.getElementById('message');

            function transmitMessage() {
                socket.send(message.value);
            }

            socket.onmessage = function(e) {
                document.getElementsByClassName("content")[0].innerHTML += "<p>" + e.data + "</p>";
            }
            socket.onopen = function() {
                socket.send(socketId);
            }
        </script>
    </body>
</html>
