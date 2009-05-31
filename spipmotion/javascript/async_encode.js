// JavaScript Document
jQuery.async_encode_count = 0;
jQuery.fn.async_encode = function(add_function) {
	return this.submit(function(){
		return do_async_encode(this);
	});
	function do_async_encode(form) {
		jQuery.async_encode_count++;
		var num = jQuery.async_encode_count;
		var jForm = jQuery(form);
		var par = jQuery(jForm).parent();
		jQuery("div.encode_message",par)
		.remove();
		jForm.css("opacity",1);

		if(!form.async_encode_init) {
			form.async_encode_init = true
			jForm
			.append("<input type='hidden' name='iframe' value='iframe'>")
			.find("input[@name='redirect']")
			.val("")
			.end();
		}

		jForm.attr("target","encode_frame"+num);
    	var jFrame = jQuery("<iframe id='encode_frame"+num+"' name='encode_frame"+num+"' frameborder='0' marginwidth='0' marginheight='0' scrolling='no' style='position:absolute;width:1px;height:1px;' onload='this.iframeload("+num+")'></iframe>")
      	.appendTo("body")
      	
		//IE apparently do not write anything in an iframe onload event handler 
		jFrame[0].iframeload = function(num) {
		
		//remove the previous message
		jQuery("div.encode_message",par).remove();
		jForm.css("opacity",1);
		
        var res = jQuery(".upload_answer_video",this.contentDocument || document.frames(this.name).document.body);
		//possible classes
		//encode_document_added
		if(res.is(".upload_document_video_added")) {
			return add_function(res,jForm);
		}
		};

		jForm.before(jQuery("<div class='encode_message' style='height:1%'>").append(ajax_image_searching)[0]);
		jForm.css("opacity",0.5);
		return true;
	}
}

function async_encode_article_edit(res,jForm){
      var cont;
      //verify if a new document or a customized vignette
      var anchor = jQuery(res.find(">a:first[@id^=video]"));
			if(jQuery("#"+anchor.attr('id')).size()) {
				cont = jQuery("#"+anchor.attr('id')).next().next().html(anchor.next().next().html());
			} else {
	      if (jForm.find("input[@name='arg']").val().search("/0/videos")!=-1){
	        cont = jQuery("#joindre_video");}
			cont.find(">div.joindre").fadeOut("slow");
	        cont.html('').append(res.clone2());
	    }
			jQuery("form.form_encode",cont).async_upload(async_encode_article_edit);
      verifForm(cont);
      return true;
}