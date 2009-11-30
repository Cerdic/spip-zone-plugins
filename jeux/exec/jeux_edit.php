<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_edit(){
	include_spip('inc/utils');
	$id_jeu	 = _request('id_jeu');

	if (_request('valider')) {
		$id_jeu = jeux_ajouter_jeu($id_jeu,_request('contenu'),_request('titre_prive'),_request('type_resultat'));
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_voir', 'id_jeu='.$id_jeu, true));
	}

	$gros_titre = _request('nouveau')?_T('jeux:nouveau_jeu'):_T('jeux:modifier_jeu', array('id'=>$id_jeu));
	jeux_debut_page($gros_titre);
	
	jeux_compat_boite('debut_gauche');
	echo boite_infos_jeu($id_jeu);
	
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	echo gros_titre($gros_titre, '', false);
	
	echo debut_cadre_formulaire('', true);

	echo "<form method='post' name='jeux_edit'>\n";
//	debut_cadre_relief();
	include_spip('public/assembler');
	echo recuperer_fond('fonds/jeux_edit',array('id_jeu'=>$id_jeu));
//	fin_cadre_relief();
	echo "<p align='right'><input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' /></p>";
	echo '</form>';
	echo fin_cadre_formulaire(true);
	echo fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($id_jeu=false, $contenu='', $titre_prive='', $type_resultat='defaut'){
	include_spip('jeux_utils');
	$type_jeu = jeux_trouver_nom($contenu);
	$type_jeu = strlen($type_jeu)?$type_jeu:_T('jeux:jeu_vide');
	$titre_prive = strlen($titre_prive)?$titre_prive:_T('jeux:sans_titre_prive');
	$contenu = "<jeux>$contenu</jeux>";
	if (!$id_jeu) {

        $id_jeu = sql_insertq('spip_jeux', array('date' => 'NOW()', 'statut'=>'publie', 'type_jeu'=>$type_jeu, 'titre_prive'=>$titre_prive, 'contenu'=>$contenu, 'type_resultat'=>$type_resultat));
		
	} else {
        sql_updateq('spip_jeux', array('date' => 'NOW()', 'type_jeu'=>$type_jeu, 'titre_prive'=>$titre_prive, 'contenu'=>$contenu, 'type_resultat'=>$type_resultat), "id_jeu=$id_jeu");
        spip_log("Le jeu #$id_jeu a ete modifie par l'auteur #".$GLOBALS["auteur_session"]['id_auteur'],'jeux');
	}
	return $id_jeu;
}
	
?>
