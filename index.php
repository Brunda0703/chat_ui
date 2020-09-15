<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

<html>

<head>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet" />

  <script>
    var server = "http://localhost:3000";
    var io = io(server);
    var myName = "";
    var otherPersonName = "";

    function enterName() {
      myName = document.getElementById("name").value;
      document.getElementById("name").disabled = true;
      io.emit("user_connected", myName);
      // onPageLoad(true);
      alert("You are connected");
      return false
    }

    function sendMessage() {
      var message = document.getElementById("message").value;
      document.getElementById("message").value = "";
      io.emit("send_message", {
        "sender": myName,
        "receiver": otherPersonName,
        "message": message
      });

      var html = "";
      html += '<div class="outgoing_msg">';
      html += '<div class="sent_msg">';
      html += '<p> ' + message + '</p>';
      html += '</div>';
      html += '</div>';
      document.getElementById("messages").innerHTML += html;

      return false;
    }

    io.on("message_received", function(data) {

      if (otherPersonName == data.sender) {
        var html = "";
        html += '<div class="incoming_msg">';
        // html += '<div class="incoming_msg_img"> <img style="border-radius: 50%;background-color: black;margin-top: 9px;" src="https://s3.amazonaws.com/spoonflower/public/design_thumbnails/0381/7526/rwhite_hashtag_on_black_shop_thumb.png" alt="sunil"> </div>';
        html += '<div class="received_msg">';
        html += '<div class="received_withd_msg">';
        html += '<p>' + data.message + '</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        document.getElementById("messages").innerHTML += html;
        document.getElementById("form-send-message").style.display = "";
        document.getElementById("messages").style.display = "";
        otherPersonName = data.sender;
      } else {

        let cuser = document.getElementById("users");
        let changeuser = cuser.getElementsByTagName('div');

        for (let i = 0; i < changeuser.length; i++) {

          if (changeuser[i].getAttribute("data-username") == data.sender) {

            changeuser[i].setAttribute("style", "background-color: #3c6b50");
          }
        }

        console.log("User array");
      }
    });

    function onUserSelected(self) {
      document.getElementById("form-send-message").style.display = "";
      document.getElementById("messages").style.display = "";
      document.getElementById("messages").innerHTML = "";
      otherPersonName = self.getAttribute("data-username");

      $.ajax({
        url: server + "/get_messages",
        method: "POST",
        data: {
          "sender": myName,
          "receiver": otherPersonName
        },
        success: function(response) {
          // console.log(response);
          var messages = JSON.parse(response);
          var html = "";
          console.log("Selected");

          let ancestor = document.getElementById("users");
          let selectuser = ancestor.getElementsByTagName('div');

          for (let i = 0; i < selectuser.length; i++) {

            if (selectuser[i].getAttribute("data-username") == otherPersonName) {

              selectuser[i].setAttribute("style", "background-color: #44515f");
            } else if (selectuser[i].getAttribute("style")) {
              let a = selectuser[i].getAttribute("style").split(':');
              if (!a[1].includes("#3c6b50"))
                selectuser[i].setAttribute("style", "");
              else
                selectuser[i].setAttribute("style", "background-color: #3c6b50");
            }
          }

          for (var a = 0; a < messages.length; a++) {

            if (messages[a].sender == myName) {
              html += '<div class="outgoing_msg">';
              html += '<div class="sent_msg">';
              html += '<p>' + messages[a].message + '</p>';
              html += '</div>';
              html += '</div>';
            } else {
              html += '<div class="incoming_msg">';
              // html += '<div class="incoming_msg_img"> <img style="border-radius: 50%;background-color: black;margin-top: 9px;" src="https://s3.amazonaws.com/spoonflower/public/design_thumbnails/0381/7526/rwhite_hashtag_on_black_shop_thumb.png" alt="sunil"> </div>';
              html += '<div class="received_msg">';
              html += '<div class="received_withd_msg">';
              html += '<p>' + messages[a].message + '</p>';
              html += '</div>';
              html += '</div>';
              html += '</div>';
            }

          }
          document.getElementById("messages").innerHTML = html;
        }
      });
    }

    io.on("user_connected", function(username) {

      var html = "";
      html += '<div class="chat_list" data-username="' + username;

      if (myName == username) {

        html += '">';
      } else html += '" onclick="onUserSelected(this);">';

      html += '<div class="chat_people">';
      html += '<div class="chat_img"> <img style="border-radius: 50%;background-color: black;" src="https://s3.amazonaws.com/spoonflower/public/design_thumbnails/0381/7526/rwhite_hashtag_on_black_shop_thumb.png" alt="sunil"> </div>';
      html += '<div class="chat_ib">';
      html += '<h5>' + username;

      if (myName == username)
        html += ' (me) </h5>';
      else
        html += '</h5>';

      html += '</div>';
      html += '</div>';
      html += '</div>';
      document.getElementById("users").innerHTML += html;
    });
  </script>

</head>

<body>
  <div class="container">
    <h3 class="text-center" style="font-size: xx-large;margin: 2rem 0 ;" >HASHTAG CHAT BOX</h3>
    <div class="messaging">
      <div class="inbox_msg">
        <div class="inbox_people">
          <div class="headind_srch">
            <div class="recent_heading">
              <h4>Users</h4>
            </div>
            <div class="srch_bar">
              <div class="stylish-input-group">


                <form onsubmit="return enterName();">
                  <input id="name" type="text" class="search-bar" placeholder="Enter name">
                  <span class="input-group-addon">
                    <button type="submit"> <i style="color: white" class="fa fa-plus" aria-hidden="true"></i> </button>
                  </span>
                </form>


              </div>
            </div>
          </div>
          <div class="inbox_chat" id="users">

          </div>
        </div>
        <div class="mesgs">
          <div class="msg_history" id="messages">

          </div>
          <div class="type_msg">
            <div class="input_msg_write">

              <form onsubmit="return sendMessage();" style="display: none;" id="form-send-message">
                <input id="message" type="text" class="write_msg" placeholder="Type a message" />
                <button class="msg_send_btn" type="submit"><i style="color: white" class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
              </form>

            </div>
          </div>
        </div>
      </div>


      <!-- <p class="text-center top_spac"> Design by <a target="_blank" href="#">Sunil Rajput</a></p> -->

    </div>
  </div>

  <style>

    body {
      overflow-x: hidden;
      overflow-y: hidden;
    }

    .container {
      max-width: 1170px;
      margin: auto;
      margin-top: 50px;
    }

    img {
      max-width: 100%;
    }

    .inbox_people {
      background: #212529 none repeat scroll 0 0;
      float: left;
      overflow: hidden;
      width: 30%;

    }

    .inbox_msg {
      border: 1px solid #c4c4c4;
      -webkit-box-shadow: 10px 10px 5px 0px rgba(161, 161, 161, 1);
      -moz-box-shadow: 10px 10px 5px 0px rgba(161, 161, 161, 1);
      box-shadow: 10px 10px 5px 0px rgba(161, 161, 161, 1);
      clear: both;
      overflow: hidden;
    }

    .top_spac {
      margin: 20px 0 0;
    }


    .recent_heading {
      margin-top: 1rem;
      float: left;
      width: 40%;
    }

    .srch_bar {
      margin-top: 1rem;
      display: inline-block;
      text-align: right;
      width: 60%;
    }

    .headind_srch {
      padding: 10px 29px 10px 20px;
      overflow: hidden;
      border-bottom: 1px solid #736c6c;
    }

    .recent_heading h4 {
      color: white;
      font-size: 21px;
      margin: auto;
    }

    .srch_bar input {
      border: 1px solid #cdcdcd;
      color: white;
      border-width: 0 0 1px 0;
      width: 80%;
      padding: 2px 0 4px 6px;
      background: none;
    }

    .srch_bar .input-group-addon button {
      background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
      border: medium none;
      padding: 0;
      color: #707070;
      font-size: 18px;
    }

    .srch_bar .input-group-addon {
      margin: 0 0 0 -27px;
    }

    .chat_ib h5 {
      font-size: 15px;
      color: white;
      margin: 6px 0 8px 0;
    }

    .chat_ib h5 span {
      font-size: 13px;
      float: right;
    }

    .chat_ib p {
      font-size: 14px;
      color: #989898;
      margin: auto
    }

    .chat_img {
      float: left;
      width: 11%;
    }

    .chat_ib {
      float: left;
      padding: 0 0 0 15px;
      width: 88%;
    }

    .chat_people {
      overflow: hidden;
      clear: both;
    }

    .chat_list {
      border-bottom: 1px solid #736c6c;
      margin: 0;
      padding: 18px 16px 10px;
    }

    .inbox_chat {
      height: 550px;
      overflow-y: hidden;
    }

    .active_chat {
      background: #ebebeb;
    }

    .incoming_msg_img {
      display: inline-block;
      width: 5%;
    }

    .received_msg {
      display: inline-block;
      padding: 0 0 0 10px;
      vertical-align: top;
      width: 92%;
    }

    .received_withd_msg p {
      background: #4f5255 none repeat scroll 0 0;
      -webkit-box-shadow: 4px 7px 11px 0px rgba(161, 161, 161, 1);
      -moz-box-shadow: 4px 7px 11px 0px rgba(161, 161, 161, 1);
      box-shadow: 4px 7px 11px 0px rgba(161, 161, 161, 1);
      border-radius: 6px;
      color: white;
      font-size: 14px;
      margin: 10px 0;
      padding: 5px 10px 5px 12px;
      width: 100%;
    }

    .time_date {
      color: #747474;
      display: block;
      font-size: 12px;
      margin: 8px 0 0;
    }

    .received_withd_msg {
      width: 50%;
    }

    .mesgs {
      float: left;
      padding: 30px 15px 0 25px;
      width: 68%;
    }

    .sent_msg p {
      background: #05728f none repeat scroll 0 0;
      border-radius: 6px;
      font-size: 14px;
      margin: 10px 0;
      color: #fff;
      padding: 5px 10px 5px 12px;
      width: 100%;
      -webkit-box-shadow: -6px 4px 11px 0px rgba(161, 161, 161, 1);
      -moz-box-shadow: -6px 4px 11px 0px rgba(161, 161, 161, 1);
      box-shadow: -6px 4px 11px 0px rgba(161, 161, 161, 1);
    }

    .outgoing_msg {
      overflow: hidden;
      margin: 26px 0 26px;
    }

    .sent_msg {
      float: right;
      width: 46%;
    }

    .input_msg_write input {
      background: rgb(33 37 41) none repeat scroll 0 0;
      -webkit-box-shadow: 7px 10px 11px 0px rgba(161, 161, 161, 1);
      -moz-box-shadow: 7px 10px 11px 0px rgba(161, 161, 161, 1);
      box-shadow: 7px 10px 11px 0px rgba(161, 161, 161, 1);
      border-radius: 6px;
      border: medium none;
      color: white;
      font-size: 15px;
      min-height: 48px;
      width: 100%;
    }

    .type_msg {
      /* border-top: 1px solid #c4c4c4; */
      position: relative;
    }

    .msg_send_btn {
      background: #05728f none repeat scroll 0 0;
      border: medium none;
      border-radius: 50%;
      color: #fff;
      cursor: pointer;
      font-size: 17px;
      height: 33px;
      position: absolute;
      right: 11px;
      top: 9px;
      width: 33px;
    }

    .messaging {
      padding: 0 0 50px 0;
    }

    .msg_history {
      height: 516px;
      overflow-y: auto;
    }
  </style>

</body>

</html>