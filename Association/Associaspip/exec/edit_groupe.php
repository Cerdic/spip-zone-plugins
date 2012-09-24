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

function exec_edit_groupe()
{
	include_spip ('inc/navigation_modules');
	$id_groupe = association_passeparam_id('groupe');
	if (!autoriser('editer_groupes', 'association', $id_groupe)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('gestion_groupes', 'adherents');
		// INFO
		if ($id_groupe>100) {
			$groupe = sql_fetsel('*', 'spip_asso_groupes', "id_groupe=$id_groupe" );
			$infos['ordre_affichage_groupe'] = $groupe['affichage'];
			$infos['commentaires'] = $groupe['commentaires'];
			$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
			echo association_totauxinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
		}
		// datation et raccourcis
		raccourcis_association('groupes');
		debut_cadre_association('annonce.gif', ($id_groupe)?'titre_editer_groupe':'titre_creer_groupe');
		echo recuperer_fond('prive/editer/editer_asso_groupes', array (
			'id' => $id_groupe
		));
		fin_page_association();
	}
}

?>