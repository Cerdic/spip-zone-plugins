<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_edit(){
	include_spip('inc/utils');
	$id_jeu	 = _request('id_jeu');

	if (_request('valider')) {
		$id_jeu = jeux_ajouter_jeu($id_jeu,_request('contenu'),_request('titre_prive'),_request('enregistrer_resultat'),_request('resultat_unique'));
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_voir', 'id_jeu='.$id_jeu, true));
	}

	if (_request('nouveau'))
		jeux_debut_page(_T('jeux:nouveau_jeu'));
	 else
		jeux_debut_page(_T('jeux:modifier_jeu', array('id'=>$id_jeu)));
	
	jeux_compat_boite('debut_gauche');
	echo debut_boite_info(true);
	
	if ($id_jeu)
		echo icone_horizontale(_T('jeux:retourner_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png'),'',false);
	echo icone_horizontale(_T('jeux:liste_jeux'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'),'',false),
		fin_boite_info(true);
	
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	$nouveau ? gros_titre(_T('jeux:nouveau_jeu'), '', false) : gros_titre(_T('jeux:modifier_jeu',array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	
	if(defined('_SPIP19100')) debut_cadre_formulaire(); else echo debut_cadre_formulaire('', true);

	echo "<form method='post' name='jeux_edit'>\n";
	debut_cadre_relief();
	include_spip('public/assembler');
	echo recuperer_fond('fonds/jeux_edit',array('id_jeu'=>$id_jeu));
	fin_cadre_relief();
	echo "<p align='right'><input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' /></p>";
	echo '</form>';
	if(defined('_SPIP19100')) fin_cadre_formulaire(); else echo fin_cadre_formulaire(true);
	echo fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($id_jeu=false, $contenu='', $titre_prive='', $enregistrer_resultat='oui', $resultat_unique='non'){
	include_spip('jeux_utils');
	$type_jeu = jeux_trouver_nom($contenu);
	$type_jeu = strlen($type_jeu)?$type_jeu:_T('jeux:jeu_vide');
	$titre_prive = strlen($titre_prive)?$titre_prive:_T('jeux:sans_titre_prive');
	$contenu = "<jeux>$contenu</jeux>";
	if (!$id_jeu) {
		if(function_exists('sql_insertq'))
			return sql_insertq('spip_jeux', array('date' => 'NOW()', 'statut'=>$statut, 'type_jeu'=>$type_jeu, 'titre_prive'=>$titre_prive, 'contenu'=>$contenu, 'enregistrer_resultat'=>$enregistrer_resultat, 'resultat_unique'=>$resultat_unique));
		else {
			spip_query("INSERT into spip_jeux (statut,type_jeu,titre_prive,contenu,enregistrer_resultat,resultat_unique) VALUES('publie',"._q($type_jeu).","._q($titre_prive).","._q($contenu).",'$enregistrer_resultat','$resultat_unique')");	
			return mysql_insert_id();
		}
	} else {
		if(function_exists('sql_replace'))
			sql_replace('spip_jeux', array('id_jeu'=>$id_jeu, 'statut'=>$statut, 'type_jeu'=>$type_jeu, 'titre_prive'=>$titre_prive, 'contenu'=>$contenu, 'enregistrer_resultat'=>$enregistrer_resultat, 'resultat_unique'=>$resultat_unique));
		else
			spip_query("REPLACE into spip_jeux (id_jeu,statut,type_jeu,titre_prive,contenu,enregistrer_resultat,resultat_unique) VALUES ($id_jeu,'publie',"._q($type_jeu).","._q($titre_prive).","._q($contenu).",'$enregistrer_resultat','$resultat_unique')");
	}
	return $id_jeu;
}
	
?>
