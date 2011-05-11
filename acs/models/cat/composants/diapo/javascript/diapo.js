
function initDiapos() {
  jQuery("a.diapo").mediabox({
    href: function() {
    return "spip.php?page=c&c=diapo&p=mb&type=text/html&" + jQuery(this).attr("longdesc");
    },
    maxHeight: "90%",
    maxWidth: "90%",
    autoResize: true,
    onShow: function(){
      jQuery(this).colorbox.resize();
    }
  });
}
jQuery(document).ready(
  function() {
    initDiapos();
    onAjaxLoad(initDiapos);
  }
);
