<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/association_comptabilite');

function balise_EDITEUR_DESTINATIONS_dist ($p) {
      return calculer_balise_dynamique($p, 'EDITEUR_DESTINATIONS', array('id_compte'));
}

function balise_EDITEUR_DESTINATIONS_dyn($id_compte) {
	return association_editeur_destinations(association_liste_destinations_associees($id_compte));
}

?>
