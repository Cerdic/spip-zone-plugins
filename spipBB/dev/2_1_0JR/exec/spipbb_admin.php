<?php


// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_admin() {

	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_display;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	include_spip('inc/spipbb_rubriquage');
	#

	$id_salon=intval(_request('id_salon'));

	#
	# prepa salon
	if (!empty($id_salon)) {

		// + prepa info rubrique(salon)
		$row = sql_fetsel(array("id_rubrique","id_parent","titre","descriptif"), // rows
							"spip_rubriques", // from
							"id_rubrique=$id_salon"); //where

		$id_salon = $row['id_rubrique'];
		$desc_salon = $row['descriptif'];
		$id_parent_salon = $row['id_parent'];

		# si crea rubrique(salon), renumerote
		# valable pour la premiere passe sur spipb_admin !
		if(recuperer_numero($row['titre'])=='') {
			spipbb_renumerote();
		}
		$titre_salon = supprimer_numero($row['titre']);
	}
	else {
		$titre_salon=_T('spipbb:titre_spipbb');
	}

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec'))." : ".textebrut(typo($titre_salon)), "forum", "spipbb_admin",$id_salon);
	echo "<a name='haut_page'></a>";


	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'),$id_salon);

	echo debut_droite('',true);

	echo debut_cadre_formulaire('',true);

	echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

	//
	// présenter UN salon/rubrique
	//
	if($id_salon) {

		if($id_parent_salon=='0') { $ico_rang = "secteur-24.gif"; }
		else { $ico_rang = "rubrique-24.gif"; }

		echo "\n<table cellpadding='2' cellspacing='0' border='0' width='100%'>";
		echo "<tr>";
		echo "<td valign='top' width='10%'>\n";

		# bouton edition rubrique
		# h. 18/11
		icone(_T('icone_modifier_rubrique'), generer_url_ecrire("spipbb_rubriques_edit","id_rubrique=$id_salon"), $ico_rang, "edit.gif");
		#echo http_img_pack($ico_rang,''," border='0' valign='absmiddle'");
		#echo "<span class='verdana2'>".$id_salon."</span>";
		echo "</td>";
		echo "<td width='80%' valing='top'>\n";

		debut_cadre_relief();

		echo gros_titre(typo($titre_salon),'',false);
		echo "<span class='arial2'>".propre($desc_salon)."</span>";
		fin_cadre_relief();

		echo "</td><td width='10%' valign='top'>\n";

		# voir en ligne
		#
		echo "<div style='float:right; margin-left:3px; padding:2px;'>\n";
		icone(_T('icone_voir_en_ligne'), generer_url_public("rubrique", "id_rubrique=".$id_salon), "racine-24.gif", "rien.gif");
		echo "</div>\n";
		echo "\n</td></tr>";

		echo "<tr><td>";

		# bouton nouveau salon (dans rub-secteur)
		#
		if($id_parent_salon=='0' AND ($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon))) {
			echo "<div style='float:left; padding:2px;'>";
			icone(_T('spipbb:creer_categorie'), generer_url_ecrire("spipbb_rubriques_edit", "id_parent=".$id_salon."&new=oui"), "rubrique-24.gif","creer.gif");
			echo "</div>";

		}
		echo "</td><td colspan='2'>\n";
		# bouton nouveau forum/article
		#
		# h.12/11 .. supres : AND $id_parent_salon!='0'
		if (($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) ) {
			echo "<div style='float:right; padding:2px;'>";
			icone(_T('spipbb:creer_forum'), generer_url_ecrire("spipbb_articles_edit", "id_rubrique=".$id_salon."&new=oui"), _DIR_IMG_SPIPBB."gaf_forum.gif","creer.gif");
			echo "</div>";
		}
		echo "\n</td></tr></table>\n<br />";

		#
		# les forums de ce salon
		#
		$res_af = sql_select(array("id_article","titre","descriptif","statut"), // rows
							"spip_articles", // from
							"id_rubrique = $id_salon", // where
							"", // groupby
							array("titre")); // orderby

		# compter les forums
		if($nombre_forums=sql_count($res_af)) {
			$flag_ordonne = ($nombre_forums>1)?true:false;
		}
		else $flag_ordonne = false;

		$ifond=0;

		while ($row=sql_fetch($res_af)) {
			$id_forum = $row['id_article'];
			$titre_forum = supprimer_numero($row['titre']);
			$desc_forum = $row['descriptif'];
			$nbr_sujet='';
			$nbr_post='';
			$url_post='';

			if($row['statut']=='publie') {
				// nbre total de sujets de ce $id_forum
				$res_sujet = sql_select("id_forum","spip_forum","id_article='$id_forum' AND id_parent=0 AND statut IN ('publie', 'off', 'prop')");

				$nbr_sujet=sql_count($res_sujet);

				// nombre total de posts de ce $id_forum
				$res_post = sql_select("id_forum","spip_forum","id_article='$id_forum' AND statut IN ('publie', 'off', 'prop')");
				$nbr_post=sql_count($res_post);

				// dernier post
				$res_date = sql_select(array("id_forum","id_thread","DATE_FORMAT(date_heure, '%d/%m/%Y %H:%i') AS dateur"), // rows
										"spip_forum", // from
										"id_article=$id_forum AND statut IN ('publie', 'off', 'prop')", // where
										"", // groupby
										array("date_heure DESC"), // orderby
										"0,1"); // limit
				$rd = sql_fetch($res_date);
				$id_post = $rd['id_forum'];
				$id_sujet = $rd['id_thread'];
				$der_date = $rd['dateur'];

				$url_post = url_post_tranche($id_post, $id_sujet);

			}

			$ouvrir_forum = generer_url_ecrire("spipbb_forum", "id_article=".$id_forum);

			if(!function_exists('verif_article_ferme')) include_spip("inc/spipbb_util");
			$art_ferme = verif_article_ferme($id_forum, $GLOBALS['spipbb']['id_mot_ferme']);

			$ifond = $ifond ^ 1;
			$coul_sujet = ($ifond) ? $couleur_claire : '#e3e3e3';

			debut_bloc_couleur($coul_sujet);
			echo "\n<table cellpadding='3' cellspacing='0' border='0' width='100%'>\n"
			. "<tr>\n"
			. "<td width='6%' valign='top' class='verdana2'>\n";

			icone(_T('spipbb:forum_ouvrir'), $ouvrir_forum, _DIR_IMG_SPIPBB."gaf_forum.gif", "rien.gif");

			echo $id_forum 	. "</td>\n"
			. "<td valign='top' class='verdana2'>\n";

			if($art_ferme) {
				bloc_info_etat($art_ferme);
				echo "<br />";
			}
			$puce_statut = charger_fonction('puce_statut', 'inc');
			echo $puce_statut($id_forum, $row['statut'], $id_salon,'article')."&nbsp;";
			echo "<span class='verdana3'><b>".propre($titre_forum)."</b></span><br />"
			. propre($desc_forum)
			. "</td>\n"
			. "<td width='8%' valign='top' class='verdana2'>\n";

			debut_bloc_gricont();
			echo "<img src='"._DIR_IMG_SPIPBB."gaf_sujet-12.gif' border='0' align='absmiddle' title='"._T('spipbb:sujet_nombre')."' /><br />";
			echo $nbr_sujet;
			fin_bloc();

			echo "</td>\n";
			echo "<td width='8%' valign='top' class='verdana2'>\n";

			debut_bloc_gricont();
			echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' border='0' align='absmiddle' title='"._T('spipbb:total_messages')."' /><br />";
			echo $nbr_post;
			fin_bloc();

			echo "</td>\n"
			. "<td width='12%' valign='top' class='verdana2'><div align='center'>\n";

			if($id_sujet!='' && $url_post) {
				echo "<a href='".$url_post."' title='Voir ce message'>"
				. "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' border='0' align='absmiddle' />"
				. _T('spipbb:dernier')."<br />".$der_date."</a>";
			}

			echo "</div></td>";

			if($flag_ordonne) {
				echo "<td width='3%' valign='top' class='verdana2'>\n";
				echo bouton_ordonne_forum($id_forum,generer_url_ecrire("spipbb_admin","id_salon=$id_salon",true));
				echo "</td>";
			}

			echo "</tr></table>\n";
			fin_bloc();

		}
	}


	//
	// les rubriques a la SPIP
	//
	afficher_enfant_rubfo($id_salon);

	echo fin_cadre_formulaire(true);

	echo "<br />";


	//
	// les 20 derniers sur site / ou sur rubrique selectionnee
	//

	// si sur rub
	if($id_salon) {
		$enum_art = branche_articles($id_salon);
		$wheres = ""; // si rubrique sans article
		if (!empty($enum_art)) {$wheres = "AND id_article IN ($enum_art)";}
	}

	$res = sql_select(array("id_forum","id_thread","date_heure","auteur","titre","statut"), // rows
						"spip_forum", // from
						"statut IN ('publie', 'off', 'prop') $wheres", // where
						"", // groupby
						array("date_heure DESC"), //orderby
						"0,20");// limit

	echo debut_cadre_formulaire('',true);
	echo "\n<table cellpadding='3' cellspacing='0' border='0' width='100%'>\n";
	echo "<tr><td colspan='3'><span class='verdana3'><b>"._T('spipbb:derniers_messages')."</b></span></td></tr>\n";
	while ($row = sql_fetch($res))
		{
		$id_post = $row['id_forum'];
		$date_rel = date_relative($row['date_heure']);
		$aut_post = typo($row['auteur']);
		$id_sujet = $row['id_thread'];
		$titre_post = $row['titre'];
		$url_post = url_post_tranche($id_post, $id_sujet);
		$statut = $row['statut'];
		$icostat='';

		switch($statut) {
			case 'off':
			$icostat=http_img_pack('puce-poubelle-breve.gif',$statut,"border='0' align='absmiddle'",_T('spipbb:post_rejete'))."&nbsp;";
			break;
			case 'prop':
			$icostat=http_img_pack('puce-orange-breve.gif',$statut,"border='0' align='absmiddle'",_T('spipbb:post_propose'))."&nbsp;";
			break;
			case 'publie': $icostat="";
			break;
		}

		if($aut_post=='') { $aut_post=_T('spipbb:anonyme'); }

		$ifond = $ifond ^ 1;
		$coul_ligne = ($ifond) ? $couleur_claire : '#ffffff';

		echo "<tr width='100%' bgcolor='".$coul_ligne."'>\n";
		echo "<td class='verdana2' width='20%'>".$date_rel."</td>\n";
		echo "<td valign='middle' class='verdana2' width='20%'>".$aut_post."</td>\n";
		echo "<td class='verdana2'>";
		echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' border='0' align='absmiddle' />&nbsp;".$icostat;
		echo "<b><a href='".$url_post."'>".couper(propre($titre_post), "50")."</a></b>\n";
		echo "</td></tr>\n";
		}
	echo "</table>\n";
	echo fin_cadre_formulaire(true);

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_admin

?>
