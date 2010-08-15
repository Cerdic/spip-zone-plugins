<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/forum'); // pour fonction boutons_controle_forum()
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# initialiser spipbb
include_spip('inc/spipbb_init');
# requis de cet exec
include_spip('inc/traiter_imagerie');
# requis spip
include_spip('inc/chercher_logo');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_sujet() {

	#  spip
	global 	$connect_statut,
			$connect_toutes_rubriques, $connect_id_rubrique,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_display,
			$spip_lang_right;

	# h.26/11/07 .. function boutons_controle_forum() : refonte maison !
	# tester usage depuis autre que spipbb_presentation, ni spipbb_util

	# valeurs recup
	$id_sujet=intval(_request('id_sujet'));
	$vl=intval(_request('vl'));

	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	#
	# sujet origine
	if($id_sujet) {
		$row=sql_fetsel("id_article, statut, titre","spip_forum","id_forum=$id_sujet");
		$statut_sujet = $row['statut'];
		$id_art=$row['id_article'];
		$titre_sujet=$row['titre'];
	}

	# recup de id_rubrique pour 'droits' sur bouton_ecrire_post.

	$rowrub=sql_fetsel("id_rubrique","spip_articles","id_article=$id_art");
	$id_rubrique=$rowrub['id_rubrique'];

	# verif si Forum conteneur (article) est fermé ?
	if(!function_exists('verif_article_ferme')) include_spip("inc/spipbb_util");
	$art_ferme = verif_article_ferme($id_art, $GLOBALS['spipbb']['id_mot_ferme']);

	# verif si Sujet est ferme ?
	if(!function_exists('verif_sujet_ferme')) include_spip("inc/spipbb_util");
	$sujet_ferme = verif_sujet_ferme($id_sujet, $GLOBALS['spipbb']['id_mot_ferme']);

	# sujet, type "annonce" ?
	$annonce = verif_sujet_annonce($id_sujet);

	# verif Forum type "annonce" ?
		$forum_annonce = verif_forum_annonce($id_art);
	# (tranche) fixe LIMIT : $dl
	$dl=($vl+0);

	//
	// requete des posts
	//

	$res_post = sql_select("*,DATE_FORMAT(date_heure, '%d/%m/%Y %H:%i') AS dateur_post",
							"spip_forum",
							"id_thread=$id_sujet AND statut IN ('publie', 'off', 'prop')",
							"",
							"date_heure",
							"$dl,$fixlimit");

	#
	# Prepa Tranches
	#
	// récup nombre total d'entrée de req_post
	$nligne = sql_fetsel("COUNT(*) AS total","spip_forum","id_thread=$id_sujet AND statut IN ('publie', 'off', 'prop')","","","$dl,$fixlimit");

	// valeur de tranche affichée
	$tranche_encours = $dl+1;

	// adresse retour des tranche
	$retour_gaf_local = generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet);

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(textebrut(typo($titre_sujet)), "forum", "spipbb_admin", $id_art);
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'),'',$id_art,$id_sujet);

	echo debut_droite('',true);

	#
	# Le sujet
	#
	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='600'>\n";
	echo "<tr>";

	echo "<td width='10%' valign='top'>\n";
		echo "<img src='"._DIR_IMG_SPIPBB."gaf_sujet.gif' border='0' valign='absmiddle' />\n";
	echo "</td>";

	echo "<td width='80%' valing='top'>\n";
	debut_cadre_relief("");

	echo "<span class='verdana2'>"._T('spipbb:sujet')." .. ".$id_sujet."</span>";
	echo gros_titre(typo($titre_sujet),'',false);
	echo "<br />";

	# boutons (+ message) fermeture/maintenance Sujet/Forum
	#
	echo debut_cadre_couleur('',true);
	// bouton fermer sujet (bloque post en zone public)
	if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_rubrique)) AND !$art_ferme AND !$sujet_ferme) {
		formulaire_bouton_ferlibsujet($id_sujet, 'ferme', _DIR_IMG_SPIPBB."gaf_verrou1.gif");
	}
	// boutons liberer sujet (debloque zone public)
	if(($connect_toutes_rubriques OR acces_restreint_rubrique($id_rubrique)) AND $sujet_ferme=="ferme") {
		formulaire_bouton_ferlibsujet($id_sujet, 'libere', _DIR_IMG_SPIPBB."gaf_libere1.gif");
	}
	// passer le sujet en 'annonce'
	if( $connect_toutes_rubriques AND !$annonce) {
		bouton_formulaire_annonce($id_sujet, "annonce", _DIR_IMG_SPIPBB."gaf_annonce.gif");
	}
	// passer le sujet en 'desannonce'
	if($connect_toutes_rubriques AND $annonce) {
		bouton_formulaire_annonce($id_sujet, "desannonce", _DIR_IMG_SPIPBB."gaf_desannonce.gif");
	}
	# message Etat .. ferme
	$obj='sujet';
	if($art_ferme) { $type_ferme=$art_ferme; $obj='forum'; }
	elseif($sujet_ferme) { $type_ferme=$sujet_ferme; }
	else { $type_ferme=''; }
	bloc_info_etat($type_ferme, $obj, $annonce);

	echo fin_cadre_couleur(true);

	fin_cadre_relief();
	echo "</td>\n";
	echo "<td width='10%' valign='top'>\n";

	# bouton voir en ligne
	#
	echo "<div style='float:right; margin-left:3px;'>\n";
	icone(_T('icone_voir_en_ligne'), generer_url_public("voirsujet", "id_forum=".$id_sujet), "racine-24.gif", "rien.gif");
	echo "</div>\n";

	echo "\n</td></tr><tr><td colspan='3'>\n";

		# bouton repondre (au thread)
		#
		if ($connect_statut == '0minirezo') {
			if ($accepter_forum!='non' AND $statut_sujet=='publie' AND $art_ferme!='maintenance') {
				if($art_ferme=="ferme" OR $forum_annonce==true){
					if($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) {
						bouton_ecrire_post($id_forum,'');
					}
				}
				else { bouton_ecrire_post($id_art, $id_sujet); }
			}
		}
	echo "</td></tr></table>\n<br />";


	#
	# afficher les tranches
	#
		if ($nligne['total'] > $fixlimit) {
			echo "<div align='center' class='iconeoff' style='margin:2px;'><span class='verdana2'><b>";
			tranches_liste_forum($tranche_encours, $retour_gaf_local, $nligne['total']);
			echo "</b></span></div>";
		}

	#
	# les posts
	#
	echo "\n<table cellpadding='3' cellspacing='1' border='0' width='600'>\n";

	$ifond= 0;
	while($row=spip_fetch_array($res_post)) {
		$id_post = $row['id_forum'];
		$id_parent = $row['id_parent'];
		$id_art_post = $row['id_article'];
		$titre_post = $row['titre'];
		$texte_post = $row['texte'];
		$aut_post = typo($row['auteur']);
		$mail_aut_post = $row['email_auteur'];
		$site_post = typo($row['nom_site']);
		$url_st_post = $row['url_site'];
		$date_post = $row['dateur_post'];
		$date_post_relative = $row['date_heure'];
		$statut_post = $row['statut'];
		$ip_post = $row['ip'];
		$id_auteur = $row['id_auteur'];

		# identifier auteur
		if($aut_post=="") { $aut_post=_T('spipbb:anonyme'); }
		# infos sur auteur
		if($id_auteur!='0') {
			$infos_aut=spipbb_donnees_auteur($id_auteur);
		}

		$ifond = $ifond ^ 1;

		// couleur de fond du post selon "statut"
		$couleur = ($statut_post=='off') ? '#e8c8c8' : ( ($statut_post=='prop') ? '#d0d4b2' : (($ifond) ? '#c7c7c7' : '#e3e3e3') ) ;

		if($id_parent=='0')
			{ $logo_post = "gaf_sujet.gif"; $couleur = $couleur_claire; }
		else
			{ $logo_post = "gaf_post.gif"; }

		// icone statut du post
		$aff_statut = icone_statut_post($statut_post);

		// affichage ; ligne de post
		echo "<tr bgcolor='$couleur_foncee'><td colspan='3'><a id='".$id_post."'></a></td></tr>\n";
		echo "<tr width='100%' bgcolor='$couleur'>";
		echo "<td width='5%'valign='top'>\n";
		echo "<img src='"._DIR_IMG_SPIPBB.$logo_post."' border='0'><br />\n";
		echo "<span class='verdana1'>".$id_post."</span>";
		echo "</td>\n";
		echo "<td width='75%' valign='top'>".$aff_statut;

		# affiche logo auteur spip
		# h.3/10/07 .. ajout avatar pour 6forum
		if ($id_auteur AND $spip_display!=1
			AND $spip_display!=4 AND $GLOBALS['meta']['image_process']!="non") {
			$voir_logo = "float:left; margin-right: 3px;";
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			$logo = $chercher_logo($id_auteur, 'id_auteur', 'on');
			list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
			if($fid) {
				$logo = image_reduire("<img src='$fid' alt='' />",50,50);
			}
			else {
				$logo = image_reduire(afficher_avatar($id_auteur),50,50);
			}
			echo  "\n<div style='$voir_logo'>$logo</div>";
		}

		echo "<span class='verdana3'><b>".propre($titre_post)."</b></span><br />\n";
		if ($mail_aut_post && ($connect_toutes_rubriques OR acces_restreint_rubrique($id_rubrique)))
			{
			echo "<div style='float:left; margin:2px 3px 0px 0px;'>\n";
			echo "<a href='mailto:$mail_aut_post?SUBJECT=".textebrut($titre_post)."' title='"._T('spipbb:ecrirea')." ".entites_html($aut_post)."'>\n";
			echo "<img src='"._DIR_IMG_PACK."cal-messagerie.png' width='18' height='13' border='0' /></a>\n";
			echo "</div>";
			}

		echo "<span class='verdana2'><b>".$aut_post."</b></span><br />\n";
		echo "<span class='verdana2'>"._T('spipbb:le')." ".$date_post."</span>\n";
		echo "</td><td width='18%' valign='top'><div align='right'>\n";
		echo "<span class='arial2'>".date_relative($date_post_relative)."</span></div>\n";
		echo "</td></tr><tr bgcolor='$couleur'>";
		echo "<td colspan='3' valign='top' class='verdana2'>\n";

		# bouton citer
		if ($connect_statut == '0minirezo') {
			if ($accepter_forum!='non' AND $statut_sujet=='publie' AND $art_ferme!='maintenance') {
				if($art_ferme=="ferme" OR $forum_annonce==true){
					if($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) {
						bouton_ecrire_post($id_art, $id_sujet, $id_post);
					}
				}
				else { bouton_ecrire_post($id_art, $id_sujet, $id_post); }
			}
		}

		echo propre(smileys($texte_post))."\n";

		# inserer signature si autorise/existe
		if(lire_config('spipbb/affiche_signature_post')=='oui' AND $infos_aut['signature_post']){
			echo "<p class='signature'>".propre($infos_aut['signature_post'])."</p>";
		}

		if ($site_post)
			{
			echo "<div class='verdana2'><br />--------<br />\n";
			echo http_img_pack('racine-site-12.gif','ico',"border='0' valign='absmiddle'",_T('spipbb:site_propose', array('auteur_post' => $aut_post)));
			echo " <b><a href='".$url_st_post."'>".$site_post."</a></b><br />--------</div>\n";
			}

		echo "</td></tr><tr bgcolor='$couleur'><td valign='top'>\n";


		echo "</td><td>\n";
		echo "<div align='right' class='arial2'>\n";
		if ($connect_toutes_rubriques OR acces_restreint_rubrique($id_rubrique)) {
			// Déplacer
			/*
			echo icone_inline(_T('icone_supprimer_deplacer'), generer_action_auteur('instituer_forum',"$id_post-deplacer", 
			generer_url_ecrire('spipbb_sujet', 'id_sujet='.$id_sujet, true, true) . "#id$id_forum"),
			"forum-public-24.gif",
			"deplacer.gif", 'right', 'non');
			*/
			
			// Diviser
			
			// passer post en 'off' == supprimer
			echo boutons_controle_forum($id_post, $statut_post, $id_auteur, "id_article=$id_post", $ip_post,'spipbb_sujet','id_sujet='.$id_sujet);
		}
		echo "</div>\n";
		echo "</td><td valign='bottom'>\n";
		echo "<div align='right' class='arial2'>". $ip_post ."</div>\n";
		echo "</td></tr>\n";
	}

	echo "<tr><td colspan='3'>\n";

		// bouton repondre
		if ($connect_statut == '0minirezo') {
			if ($accepter_forum!='non' AND $statut_sujet=='publie' AND $art_ferme!='maintenance') {
				if($art_ferme=="ferme" OR $forum_annonce==true){
					if($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) {
						bouton_ecrire_post($id_forum,'');
					}
				}
				else { bouton_ecrire_post($id_art, $id_sujet); }
			}
		}

	echo "</td></tr>\n";
	echo "</table>\n";

	#
	# afficher les tranches
	#
		if ($nligne['total']>$fixlimit) {
			echo "<div align='center' class='iconeoff' style='margin:2px;'><span class='verdana2'><b>";
			tranches_liste_forum($tranche_encours, $retour_gaf_local, $nligne['total']);
			echo "</b></span></div>";
		}

	// retour haut de page
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_sujet

?>