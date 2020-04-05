<?php
/**
 * Plugn SPIP-Projet
 * Licence GPL
 *
 * Affichage de la liste des projets associes a un auteur (exec=projets_page)
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_projets_page_dist()
{
	global $connect_statut, $connect_id_auteur;

	// Initialisation de la page
 	pipeline('exec_init', array('args'=>array('exec'=>'projets_page'), 'data'=>''));

	// Titre, partie, sous-partie (pour le menu)
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('projets:titre_page_projets_page'), 'naviguer', 'projets');

	// Intitule de la page
	echo"<br/><br/><br/>\n";//outch!aieaieaie!ausecours!
	echo gros_titre(_T('projets:titre_contenu_projets_page'), '', false);

	// Colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'projets_page'), 'data'=>''));

	// Colonne droite
	// -- Afficher le bloc de raccourcis (cree la colonne aussi: echo creer_colonne_droite('', true);)
	$bloc = NULL;
	if (autoriser('creer', 'projet')) {
		$bloc .= icone_horizontale(_T('projets:icone_ecrire_projet'), parametre_url(generer_url_ecrire('projet_edit','new=oui'),'redirect',self()), chemin('projets-24.gif','prive/images/'), 'creer.gif', false);
	}
	// Creer un pipeline pour ajouter des items dans le bloc des raccourcis
	if ($bloc)
		echo bloc_des_raccourcis($bloc);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'projets_page'), 'data'=>''));

	// Centre
	echo debut_droite('', true);
	// -- Contenu

	// -- -- On determine le contexte
	$contexte = array('id_auteur' => $connect_id_auteur);
	$contexte = array_merge($contexte, $_GET, $_POST);

	// -- -- On appelle la noisette de presentation
	echo recuperer_fond('prive/contenu/projets_page', $contexte);
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'projets_page'), 'data'=>''));

	// Fin de la page
	echo fin_gauche(), fin_page();
}

?>
