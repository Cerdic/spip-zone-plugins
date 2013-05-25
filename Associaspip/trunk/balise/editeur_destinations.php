<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/association_comptabilite');

function balise_EDITEUR_DESTINATIONS_dist ($p) {
	if ($GLOBALS['association_metas']['destinations']) // on recupere dans l'environement id_dest,montant_dest, et defaut_dest qui doivent donc etre assignees par la fonction charger du formulaire contenant la balise
		return calculer_balise_dynamique($p, 'EDITEUR_DESTINATIONS', array('id_dest', 'montant_dest', 'defaut_dest') );
	else
		return '';
}

function balise_EDITEUR_DESTINATIONS_dyn($id_dest, $montant_dest, $defaut_dest) {
	$destinations_id_montant = array();
	if (($id_dest) && ($montant_dest))
		foreach ($id_dest as $k => $v) {
			$destinations_id_montant[$v] = $montant_dest[$k];
		}
	return filtre_selecteur_compta_destinations($destinations_id_montant, $defaut_dest);
}

?>