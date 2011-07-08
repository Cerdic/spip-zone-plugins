
function initDiapos() {
  jQuery("a.diapo").not('.dhasbox').mediabox({
    href: function() {
      return "spip.php?page=c&c=diapo&p=mb&type=text/html&" + jQuery(this).attr("longdesc") + "&cache=7200,cache-client";
    },
    maxHeight: "90%",
    maxWidth: "90%",
    slideshow: true,
    slideshowAuto: false,
    traiter_toutes_images: "non",
    onShow: function() {
      switch(this.type) {
        case "application/x-shockwave-flash":
          jQuery(this).colorbox.resize();
          break;
        case "application/pdf":
          jQuery(this).colorbox.resize({height:"90%",width:"90%"});
      }
      var cbc = jQuery("#cboxLoadedContent");
      var img = jQuery("img", "#cboxLoadedContent");
      if (img.length) {
      	var w = cbc.innerWidth();
      	var h = cbc.innerHeight();
      	var titre = jQuery(".spip_doc_titre", cbc);
      	var ht = 0;
      	if (titre.length)
      		ht = titre.height();
      	var descr = jQuery(".spip_doc_descriptif", cbc);
      	var hd = 0;
      	if (descr.length)
      		hd = descr.height();
      	h = h - ht - hd;
      	img.css("max-height", h + "px")
      	   .css("max-width", w + "px");
      	cbc.css("overflow", "hidden");
      }
    }
  }).addClass('dhasbox');
}
jQuery(document).ready(
  function() {
    initDiapos();
    onAjaxLoad(initDiapos);
  }
);
