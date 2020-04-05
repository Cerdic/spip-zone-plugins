function initVideoPlayers() {
	jQuery(".html5video").each(function(pid, player){
		var yesWeCan = false;
		var sources = jQuery("source", jQuery(player));
		for (var i=0;i<sources.length;i++) {
			var type = sources[i].type;
			if (player.canPlayType(type)) {
				yesWeCan = true;
				break;
			}
		}
    if (!yesWeCan)
      jQuery(player).replaceWith(jQuery("object", jQuery(player)));
  });
}
jQuery(document).ready(
  function() {
  	initVideoPlayers();
    onAjaxLoad(initVideoPlayers);
  }
);
