<?php
function ajax_nav_insert_head($flux) {

  if ($res = sql_select('valeur', 'spip_meta', 'nom="ajax_nav_config"')) {
    $options = sql_fetch($res);
    $options = unserialize($options['valeur']);
  }

  function prepare($options) {
    $options = preg_replace("/[^a-zA-Z0-9\-\_]+/", "', ", $options);
    $options = preg_replace("/([a-zA-Z0-9\-\_]+)/", "'$1", $options);
    $options = preg_replace("/([^'])$/", "$1'", $options);
    return $options;
  }

  $ajaxNavFile = ($options['html4Fallback'] == 'on') ?
    'plugins/ajax_nav/ajax_nav.js' : '';

  $historyLibFile = ($options['html4Fallback'] == 'on') ?
    'plugins/ajax_nav/lib/balupton-history.js/scripts/bundled/html4+html5/jquery.history.js' : '';

  if ($options['useModernLib'] == "on") {
    $flux .= "<script type='text/javascript' src='plugins/ajax_nav/lib/modernizr.js'></script>";
  }

  $flux .= "<script type='text/javascript'>
Modernizr.load([";

  if ($options['useHistoryLib'] == "on") {
    $flux .= "    {
    	test : Modernizr.history,
        yep : ['plugins/ajax_nav/lib/balupton-history.js/scripts/bundled/html5/jquery.history.js'],
	nope : ['" . $historyLibFile . "']
    },";
  }

  $flux .= "
    {
	test : Modernizr.history,
	yep : ['plugins/ajax_nav/ajax_nav.js'],
	nope: ['" . $ajaxNavFile . "'],
	complete : function () {
	    if (typeof AjaxNav !== 'undefined') {
		AjaxNav.options = {
		    urlPrefix: '"	. $options["urlPrefix"] . "',
		    pagesToAjaxify: ["	. prepare($options["pagesToAjaxify"]) . "],
		    ajaxDivs: ["	. prepare($options["ajaxDivs"]) . "],
		    localizedDivs: ["	. prepare($options["localizedDivs"]) . "],
		    siteURL: '"		. $GLOBALS['meta']['adresse_site'] . "/'
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