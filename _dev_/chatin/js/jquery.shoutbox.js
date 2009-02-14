var count = 1;
        function prepare(response) {
          var string = '<div class="shoutbox-list" id="list-'+count+'">'
              + '<span class="shoutbox-list-time">'+response.time+'</span>'
              + '<span class="shoutbox-list-nick">'+response.nickname+':</span>'
              + '<span class="shoutbox-list-message">'+response.message+'</span>'
              +'</div>';
          return string;
        }
        
        function showResponse(response, status)  { 
          if(status == 'success') {
            $('#daddy-shoutbox-response').html('<img src="images/accept.png" />');
            $('#daddy-shoutbox-list').append(prepare(response));
            $('#list-'+count).fadeIn('slow');
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
          $('#daddy-shoutbox-response').html('<img src="images/loader.gif" />');
          //alert('Both fields contain values.'); 
        }
        
                // wait for the DOM to be loaded 
        $(document).ready(function() { 
            var options = { 
                //target:        '#myForm',
                //beforeSubmit:  showRequest,
                dataType:       'json',
                beforeSubmit:   validate,
                success:        showResponse
            }; 
            $('#daddy-shoutbox-form').ajaxForm(options);
            $.getJSON("daddy-shoutbox.php?action=view", function(json) {
              for(i=0; i < json.length; i++) {
                $('#daddy-shoutbox-list').append(prepare(json[i]));
                $('#list-'+count).fadeIn('slow');
              }
            })
        }); 