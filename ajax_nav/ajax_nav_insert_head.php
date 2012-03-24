<?php
function ajax_nav_insert_head($flux) {

  $res = sql_select('valeur', 'spip_meta', 'nom="ajax_nav_config"');
  $options = array();
  if (sql_count($res) == 1) {
    $options = sql_fetch($res);
    $options = unserialize($options['valeur']);

    /* evite les problemes lors de mises a jour du plugin */
    if ( ! $options['autoReplaceDivs'] ) {
      $options['autoReplaceDivs'] = 'on';
    }
  }

  function prepare($option) {
    $option = preg_replace("/[^a-zA-Z0-9\-\_]+/", "', ", $option);
    $option = preg_replace("/([a-zA-Z0-9\-\_]+)/", "'$1", $option);
    $option = preg_replace("/([^'])$/", "$1'", $option);
    return $option;
  }

  $ajaxNavFile = ($options['html4Fallback'] == 'on') ?
    find_in_path('ajax_nav.js') : '';

  $historyLibFile = ($options['html4Fallback'] == 'on') ?
    find_in_path('lib/balupton-history.js/scripts/bundled/html4+html5/jquery.history.js') : '';

  if ($options['useModernLib'] == "on") {
    $flux .= "<script type='text/javascript' src='" . find_in_path("lib/modernizr.js") . "'></script>";
  }

  $auto_replace_divs = ($options["autoReplaceDivs"] == "on") ? 'true' : 'false';

  $flux .= "<script type='text/javascript'>
Modernizr.load([";

  if ($options['useHistoryLib'] == "on") {
    $flux .= "    {
    	test : Modernizr.history,
        yep : ['" . find_in_path("lib/balupton-history.js/scripts/bundled/html5/jquery.history.js") . "'],
	nope : ['" . $historyLibFile . "']
    },";
  }

  $flux .= "
    {
	test : Modernizr.history,
	yep : ['" . find_in_path("ajax_nav.js") . "'],
	nope: ['" . $ajaxNavFile . "'],
	complete : function () {
	    if (typeof AjaxNav !== 'undefined') {
		AjaxNav.options = {
		    pagesToAjaxify: ["	. prepare($options["pagesToAjaxify"]) . "],
		    ajaxDivs: ["	. prepare($options["ajaxDivs"]) . "],
		    localizedDivs: ["	. prepare($options["localizedDivs"]) . "],
                    autoReplaceDivs: "  . $auto_replace_divs . "
		};
		AjaxNav();
	    }
	}
    }
]);
</script>
";

  return $flux;
}
?>