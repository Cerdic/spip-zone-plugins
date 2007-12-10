<?php
/*
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| Page deplacer un thread resultat
| (anc. gaf_val_affect.php)
+-------------------------------------------+
*/

#########################
# h. pour le moment ce script fait la mise à jour
# mais il faudra la passer en action !!
#########################

if (!defined("_ECRIRE_INC_VERSION")) return;



function exec_spipbb_affecter_affiche() {

# requis spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


# initialiser spipbb
include_spip('inc/spipbb_init');

# requis de cet exec
#

$id_sujet = intval(_request('id_sujet'));
$id_art_orig = intval(_request('id_art_orig'));
$id_art_new = intval(_request('id_art_new'));
$titre_sujet = _request('titre_sujet');

#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_L('titre_page_'._request('exec'), "forum", "spipbb_admin",'');
echo "<a name='haut_page'></a>";


debut_gauche();
	spipbb_menus_gauche(_request('exec'),$id_salon, $id_art);
	

debut_droite();


# admin seul
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_cadre_relief("");
		echo _T('avis_non_acces_page');
		fin_cadre_relief();
		echo fin_gauche(), fin_page();
		exit;
	}


// cherche message du thread a deplacer
$req=spip_query("SELECT id_forum FROM spip_forum 
				WHERE id_thread=$id_sujet AND id_article=$id_art_orig");

while ($row=spip_fetch_array($req)) {
	$idf = $row['id_forum'];
	spip_query("UPDATE spip_forum SET id_article = $id_art_new WHERE id_forum=$idf");
}

// recupere info pour affichage
$rqo=spip_query("SELECT titre FROM spip_articles WHERE id_article=$id_art_orig");
$ro=spip_fetch_array($rqo);
$titre_orig = $ro['titre'];

$rqn=spip_query("SELECT titre FROM spip_articles WHERE id_article=$id_art_new");
$rn=spip_fetch_array($rqn);
$titre_new = $rn['titre'];

debut_cadre_relief("");
	debut_ligne_foncee('0');
	echo "<img src='"._DIR_IMG_SPIPBB."gaf_sujet.gif' align='absmiddle' />\n";
	echo "<b>".propre($titre_sujet)."</b>\n";
	fin_bloc();
	
	echo "<div class='verdana3' style='padding:3px;'>"._T('gaf:forum_deplace')."</div>\n";
		
	debut_ligne_grise('30');
	echo "<div style='float:right; padding:3px; text-align:right; 
			border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
	echo " "._T('icone_retour')." <a href='".generer_url_ecrire("spipbb_forum","id_article=".$id_art_orig)."'>
			<img src='"._DIR_IMG_SPIPBB."gaf_forum.gif' border='0' align='absmiddle' /></a>";
	echo "</div>\n";
	echo propre($titre_orig);
	echo "<div style='clear:both;'></div>";
	fin_bloc();
	
	echo "<div class='verdana3' style='padding:3px;'>"._T('gaf:forum_vers')."</div>\n";

	debut_ligne_grise('30');
	echo "<div style='float:right; padding:3px; text-align:right; 
			border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
	echo " "._T('icone_retour')." <a href='".generer_url_ecrire("spipbb_forum", "id_article=".$id_art_new)."'>
			<img src='"._DIR_IMG_SPIPBB."gaf_forum.gif' border='0' align='absmiddle' /></a>";
	echo "</div>\n";
	echo propre($titre_new);
	echo "<div style='clear:both;'></div>\n";
	fin_bloc();

	echo "<br />";
	
	debut_bloc_gricont();
		echo "<span class='verdana3'>"._T('gaf:info_fin_maintenance')."</span>\n";
	fin_bloc();
	
fin_cadre_relief();


# pied page exec
bouton_retour_haut();

echo fin_gauche(), fin_page();

} // exec_spipbb_affecter_affiche
?>
