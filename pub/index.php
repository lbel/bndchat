<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BND Chat</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bndchat.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bndchat.js.php"></script>
    <script src="js/util.js"></script>
  </head>
  <body>
    <div class="container">
      <div id="main">
        <div id="topic_container"><span class="topic_heading">Topic</span><span id="topic"></span></div>

        <div class="chat_wrapper">
          <div id="user_list_container"><div class="users_header">Users</div><ul id="users"></ul></div>
           <div id="chat_container"></div>
          <div id="message_container">
           <textarea class="form-control" name="message" id="message"></textarea>
          </div>
         </div>
        </div>
    </div>
  </body>
</html>
