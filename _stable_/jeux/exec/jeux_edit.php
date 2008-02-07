<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_edit(){
	include_spip('inc/utils');
	
	$valider = _request('valider');
	$nouveau = _request('nouveau');
	$id_jeu	 = _request('id_jeu');
	$type_jeu = _request('type_jeu');
	$titre_prive	 = _request('titre_prive');
	$contenu = _request('contenu');
	$enregistrer_resultat = _request('enregistrer_resultat');

	
	if ($valider) {
		$id_jeu = jeux_ajouter_jeu($titre_prive,$contenu,$enregistrer_resultat,$id_jeu);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_voir', 'id_jeu='.$id_jeu,true));
	}
	
	$nouveau ? jeux_debut_page(_T('jeux:nouveau_jeu')) : jeux_debut_page(_T('jeux:modifier_jeu',array('id'=>$id_jeu,'nom'=>$type_jeu)));
	
	if (!$nouveau){
	$requete = spip_fetch_array(spip_query("SELECT enregistrer_resultat,contenu,type_jeu,titre_prive FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$type_jeu = $requete['type_jeu'];
	$titre_prive = $requete['titre_prive']==_T('jeux:sans_titre_prive')?'':entites_html($requete['titre_prive']);
	$contenu = entites_html(str_replace(array("<jeux>","</jeux>"), "", $requete['contenu']));
	$enregistrer_resultat  = $requete['enregistrer_resultat'];
	}
	
	jeux_compat_boite('debut_gauche');
	echo debut_boite_info(true);
	
	if ($id_jeu)
		echo icone_horizontale(_T('jeux:retourner_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png'),'',false);
	echo icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'),'',false),
		fin_boite_info(true);

	
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	$nouveau ? gros_titre(_T('jeux:nouveau_jeu'), '', false) : gros_titre(_T('jeux:modifier_jeu',array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	
	if(defined('_SPIP19100'))debut_cadre_formulaire(); else echo debut_cadre_formulaire('', true);
	echo "<form method='post' name='jeux_edit'>\n";
	debut_cadre_relief();
	
	// titre prive
	echo "<label><span class='titrem'>"._T('jeux:jeu_titre_prive');
	echo "<br /></span><input type='text' name='titre_prive' value=\"$titre_prive\" style='width:100%;' />";
	echo '</label>';

	// contenu
	echo "<br /><br /><label><span class='titrem'>"._T('jeux:jeu_contenu');
	echo "<textarea  name='contenu' class='formo' rows='20' cols='40' style='width:100%;' >",
		strip_tags($contenu),
		'</textarea>';
	echo '</label>';
	
	// enregistrement des resultats
	echo "<br /><label><span class='titrem'>"._T('jeux:enregistrer_resultat');
	echo '<br /></span><select class="formo" name="enregistrer_resultat" ><option value="oui">'._T('item_oui').'</option>';
	echo '<option value="non"';
	if ($enregistrer_resultat=='item_non') { echo 'selected="selected"';}
	echo '>'._T('item_non').'</option></select>';
	echo '</label>';
	fin_cadre_relief();
	
	
	echo "<p align='right'><input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' /></p>";
	echo '</form>';
	if(defined('_SPIP19100'))fin_cadre_formulaire();else echo fin_cadre_formulaire(true);
	echo fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($titre_prive,$contenu, $enregistrer_resultat, $id_jeu=false){
	include_spip('jeux_utils');
	$type_jeu = jeux_trouver_nom($contenu);
	$type_jeu = _q($type_jeu==''?_T('jeux:jeu_vide'):$type_jeu);
	$titre_prive = _q($titre_prive==''?_T('jeux:sans_titre_prive'):$titre_prive);
	$contenu = _q("<jeux>$contenu</jeux>");
	if (!$id_jeu) {
		spip_query("INSERT into spip_jeux (statut,type_jeu,titre_prive,contenu,enregistrer_resultat) VALUES('publie',$type_jeu,$titre_prive,$contenu,'$enregistrer_resultat')");	
		$id_jeu = mysql_insert_id();		
	} else {
		spip_query("REPLACE into spip_jeux (id_jeu,statut,type_jeu,titre_prive,contenu,enregistrer_resultat) VALUES ($id_jeu,'publie',$type_jeu,$titre_prive,$contenu,'$enregistrer_resultat')");
	}
	return $id_jeu;
}
	
?>
