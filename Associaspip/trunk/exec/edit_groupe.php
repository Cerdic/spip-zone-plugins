<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_groupe() {
	if (!autoriser('editer_groupes', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_groupe = association_passeparam_id('groupe');
		if (!$id_groupe)
			$r = array(0, array());
		else  $r = association_controle_id('groupe', 'asso_groupes');
		if ($r) {
			include_spip ('association_modules');
			list($id_groupe, $groupe) = $r;
			echo association_navigation_onglets('gestion_groupes', 'adherents');
			if ($groupe) {
				$infos = sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe");
				$infos = array('entete_utilise' => _T('asso:nombre_fois', array('nombre'=> $infos)));
				echo association_tablinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
				$titre = 'titre_editer_groupe';
			} else  $titre = 'titre_creer_groupe';
			// datation et raccourcis
			echo association_navigation_raccourcis(array(
				array('tous_les_groupes', 'annonce.gif', array('groupes', "id=$id_groupe"), array('voir_groupes', 'association') ),
			) );
			debut_cadre_association('annonce.gif', $titre);
			echo recuperer_fond('prive/editer/editer_asso_groupes',
					array ('id' => $id_groupe));
			fin_page_association();
		}
	}
}

?>
