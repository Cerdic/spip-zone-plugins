<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_exercice() {
	sinon_interdire_acces(autoriser('gerer_compta', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_exercice = association_passeparam_id('exercice');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('exercices_budgetaires_titre', 'association');
/// AFFICHAGES_LATERAUX : INTRO : info exercice
	$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_comptes', ""), )); // bof, le nombre d'operations est deja indique sur la page de comptes pour l'exercice selectionne
	$infos['entete_duree'] = association_formater_duree(sql_getfetsel("TIMESTAMPDIFF(day,date_debut,date_fin) AS duree_jours", 'spip_asso_exercices', "id_exercice=$id_exercice"), 'D'); // voir note dans "/exec/exercices_comptable.php" au sujet de TIMESTAMPDIFF sachant que la simple diffrence "fin-debut" peut donner des resultats surprenants...
	echo association_tablinfos_intro(sql_getfetsel('intitule', 'spip_asso_exercices', "id_exercice=$id_exercice" ), 'exercice', $id_exercice, $infos);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('tous_les_exercices', 'grille-24.png', array('exercice_comptable', "id=$id_exercice"), array('gerer_compta', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_exercice', array (
		'id_exercice' => $id_exercice,
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>