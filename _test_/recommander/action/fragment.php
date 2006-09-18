<?php

	// Charge le fragment demande (par ahah ou en inclusion)

	// Si le fragment a une definition specifique, celle-ci est prioritaire

	function action_fragment_dist() {

		// Une definition specifique du fragment est prioritaire
		if ($f = charger_fonction('fragment_'._request('fragment'),
		'action', true))
			return $f();

		// Sinon, definitions generiques... (a voir, donc)
	}

?>