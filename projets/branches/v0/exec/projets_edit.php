<?php
/**
 * Plugn SPIP-Projet
 * Licence GPL
 *
 * Creation et edition d'un projet (exec=projet_edit)
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_projet_edit_dist() {
	exec_projet_edit_args(
		intval(_request('id_projet')),
		intval(_request('id_parent')),
		_request('new'),
		_request('redirect'));
}

function exec_projet_edit_args($id_projet, $id_parent, $new,$redirect)
{
	global $connect_statut, $spip_lang_right;

	// Initialisation des champs de base du projet
	$titre = false;
	if ($new == "oui") {
		$id_projet = 0;
		$titre = filtrer_entites(_T('projets:info_nouveau_projet'));
		$id_parent = 0;
	}
	else {
		$row = sql_fetsel("*", "spip_projets", "id_projet=$id_projet");
		if ($row) {
			$id_parent = $row['id_parent'];
			$titre = $row['titre'];
			$id_statut = $row['id_statut'];
		}
	}
	// Traitement des cas d'erreurs
	if ($titre === false
		OR ($new!='oui' AND !autoriser('creer','projet',$id_projet)))  {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// Initialisation de la page
	pipeline('exec_init',array('args'=>array('exec'=>'projet_edit','id_projet'=>$id_projet),'data'=>''));

	// Titre, partie, sous-partie (pour le menu)
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('info_modifier_titre', array('titre' => $titre)), "naviguer", "projets", $id_projet);

	// Intitule de la page
	// -- Aucun

	// Colonne gauche
	echo debut_gauche('', true);
	// -- Pave "documents associes a la rubrique"
	if (!$new){
		# affichage sur le cote des pieces jointes, en reperant les inserees
		# note : traiter_modeles($texte, true) repere les doublons
		# aussi efficacement que propre(), mais beaucoup plus rapidement
 		traiter_modeles(join('',$row), true);
 		//echo afficher_documents_colonne($id_projet, 'projet');
	}
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'projet_edit','id_projet'=>$id_projet),'data'=>''));

	// Colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'projet_edit','id_projet'=>$id_projet),'data'=>''));

	// Centre
	echo debut_droite('', true);

	$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), $redirect ? $redirect:generer_url_ecrire("projets","id_projet=$id_projet"), find_in_path('prive/images/projets-24.gif'), "rien.gif",$GLOBALS['spip_lang_left']),
		'redirect'=>$redirect?$redirect:generer_url_ecrire("projets","id_projet=".$id_projet),
		'titre'=>$titre,
		'new'=>$new == "oui"?$new:$id_projet,
		'id_rubrique'=>$id_parent, // pour permettre la specialisation par la rubrique appelante
		'config_fonc'=>''
	);

	// -- -- On appelle la noisette de presentation
	echo recuperer_fond("prive/editer/projet", $contexte);
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'projet_edit','id_rubrique'=>$id_rubrique),'data'=>''));

	// Fin de la page
	echo fin_gauche(), fin_page();
}

?>