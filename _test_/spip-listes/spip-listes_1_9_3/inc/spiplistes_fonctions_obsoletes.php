<?php

// inc/spiplistes_fonctions_obsoletes.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/* les fonctions obsoletes de spiplistes
*/

if(!spiplistes_spip_est_inferieur_193()) { 
	function generer_url_courrier ($script='', $args="", $no_entities=false, $rel=false) {
		$action = get_spip_script();
		$id_courrier = _request('id_courrier');
		$action = parametre_url($action, 'page', 'courrier', '&') . "&id_courrier=$id_courrier";
		if (!$no_entities) {
			$action = quote_amp($action);
		}
		return ($rel ? '' : url_de_base()) . $action;
	}
}


?>