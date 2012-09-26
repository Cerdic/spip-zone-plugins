<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_vente()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
//		include_spip ('inc/association_comptabilite');
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