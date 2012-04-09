function init_agenda() {
	var delai = "400";
	var chrono;
	function cacheBulles() {
		jQuery(".bulle").hide().css("left", 0).css("top", 0);
	}
	function mouseOverBulle() {
	  clearTimeout(chrono);
	}
	function mouseOutBulle() {
	  chrono = setTimeout(cacheBulles, delai);
	}
  jQuery(".cAgenda .unjour").each(
    function(i, obj) {
    	mouseOverBulle();
    	var date = obj.id.substring(4);
      jQuery(obj).hover(function(e) {
      	mouseOverBulle();
      	cacheBulles();
      	var pos = jQuery(obj).offset();
      	var bulle = jQuery("#bulle"+date);
      	var left = pos.left;
      	var top = pos.top + jQuery(obj).outerHeight();
      	var w = jQuery(window).width();
      	var bw = bulle.outerWidth();
        bulle.css("left", left).css("top", top);
      	if (left + bw >= w)
      		bulle.css("left", w - bw);
      	bulle.show();
      },function(e) {
      	mouseOutBulle();
      });
    }
  );
  jQuery(".cAgenda .bulle").each(
    function(i, obj) {
      jQuery(obj).hover(mouseOverBulle, mouseOutBulle);
    }
  );

}

jQuery(document).ready(
  function() {
    init_agenda();
    onAjaxLoad(init_agenda);
  }
);
jQuery(document).unload(function() {
	jQuery(".cAgenda .unjour, .cAgenda .bulle").unbind('mouseenter mouseleave');
});

/*
function init_agenda() {
  jQuery("#agenda_prev").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/agenda/inc-agenda"), "agenda", init_agenda);
        return false;
      }
      obj.href = unwrapUrl(obj.href, "composants/agenda/inc-agenda", pageUrl());
    }
  );
  jQuery("#agenda_next").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/agenda/inc-agenda"), "agenda", init_agenda);
        return false;
      }
      obj.href = unwrapUrl(obj.href, "composants/agenda/inc-agenda", pageUrl());
    }
  );
}
*/