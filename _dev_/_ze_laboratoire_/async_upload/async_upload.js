// JavaScript Document
jQuery.fn.async_upload = function(add_function) {
  return this.submit(function(){
    return do_async_upload(this);
  });
  
  var iframe;
  function do_async_upload(form) {
    var jForm = $(form);
    var par = $(jForm).parent();
    $("div.upload_message",par)
    .remove();
    if(!form.async_init) {
      form.async_init = true
      jForm
      .attr("target","upload_frame")
      .append("<input type='hidden' name='iframe' value='iframe'>")
      .find("input[@name='redirect']")
        .val("")
      .end();
    }
  
    if (!iframe) {
      iframe = $("<iframe id='upload_frame' name='upload_frame' frameborder='0' marginwidth='0' marginheight='0' scrolling='yes' style='position:absolute' onload='this.iframeload()'></iframe>")
      .appendTo("body");
    }
    
    //IE apparently do not write anything in an iframe onload event handler 
    iframe[0].iframeload = function() {
        //remove the previous message
        $("div.upload_message",par).remove();
        var res = $(".upload_answer",this.contentDocument || document.frames("upload_frame").document.body);
        //possible classes 
        //upload_document_added
        if(res.is(".upload_document_added")) {
          return add_function(res,jForm);
        }
        //upload_error
        if(res.is(".upload_error")) {
          var msg = $("<div class='upload_message'>")
          .append(res.html())
          jForm.after(msg[0]);
          return true;
        } 
        //upload_zip_list
        if(res.is(".upload_zip_list")) {
          var zip_form = $("<div class='upload_message'>").append(res.html());
          zip_form
          .find("form")
            .attr("target","upload_frame")
            .append("<input type='hidden' name='iframe' value='iframe'>")
          .end();
          jForm.after(zip_form[0]);
          return true;  
        }
    };
    
    jForm.before($("<div class='upload_message' style='height:1%'>").append(ajax_image_searching)[0]);
    return true;
  }
}
