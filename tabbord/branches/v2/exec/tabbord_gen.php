<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Recapitulatif, point accueil plugin
+--------------------------------------------+
*/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_gen() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


//
// requis
//
include_spip('inc/tabbord_pres');
include_spip('inc/func_tabbord_req');


//
// prepa
//



//
// affichage
//

#debut_page(_T('tabbord:titre_plugin'), "suivi", "tabbord");
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');
	echo "<br />";


// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}
	


debut_gauche();

menu_gen_tabbord();


debut_droite();


debut_cadre_formulaire();
	$rubriques = etats_rubriques();
	$articles = etats_articles();
	$breves = etats_breves();
	$mots_clefs = etats_mots_clefs();
	$documents = etats_documents();
	$forums = etats_forums();
	$petitions = etats_petitions();
	$auteurs = etats_auteurs();
	$sites = etats_sites();

	gros_titre($GLOBALS['meta']['nom_site']);

		echo "\n<table cellspacing='0' cellpadding='2' border='0' class='tabbord' width='100%'>\n";
		echo "<tr><td colspan='6'>&nbsp;</td></tr>\n"; 

		echo "<tr><th width='25%'>"._T('tabbord:types_pages')."</th>\n
	  		<th width='15%' align='center'>"._T('tabbord:publiee_s')."</th>\n
			<th  colspan='2' align='center'>"._T('tabbord:publiee_s_non')."</th>\n
			<th  colspan='2' align='center'>"._T('tabbord:total')."</th></tr>\n";
	  
		echo "<tr class='liste'><td><a href='".generer_url_ecrire("tabbord_liste","objet=rubrique")."'>"._T('tabbord:rubrique_s')."</a></td>\n";
		echo "<td class='center'>" . $rubriques[0] . "</td>\n";
		echo "<td colspan='2' class='center'>" . $rubriques[1] . "</td>\n";
		echo "<td colspan='2' class='center'>" . $rubriques[2] . "</td></tr>\n";

		echo "<tr class='liste'><td><a href='".generer_url_ecrire("tabbord_liste","objet=article")."'>"._T('tabbord:article_s')."</a></td>";
		echo "<td class='center'>" . $articles[0] . "</td>";
		echo "<td colspan='2' class='center'>" . $articles[1] . "</td>";
		echo "<td colspan='2' class='center'>" . $articles[2] . "</td></tr>";

		echo "<tr class='liste'><td><a href='".generer_url_ecrire("tabbord_liste","objet=breve")."'>"._T('tabbord:breve_s')."</a></td>";
		echo "<td class='center'>" . $breves[0] . "</td>";
		echo "<td colspan='2' class='center'>" . $breves[1] . "</td>";
		echo "<td colspan='2' class='center'>" . $breves[2] . "</td></tr>";

		echo "<tr><td colspan='6'>&nbsp;</td></tr>";
		
		echo "<tr class='liste'><td><a href='".generer_url_ecrire("tabbord_mots")."'>"._T('tabbord:mot_clef_s')."</a></td>";
		echo "<td class='center'>" . $mots_clefs[0] . "</td>";
		echo "<td colspan='4' class='center'>"._T('tabbord:groupes_mot_s_').$mots_clefs[1]."</td>";
		echo "</tr>";
		
		echo "<tr class='liste'><td><a href='".generer_url_ecrire("tabbord_documents")."'>"._T('tabbord:document_s')."</a></td>";
		echo "<td class='center'>" . $documents . "</td>";
		echo "<td colspan='2' class='center'>&nbsp;</td>";
		echo "<td colspan='2' class='center'>&nbsp;</td></tr>";
		
		echo "<tr class='liste'><td>".
		($petitions[0]>0 ? "<a href='".generer_url_ecrire("tabbord_petitions")."'>"._T('tabbord:petition_s')."</a>":_T('tabbord:petition_s')).
		"</td>";
		echo "<td class='center'>". $petitions[0] . "</td>";
		echo "<td colspan='4' class='center'>"._T('tabbord:total_signatures_') . $petitions[1] . "</td></tr>";

		echo "<tr><td colspan='6'>&nbsp;</td></tr>";
		echo "<tr><th>&nbsp;</th>".
			"<th class='center'>"._T('tabbord:article_s')."</th>".
			"<th class='center'>"._T('tabbord:auteur_s')."</th>".
			"<th class='center'>"._T('tabbord:breve_s')."</th>".
			"<th class='center'>"._T('tabbord:rubrique_s')."</th>".
			"<th class='center'>"._T('tabbord:total')."</th></tr>";
		echo "<tr class='liste'><td>".
		($sites[4]>0 ? "<a href='".generer_url_ecrire("tabbord_sites")."'>"._T('tabbord:sites_references')."</a>":_T('tabbord:sites_references')).
		"</td>";
		echo "<td class='center'>" . $sites[0] . "</td>";
		echo "<td class='center'>" . $sites[1] . "</td>";
		echo "<td class='center'>" . $sites[2] . "</td>";
		echo "<td class='center'>" . $sites[3] . "</td>";
		echo "<td class='center'>" . $sites[4] . "</td></tr>";


		echo "<tr><td colspan='6'>&nbsp;</td></tr>";
		echo "<tr><th>"._T('tabbord:forum_s')."</th>".
			"<th class='center'>"._T('tabbord:public_s')."</th>".
			"<th class='center'>"._T('tabbord:interne_s')."</th>".
			"<th class='center'>"._T('tabbord:admin')."</th>".
			"<th class='center'>"._T('tabbord:propose_s')."</th>".
			"<th class='center'>"._T('tabbord:total')."</th></tr>";
		echo "<tr class='liste'><td>"._T('tabbord:message_s')."</td>";
		echo "<td class='center'>" . $forums[0] . "</td>";
		echo "<td class='center'>" . $forums[1] . "</td>";
		echo "<td class='center'>" . $forums[2] . "</td>";
		echo "<td class='center'>" . $forums[3] . "</td>";
		echo "<td class='center'>" . $forums[4] . "</td></tr>";

		echo "<tr><td colspan='6'>&nbsp;</td></tr>";
		echo "<tr><th colspan='6' align='center'><a href='".generer_url_ecrire("tabbord_auteurs")."'>"._T('tabbord:auteurs_enregistre_s')."</a></th></tr>";
		echo "<tr><th class='center'>"._T('tabbord:admin_restreints_')."</th>".
			"<th class='center'>"._T('tabbord:redacteur_s')."</th>".
			"<th class='center'>"._T('tabbord:visiteur_s')."</th>".
			"<th class='center'>"._T('tabbord:efface_s')."</th>".
			"<th class='center'>"._T('tabbord:autre_s')."</th>".
			"<th class='center'>"._T('tabbord:total')."</th></tr>";
		echo "<tr class='liste'><td class='center'>". $auteurs[0] ." (".$auteurs[1].")</td>";
		echo "<td class='center'>" . $auteurs[2] . "</td>";
		echo "<td class='center'>" . $auteurs[3] . "</td>";
		echo "<td class='center'>" . $auteurs[4] . "</td>";
		echo "<td class='center'>" . $auteurs[5] . "</td>";
		echo "<td class='center'>" . $auteurs[6] . "</td></tr>";

		echo "</table>";


	
fin_cadre_formulaire();


//
//
echo fin_gauche(), fin_page();
}
?>
