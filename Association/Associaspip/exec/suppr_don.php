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

function exec_suppr_don()
{
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		list($id_don, $don) = association_passeparam_id('don', 'asso_dons');
		onglets_association('titre_onglet_dons', 'dons');
		// info
		$infos['entete_date'] = association_formater_date($don['date_don'], '');
		$infos['entete_nom'] = association_formater_idnom($don['id_auteur'], $don['bienfaiteur'], 'membre');
		$infos['argent'] = association_formater_prix($don['argent'], 'donation cash');
		$infos['colis'] = ($don['valeur'] ? '('.association_formater_prix($don['valeur'], 'donation estimated').')<div class="n">' : '') .$don['colis'] .($don['valeur']?'</div>':'');
		$infos['contrepartie'] = $don['contrepartie'];
		$infos['entete_commentaire'] = $don['commentaire'];
		echo '<div class="hproduct">'. association_totauxinfos_intro('', 'don', $id_don, $infos ) .'</div>';
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('dons-24.gif', 'action_sur_les_dons');
		echo association_bloc_suppression('don', $id_don);
		fin_page_association();
	}
}

?>