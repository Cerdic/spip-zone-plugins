
function initDiapos() {
  jQuery("a.diapo").mediabox({
    href: function() {
      return "spip.php?page=c&c=diapo&p=mb&type=text/html&" + jQuery(this).attr("longdesc") + "&cache=7200,cache-client";
    },
    maxHeight: "90%",
    maxWidth: "90%",
    onShow: function(){
      switch(this.type) {
        case "application/x-shockwave-flash":
          jQuery(this).colorbox.resize();
          break;
        case "application/pdf":
          jQuery(this).colorbox.resize({height:"90%",width:"90%"});
      }
      var img = jQuery("img", "#cboxLoadedContent");
      if (img.length) {
      	var dw = jQuery("#colorbox").innerWidth() - jQuery("#colorbox").width();
      	var dh = jQuery("#colorbox").innerHeight() - jQuery("#colorbox").height();
      	img.css("max-height", img.height()-dh-10)
           .css("margin-top", "3px")
      	   .css("max-width", img.width()-dw);
      }
    }
  });
}
jQuery(document).ready(
  function() {
    initDiapos();
    onAjaxLoad(initDiapos);
  }
);
