<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_jeux_edit(){
	include_spip('inc/utils');
	
	$valider = _request('valider');
	$contenu = _request('contenu');
	$nouveau = _request('nouveau');
	$id_jeu	 = _request('id_jeu');
	

	
	if ($valider)
		{
		$id_jeu = jeux_ajouter_jeu($contenu,$id_jeu);
		 include_spip('inc/headers');	 redirige_par_entete(generer_url_ecrire('jeux_voir', 'id_jeu='.$id_jeu));
		
		}
		
	
	// Admin SPIP-Listes
	$nouveau ? debut_page(_T('jeux:nouveau_jeu')) : debut_page(_T('jeux:modifier_jeu'));
	$nouveau ? gros_titre(_T('jeux:nouveau_jeu')) : gros_titre(_T('jeux:modifier_jeu'));
	$contenu = spip_fetch_array(spip_query("SELECT contenu FROM spip_jeux WHERE id_jeu =".$id_jeu));
	
	debut_gauche();
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	creer_colonne_droite();
	debut_droite();
	debut_cadre_formulaire();
	echo "<form method='post'  name='jeux_edit'>
	<textarea  name='contenu'  class='formo' rows='20' cols='40'>";
	
	echo strip_tags($contenu['contenu']);
	
	echo " </textarea>";
	echo "<p align='right'><input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</form>";
		
	echo fin_cadre_formulaire(),fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($contenu,$id_jeu){
	
	
	if (!$id_jeu) {
		spip_query("INSERT into spip_jeux (contenu) VALUES('<jeux>".$contenu."</jeux>')");	
		$id_jeu = mysql_insert_id();		
				}
	
	else 		{
		spip_query('REPLACE into spip_jeux (id_jeu,contenu) VALUES ('.$id_jeu.',"<jeux>'.$contenu.'</jeux>")');
		
		}
	return $id_jeu;
};
	
?>
