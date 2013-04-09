<?php
/**
 * Utilisations de pipelines par Factures &amp; devis
 *
 * @plugin     Factures &amp; devis
 * @copyright  2013
 * @author     Cyril Marion - Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Factures\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Afficher les factures d'une organisation
 *
 * Il peut y en avoir beaucoup, on le met après le contenu d'une organisation donc.
 **/
function factures_afficher_complement_objet($flux) {

	$type = $flux['args']['type'];

	// projets sur les organisations
	if ($type == 'organisation') {

		$id_organisation = $flux['args']['id'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creerfacturedans', 'organisation', $id_organisation)) {
			include_spip('inc/presentation');
			$bouton .= icone_verticale(_T("facture:icone_creer_facture"), generer_url_ecrire("facture_edit", "id_organisation=$id_organisation"), "facture-24.png", "new", "right")
				. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('factures', array('id_organisation'=>$id_organisation, 'par'=>'date_facture'));
		$flux['data'] .= $bouton;
	}

	return $flux;
}
?>