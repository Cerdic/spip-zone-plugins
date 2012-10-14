<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_vente() {
	if (!autoriser('gerer_ventes', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('titre_onglet_ventes', 'ventes');
		$id_vente = association_passeparam_id('vente');
		// info
		echo association_totauxinfos_intro('', 'vente', $id_vente);
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('ventes.gif', 'ressources_titre_mise_a_jour');
		echo recuperer_fond('prive/editer/editer_asso_ventes', array (
			'id_vente' => $id_vente
		));
		fin_page_association();
	}
}

?>