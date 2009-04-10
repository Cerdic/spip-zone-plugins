function init_playlist() {
  jQuery(".playlist .playliste li").each(function(i) {
    jQuery(this).hover(function(){
      jQuery(this).addClass("over");
    },function(){
      jQuery(this).removeClass("over");
    });
  });
/*
  jQuery(".playlist a.lien_pagination").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/playlist/inc-playlist"), "ajax_playlist", init_playlist);
        return true;
      }
    }
  );
*/
}

jQuery(document).ready(
  function() {
    init_playlist();
  }
);
