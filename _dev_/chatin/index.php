<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>jQuery</title>
  <style type="text/css">
    #daddy-shoutbox {
      padding: 5px;
      background: #FFF;
      color: #000;
      width: 225px;
      font-family: arial,sans-serif,helvetica;
      font-size: 13px;
      border: 1px solid #CCCCCC;
    }
    .shoutbox-list {
      /* border-bottom: 1px solid #627C98; */
      padding: 5px;

      display: none;

    }
    #daddy-shoutbox-list {
      text-align: left;
      margin: 0px auto;
      max-height:250px;
      overflow: auto;
    }
    #daddy-shoutbox-form {
      text-align: left;
      
    }
    
    .shoutbox-list {
          padding: 2px ;
    }
    .shoutbox-list-time {
      color: #8DA2B4;
      display: none;
    }
    .shoutbox-list-nick {
      font-weight:bold;
      font-weight: bold;
    }
    .shoutbox-list-message {
      margin-left:0.5em;
      color: #000;
    }
    
    .entry {
        background-color:#FFFFFF;
        border-color:#666666 #CCCCCC #CCCCCC;
        border-style:solid;
        border-width:1px;
        color:#000000;
        font-size:80%;
        height:36px;
        margin:1px;
        outline-color:-moz-use-text-color;
        outline-style:none;
        outline-width:0;
        overflow-x:auto;
        overflow-y:hidden;
        padding:3px 0 3px 3px;
        position:relative;
        width:96%;
    }
    .entry_border {
        border: 2px solid #83AEF7;
       }
    
  </style>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jquery.form.js"></script>


  <script type="text/javascript">
  
  
    $(document).ready(function(){
           //reset scroll
            
          //on press enter
          $("#message").keypress(function (e){
              if (e.which == 13 && $("#message").val() != '')  { 
                 $('#daddy-shoutbox-form').submit();
                 $("#daddy-shoutbox-list").animate({scrollTop: $("#daddy-shoutbox-list")[0].scrollHeight }, 0);
                 $('#message').val('').focus();
                 return false;
              } else if (e.which == 13) {
               return false;
              }
          });
          

          
    });
    </script>
  
</head>
  <body>

  <center>
  <div id="daddy-shoutbox">
    <div id="daddy-shoutbox-list"></div>
    <br />
    <form id="daddy-shoutbox-form" action="jquery-shoutbox/shoutbox.php?action=add" method="post"> 
    
    <input type="hidden" name="nickname" value="tin" /> 

    <textarea id="message" ignoreesc="true" class="entry" name="message" onfocus="$(this).addClass('entry_border'); return false" onblur="$(this).removeClass('entry_border')"></textarea>
    <span id="daddy-shoutbox-response"></span>
    </form>
  </div>
  </center>
  
  <script type="text/javascript">
        var count = 0;
        var files = 'jquery-shoutbox/';
        var lastTime = 0;
        
        function prepare(response) {
          var d = new Date();
          count++;
          d.setTime(response.time*1000);
          var mytime = d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
          var string = '<div class="shoutbox-list" id="list-'+count+'">'
              + '<span class="shoutbox-list-time">'+mytime+'</span>'
              + '<span class="shoutbox-list-nick">'+response.nickname+':</span>'
              + '<span class="shoutbox-list-message">'+response.message+'</span>'
              +'</div>';

          return string;
        }
        
        function success(response, status)  { 
          if(status == 'success') {
            lastTime = response.time;
            $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
            $('#daddy-shoutbox-list').append(prepare(response));
            $('input[@name=message]').attr('value', '').focus();
            $('#list-'+count).fadeIn('slow');
            timeoutID = setTimeout(refresh, 3000);
          }
        }
        
        function validate(formData, jqForm, options) {
          for (var i=0; i < formData.length; i++) { 
              if (!formData[i].value) {
                  alert('Please fill in all the fields'); 
                  $('input[@name='+formData[i].name+']').css('background', 'red');
                  return false; 
              } 
          } 
          $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
          clearTimeout(timeoutID);
        }

        function refresh() {
          $.getJSON(files+"shoutbox.php?action=view&time="+lastTime, function(json) {
            if(json.length) {
              for(i=0; i < json.length; i++) {
                $('#daddy-shoutbox-list').append(prepare(json[i]));
                $('#list-' + count).fadeIn('slow');
              }
              var j = i-1;
              lastTime = json[j].time;
            }
            //alert(lastTime);
          });
          timeoutID = setTimeout(refresh, 3000);
          $("#daddy-shoutbox-list").animate({scrollTop: $("#daddy-shoutbox-list")[0].scrollHeight }, 0);            
        }
        
        // wait for the DOM to be loaded 
        $(document).ready(function() { 

            var options = { 
              dataType:       'json',
              beforeSubmit:   validate,
              success:        success
            }; 
            $('#daddy-shoutbox-form').ajaxForm(options);
            timeoutID = setTimeout(refresh, 100);
            

        });
  </script>
</body>
</html>
