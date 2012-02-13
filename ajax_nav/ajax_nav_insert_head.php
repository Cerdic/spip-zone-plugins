<?php
function ajax_nav_insert_head($flux) {

  $options = unserialize($GLOBALS['meta']['ajax_nav_config']);

  function prepare($options) {
    $options = preg_replace("/[^a-zA-Z0-9\-\_]+/", "', ", $options);
    $options = preg_replace("/([a-zA-Z0-9\-\_]+)/", "'$1", $options);
    $options = preg_replace("/([^'])$/", "$1'", $options);
    return $options;
  }

  $ajaxNavFile = ($options['html4Fallback'] == 'on') ?
    find_in_path('ajax_nav.js') : '';

  $historyLibFile = ($options['html4Fallback'] == 'on') ?
    find_in_path('lib/balupton-history.js/scripts/bundled/html4+html5/jquery.history.js') : '';

  if ($options['useModernLib'] == "on") {
    $flux .= "<script type='text/javascript' src='" . find_in_path("lib/modernizr.js") . "'></script>";
  }

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
		    localizedDivs: ["	. prepare($options["localizedDivs"]) . "]
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