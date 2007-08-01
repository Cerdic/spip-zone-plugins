<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');




function exec_jeux_edit(){
	$valider = _request('valider');
	
	$contenu = _request('contenu');
	
	if ($valider)
		{
		jeux_ajouter_jeu($contenu);
		};
		
	
	// Admin SPIP-Listes
	echo debut_page(_T('jeux:nouveau_jeu'));
	gros_titre(_T('jeux:nouveau_jeu'));
	
	
	debut_gauche();
	echo debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	echo fin_cadre_relief();
	creer_colonne_droite();
	debut_droite();
	debut_cadre_formulaire();
	echo "<form method='post'  name='jeux_edit'>
	<textarea  name='contenu'  class='formo' rows='20' cols='40'> </textarea>
	";
	
	echo "<p align='right'><input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</form>";
		
	echo fin_cadre_formulaire(),fin_gauche(), fin_page();
}

function jeux_ajouter_jeu($contenu){
	include_spip('inc/utils');
	
	spip_query("INSERT into spip_jeux (contenu) VALUES('<jeux>".$contenu."</jeux>')");
	return;
};
	
?>
