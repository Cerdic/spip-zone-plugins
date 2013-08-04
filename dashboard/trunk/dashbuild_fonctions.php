<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lister_dashboards($dashboard='') {
	static $dashboards = null;

	if (is_null($dashboards)) {
		$dashboards = pipeline('declarer_dashboard', $dashboards);
	}

	if ($dashboard)
		return isset($dashboards[$dashboard]) ? $dashboards[$dashboard] : array();
	else
		return $dashboards;

}


function informer_dashboard($dashboard, $type='') {
	$info = '';

	if ($dashboard) {
		$infos = lister_dashboards($dashboard);
		if ($type)
			$info = (isset($infos[$type]) ? $infos[$type] : '');
		else
			$info = $infos;
	}

	return $info;
}
?>