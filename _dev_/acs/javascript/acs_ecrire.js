/**
 *              ACS
 *          (Plugin Spip)
 *     http://acs.geomaticien.org
 *
 * Copyright Daniel FAIVRE, 2007-2008
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
    // Hide help on start
    $(".pliable").each(
      function(i) {
        $(this).hide();
      }
    );

    // Donne leur fonction onclick aux plieurs (générique)
    $(".acs_plieur").each(
      function(i, plieur) {
        if (plieur.onclick != "undefined") {
          $(".imgon_" + plieur.name.substr(7)).attr("onclick", plieur.onclick);
        }
        plieur.onclick = function(e) {
          var cap = plieur.name.substr(7); //classe à plier
          imgp = $(".imgp_" + cap).attr("src");
          ploff = $(".imgoff_" + cap).attr("src");
          plon = $(".imgon_" + cap).attr("src");
          if (imgp == ploff)
            $(".imgp_" + cap).attr("src", plon)
          else
            $(".imgp_" + cap).attr("src", ploff)

          $("." + cap).each(
            function(i) {
              $(this).slideToggle("slow");
            }
          );
          if ($(".imgon_" + cap).attr("onclick") != "undefined") {
            $(".imgon_" + cap).trigger("onclick");
          }
          return false;
        }
      }
    );

    $(".spip_params").each(
      function(i) {
        $(this).hide();
      }
    );

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

    $(".param_boucle_spip").each(
      function(i) {
        $(this).hide();
      }
    );

    // Mask select controls on start (they works even without javascript !)
    $(".ctlWidget").each(
      function(i,cw) {
        selectid = "#select_" + cw.id;
        $(selectid).attr("style", "visibility: hidden;");
        if ($(selectid).get(0)) $(selectid).get(0).style.visibility = "hidden"; /* IE */
        var dragid = "#" + $(selectid).val();
        $(this).append($(dragid));
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
      opacity:  0.8,
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
  }
);
