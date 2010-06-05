// JavaScript Document
jQuery.fn.async_upload = function(add_function) {
  return this.ajaxForm({
    beforeSubmit:async_upload_before_submit,
    success:add_function,
    iframe:true
  }).ddUpload(add_function);
}

// Safari plante quand on utilise clone() -> on utilise html()
// Mais FF a un bug sur les urls contenant ~ quand on utilise html() -> on utilise clone()
jQuery.fn.clone2 = jQuery.browser.mozilla ? jQuery.fn.clone : jQuery.fn.html;

var iframeHandler = function(data,jForm,success) {
        //remove the previous message
        jQuery("div.upload_message",$(jForm).parent()).remove();
        var res = jQuery(data).filter(".upload_answer");
        //possible classes 
        //upload_document_added
        if(res.is(".upload_document_added")) {
          return res;
        }
        //upload_error
        if(res.is(".upload_error")) {
          var msg = jQuery("<div class='upload_message'>")
          .append(res.html())
          jForm.after(msg[0]);
          return false;
        } 
        //upload_zip_list
        if(res.is(".upload_zip_list")) {
          var zip_form = jQuery("<div class='upload_message'>").append(res.html());
          zip_form
          .find("form")
            .async_upload(function(res,s){
              success(res,s,jForm);
            });
          jForm.after(zip_form[0]);
          return false;  
        }
};
    

function async_upload_before_submit(data,form) {
   form.before(jQuery("<div class='upload_message' style='height:1%'>").append(ajax_image_searching)[0]);
   //if not present add the iframe input
   if(!form.find("input[name=iframe]").length)
    form.append("<input type='hidden' name='iframe' value='iframe'>");
   //reset the redirect input
   form
   .find("input[name='redirect']")
   .val("");
};

function async_upload_article_edit(res,s,jForm){
      res = iframeHandler(res,jForm,async_upload_article_edit);
      if(!res) return true;
      var cont;
      //verify if a new document or a customized vignette
      var bloc = jQuery(res.find(">div:first[id^=document]"));
			if(jQuery("#"+bloc.attr('id')).size()) {
				cont = jQuery("#"+bloc.attr('id')).html(bloc.html());
			} else {
	      //add a class to new documents
	      res.
	      children("div[class]")
	      .addClass("documents_added")
	      .css("display","none");
	      if (jForm.find("input[name='arg']").val().search("/0/image")!=-1){
	        cont = jQuery("#liste_images");
	        // cas de l'interface document unifiee
	        if (!cont.length)
		        cont = jQuery("#liste_documents");
	      }
	      else
	        cont = jQuery("#liste_documents");
	      cont
	      .prepend(res.clone2());
	      //find added documents, remove label and show them nicely
	      cont = cont.
	      find("div.documents_added")
	        .removeClass("documents_added")
	        .show("slow",function(){
	            var anim = jQuery(this).css("height","");
	            //bug explorer-opera-safari
	            if(!jQuery.browser.mozilla)
	              anim.css('width', jQuery(this).width()-2);
	            a = jQuery(anim).find("img[onclick]")
	            if (a.length) a.get(0).onclick();
	        })
	        .css('overflow','');
	    }
			jQuery("form.form_upload",cont).async_upload(async_upload_article_edit);
      verifForm(cont);
      return true;
}

function async_upload_icon(res,s,jForm) {
  res = iframeHandler(res,jForm);
  if(!res) return true;
  res.children("div").each(function(){
    var cont = jQuery("#"+this.id);
    verifForm(cont.html(jQuery(this).html()));
    jQuery("form.form_upload_icon",cont).async_upload(async_upload_icon);
		cont.find("img[onclick]").each(function(){this.onclick();});
  });
  return true;                     
}

function async_upload_portfolio_documents(res,s,jForm){
  res = iframeHandler(res,jForm,async_upload_portfolio_documents);

  if(!res) return true;

  // on dirait que ca passe mieux sur Safari avec un setTimeout cf #1408
  setTimeout(function() {
  res.children("div").each(function(){
    // this.id = documenter--id_article ou documenter-id_article
    var cont = jQuery(this.id?"#"+this.id:[]);
    var self = jQuery(this);
    if(!cont.size()) {
      cont = jQuery(this.id.search(/--/)!=-1 ? "#portfolio":"#documents")
      .append(self.clone2());
    }
    verifForm(cont.html(self.html()));
    jQuery("form.form_upload",cont).async_upload(async_upload_portfolio_documents);
  });
  }, 50);
  return true;             
}

jQuery.fn.ddUpload = function(success) {
  var xhr = new XMLHttpRequest();
  if(!xhr.upload)
    return this;

  var progress = $("<div class='ddUploadprogress'><div class='ddUploadprogress_bar'></div><span class='ddUploadprogress_text'></span></div>");
  progress.css({textAlign:"center",height:"15px"});
  progress.find("div.ddUploadprogress_bar").css({backgroundColor:"#FF0000",height:"15px",position:"absolute",zIndex:"0"});
  progress.find("span.ddUploadprogress_text").css({position:"relative",zIndex:"1"});
  
  return this.each(function(){
    var self = $(this);
    var drop = function(e) {
    	try {
    		self.removeClass("ddUploadover");
    		ddArea.hide();
    		var files = e.originalEvent.dataTransfer.files;
    		if(!files) {
            return true;
    		}
    		
    		$.each(files,function(i,file){
    			var upload = function(data) {
    				var xhr = new XMLHttpRequest();  
        		var fileProgress = progress.clone().insertAfter(self.find(":file:not(.ddAreaInput)"));
        		var width = fileProgress.width();
        		var progress_bar = fileProgress.find("div.ddUploadprogress_bar");
        		var progress_text = fileProgress.find("span.ddUploadprogress_text");
    				xhr.upload.onprogress = function(e) {  
    					if (e.lengthComputable) {  
    					 var percentage = e.loaded / e.total;  
    					 progress_bar.width(Math.round(width*percentage));
    					 progress_text.text(Math.round(percentage*100)+"%"); 
    					}  
    				};  
    				
            var onload = function(e){
    					progress_bar.width(width);
    					progress_text.text("100%");
    				};
             
    				xhr.upload.onload = onload;
            xhr.onreadystatechange = function (aEvt) {  
             if (xhr.readyState == 4) {  
                if(xhr.status == 200) { 
                 onload();
                 success(xhr.responseText,xhr.status,self);
                }  
             }  
            };            
            //if not present add the iframe input
            if(!self.find("input[name=iframe]").length)
              self.append("<input type='hidden' name='iframe' value='iframe'>");
            //reset the redirect input
            self
            .find("input[name='redirect']")
            .val("");
            var submit = self.find(":submit:eq(0)");
    				var url = self.attr("action")+"&"+self.formSerialize()+"&"+submit.attr("name")+"="+submit.val();
    				xhr.open("POST", url);  
            xhr.setRequestHeader("If-Modified-Since", "Mon, 26 Jul 1997 05:00:00 GMT");
            xhr.setRequestHeader("Cache-Control", "no-cache");
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.setRequestHeader("X-File-Name", file.fileName);
            xhr.setRequestHeader("X-File-Size", file.fileSize);
            xhr.setRequestHeader("Content-Type", "multipart/form-data");
    				//xhr.sendAsBinary(data);
    				xhr.send(data);
    			}  						
    
    			if(window.FileReader) {
    				var reader = new FileReader();
    				reader.onload = function(e) { upload(e.target.result); };
    				reader.readAsDataURL(file); 
    			} else {
    				upload(file);
    			}
    									
    		});
    		
    		return false;
    	} catch (er) {
    		if(console)
    			console.log(er);
    		return false;
    	}
    
    }

    //build d&d area
    var ddArea = $("<div class='ddArea'><input class='ddAreaInput' type='file' style='width:100%;height:100%;opacity:0' /></div>").css({height:"50px",position:'relative',backgroundColor:"#A7DFB4"});
    $("<div>Drag &amp; drop files here</div>").css({position:'absolute',top:'18px',left:0,right:0,fontSize:'14px',textAlign:'center'}).prependTo(ddArea);
    ddArea.hide().insertBefore(self.find(":file:not(.ddAreaInput)"));
    //binds dd events for ff and chrome
    
    $(window).
    bind("dragenter",function(e){
      self.addClass("ddUploadover");
      ddArea.show();
      return false;
    }).
    bind("dragleave",function(e){
      if(!e.screenX && !e.screenY) {
        self.removeClass("ddUploadover");
        ddArea.hide();
      };
      return false;
    }).
    bind("dragover",function(e){
      if($(e.target).is(".ddArea") || $(e.target).closest(".ddArea").length) { 
        if(!!($.browser.safari || $.browser.msie || $.browser.opera)==false) {
          e.originalEvent.dataTransfer.dropEffect = undefined;
          return false;
        } else {
          return true;
        }
      } else {
        e.originalEvent.dataTransfer.dropEffect = 'none';
      }
      return false; //to allow dropEffect
    });
    ddArea.
    bind("drop",drop).
    //binds dd events for safari
    find(":file").change(function(e){
      drop({originalEvent:{dataTransfer:{files:e.target.files}}});
    }); 
    
  });
  
}
