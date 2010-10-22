<?php
/**
 * Modification d'un depot (exec=depots_edit)
 * La creation ne se fait que par la page "depots_gerer" via le formulaire ajouter_depot
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_depots_edit_dist() {
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	exec_depots_edit_args(intval(_request('id_depot')));
}

function exec_depots_edit_args($id_depot) {
	global $spip_lang_left;

	// Initialisation des champs de base du depot
	if (!$depot = sql_fetsel("*", "spip_depots", "id_depot=$id_depot")) {
		include_spip('inc/minipres');
		echo minipres(_T('svp:message_nok_aucun_depot'));
		die();
	} 
	else {
		$titre = $depot['titre'];
		$descriptif = $depot['descriptif'];
		$type = $depot['type'];
		$url = $depot['url_paquets'];
		$nbr_paquets = $depot['nbr_paquets'];
		$nbr_plugins = $depot['nbr_plugins'];
		$maj = $depot['maj'];
	}

	// Initialisation de la page
	pipeline('exec_init',array('args'=>array('exec'=>'depots_edit', 'id_depot'=>$id_depot),'data'=>''));

	// Titre, partie, sous-partie (pour le menu)
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('svp:titre_head_modifier_depot', array('depot' => $nom)), 'naviguer', 'depots', $id_depot);

	// Intitule de la page
	// -- Aucun

	// Colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'depots_edit', 'id_depot'=>$id_depot),'data'=>''));

	// Colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'depots_edit', 'id_depot'=>$id_depot),'data'=>''));

	// Centre
	echo debut_droite('', true);

	// Dans le redirect on ne passe que la page et pas le parametre id qui sera ajoute par l'action
	$contexte = array(
		'id_depot' => $id_depot,
		'titre' => $titre,
		'icone_retour' => icone_inline(_T('icone_retour'), generer_url_ecrire('depots',"id_depot=$id_depot"), chemin('prive/themes/spip/images/depot-24.png'), 'rien.gif', $spip_lang_left),
		'redirect' => generer_url_ecrire('depots')
	);

	// -- -- On appelle la noisette de presentation
	echo recuperer_fond('prive/editer/depot', $contexte);
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'depots_edit', 'id_depot'=>$id_depot),'data'=>''));

	// Fin de la page
	echo fin_gauche(), fin_page();
}

?>