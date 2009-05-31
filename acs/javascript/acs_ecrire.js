/**
 *              ACS
 *          (Plugin Spip)
 *     http://acs.geomaticien.org
 *
 * Copyright Daniel FAIVRE, 2007-2009
 * Copyleft: licence GPL - Cf. LICENCES.txt
 *
 * JS interface d'admin d'ACS - ACS admin GUI
 */

function detail() {
	imgp = $(".imgp_spip_params").attr("src");
	if (imgp) {
		plon = $(".imgon_spip_params").attr("src");
		if (imgp == plon)
			return "&detail=2";
	}
	return "";
}


function mode_source() {
	if ($("#mode_source").attr("name") == "srcon")
		return "&mode=source";
	return "";
}

$(document).ready(
	function() {
		acs_ecrire_init();
	}
);


function acs_ecrire_init() {
	if (detail() == "") {
		$(".pliable").each(
			function(i) {
				$(this).hide();
			}
		);
	}

	// Donne leur fonction onclick aux plieurs (générique)
	$(".acs_plieur").each(
		function(i, plieur) {
			var cap = plieur.name.substr(7); //classe à plier
			if ((typeof plieur.onclick) != "undefined") {
				plieur.clic = plieur.onclick.toString();
			}
			plieur.onclick = function(e) {
				e.preventDefault();
				imgp = $(".imgp_" + cap).attr("src");
				ploff = $(".imgoff_" + cap).attr("src");
				plon = $(".imgon_" + cap).attr("src");
				if (imgp == ploff)
					$(".imgp_" + cap).attr("src", plon)
				else
					$(".imgp_" + cap).attr("src", ploff)
				$("." + cap).each(
					function(i, cap) {
						$(cap).slideToggle("slow");
					}
				);
				if ((typeof plieur.clic) != "undefined") {
					eval(plieur.clic + ";onclick(e);");
				}
				return false;
			}
		}
	);

	// Retourne les infos sur la page avec le niveau de détail défini par le plieur 
	// get page infos with detail level setted by plieur
	$(".page_lien").each(
		function(i,link) {
			link.onclick = function(e) {
				AjaxSqueeze("?exec=acs_page_get_infos&pg=" + link.title + detail() + mode_source(), "page_infos");
				document.location.href = "#page_infos";
				return false;
			}
		}
	);

	// Mask select controls on start (they works even without javascript !)
	$(".ctlWidget").each(
		function(i,cw) {
			var selectid = "#select_" + cw.id;
			$(selectid).attr("style", "visibility: hidden;");
			if ($(selectid).get(0)) $(selectid).get(0).style.visibility = "hidden"; /* IE */
			if ($(selectid).val() != '') { 
				var dragid = "#" + $(selectid).val();
				$(this).append($(dragid));
			}
		}
	);

	// Drag/drop widgets
	var dragid;
	var dfx = 500;
	$(".widget").Draggable({
		helper: "clone",
		delay: 100,
		distance: 10,
		ghosting: true,
		opacity:	0.8,
		revert: true,
		fx: dfx,
		onStart: function(drag) { // store dragid
			dragid = "#" + $(drag).attr("id");
		},
		onStop: function() { // reset select on stop dragging from its ctlWidget
			if ($(dragid).parent().is(".ctlWidget")) {
				var dropid = "#select_" + $(dragid).parent().attr("id");
				$("#widgets").append($(dragid));
				$(dropid).val("");
				this.dragCfg.fx = dfx; // restore desired revert effect
			}
		}
	});

	$(".ctlWidget").Droppable({
		accept: "widget",
		tolerance: "touch",
		activeclass: "ctlWidget_droppable_active",
		hoverclass: "ctlWidget_droppable_over",
		onHover: function(drag) {
			var dropid = "#select_" + $(this).attr("id");
			var val = $(dropid).val();
			$(dropid).val(drag.id);
			if ($(dropid).val() == drag.id) {
				drag.dragCfg.fx = 0; // Avoid unwanted revert effects
			}
			else
				drag.dragCfg.fx = dfx;
			$(dropid).val(val);
		},
		onDrop: function(drag) {
			var dropid = "#select_" + $(this).attr("id");
			var oldval = $(dropid).val();
			$(dropid).val(drag.id);
			if ($(dropid).val() == drag.id) {
			if ($(dragid).parent().is(".ctlWidget")) {
				var olddropid = "#select_" + $(dragid).parent().attr("id");
				$(olddropid).val("");
			}						
				$(this).find(".widget").fx = dfx; // restore desired revert effect
				$("#widgets").append($(this).find(".widget"));
				$(this).find(".widget").remove();
				$(this).append(drag);
			}
			dragid = false;
		},
		fit: true
	});

	$("#mode_source").each(
	function(i, link) {
	  link.onclick = function(e) {
	    AjaxSqueeze("?exec=acs_page_get_infos&pg=" + link.title + detail() + "&mode=source", "page_infos");
	    document.location.href = "#page_infos";
	      return false;
	    }
	  }
	);

	$("#mode_schema").each(
	  function(i, link) {
	    link.onclick = function(e) {
	      AjaxSqueeze("?exec=acs_page_get_infos&pg=" + link.title + detail() + "&mode=schema", "page_infos");
	      document.location.href = "#page_infos";
	      return false;
	    }
	  }
	);
	
}

