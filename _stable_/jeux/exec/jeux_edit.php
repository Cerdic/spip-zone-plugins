<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_jeux_edit(){
	include_spip('inc/utils');
	
	$valider = _request('valider');
	$nouveau = _request('nouveau');
	$id_jeu	 = _request('id_jeu');
	$nom	 = _request('nom');
	$titre	 = _request('titre');
	$contenu = _request('contenu');
	$enregistrer_resultat = _request('enregistrer_resultat');

	
	if ($valider) {
		$id_jeu = jeux_ajouter_jeu($titre,$contenu,$enregistrer_resultat,$id_jeu);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_voir', 'id_jeu='.$id_jeu,true));
	}
	
	$nouveau ? debut_page(_T('jeux:nouveau_jeu')) : debut_page(_T('jeux:modifier_jeu',array('id'=>$id_jeu,'nom'=>$nom)));
	
	if (!$nouveau){
	$requete = spip_fetch_array(spip_query("SELECT enregistrer_resultat,contenu,nom,titre FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$nom = $requete['nom'];
	$titre = $requete['titre']==_T('jeux:sans_titre')?'':entites_html($requete['titre']);
	$contenu = entites_html(strip_tags($requete['contenu']));
	$enregistrer_resultat  = $requete['enregistrer_resultat'];
	}
	
	debut_gauche();
	debut_boite_info();
	
	if ($id_jeu)
		echo icone_horizontale(_T('jeux:retourner_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png'));
	echo icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'));
	fin_boite_info();

	
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	creer_colonne_droite();
	debut_droite();
	$nouveau ? gros_titre(_T('jeux:nouveau_jeu')) : gros_titre(_T('jeux:modifier_jeu',array('id'=>$id_jeu,'nom'=>$nom)));
	
	debut_cadre_formulaire();
	echo "<form method='post' name='jeux_edit'>\n";
	debut_cadre_relief();
	
	// titre
	echo "<label><span class='titrem'>"._T('jeux:jeu_titre_prive');
	echo "<br /></span><input type='text' name='titre' value=\"$titre\" style='width:100%;' />";
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
	echo fin_cadre_formulaire(), fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($titre,$contenu, $enregistrer_resultat, $id_jeu=false){
	include_spip('jeux_utils');
	$nom = jeux_trouver_nom($contenu);
	$nom = _q($nom==''?_T('jeux:jeu_vide'):$nom);
	$titre = _q($titre==''?_T('jeux:sans_titre'):$titre);
	$contenu = _q("<jeux>$contenu</jeux>");
	if (!$id_jeu) {
		spip_query("INSERT into spip_jeux (statut,nom,titre,contenu,enregistrer_resultat) VALUES('publie',$nom,$titre,$contenu,'$enregistrer_resultat')");	
		$id_jeu = mysql_insert_id();		
	} else {
		spip_query("REPLACE into spip_jeux (id_jeu,statut,nom,titre,contenu,enregistrer_resultat) VALUES ($id_jeu,'publie',$nom,$titre,$contenu,'$enregistrer_resultat')");
	}
	return $id_jeu;
}
	
?>
