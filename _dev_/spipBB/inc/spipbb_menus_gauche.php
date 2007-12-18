<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : inc/spipbb_menus_gauche                            #
#  Authors : scoty 2007                                         #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                 #
# [fr] compil menu_arbo.php(gaf) + interface_admin.php(spipbb)  #
#---------------------------------------------------------------#
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

#
# affichage de la colonne de menus
#
function spipbb_menus_gauche($script, $id_salon='', $id_art='', $id_sujet='', $modos='') {

	global $connect_statut,
			$connect_toutes_rubriques,
			$connect_id_rubrique,
			$connect_id_auteur;

	#
	# Liste de tous les menus d'administration (exec/...) 
	#
	# $modules[cat][rang]=array(nom,file,icone)
	$modules=array();

	// ces menus doivent toujours etre actifs
	# plus besoin de 01_/ZZ_ ... sur nom
	$modules['01_general'][40]=array('01_configuration',"spipbb_configuration",'administration-24.gif'); 
	$modules['01_general'][50]=array('ZZ_debug',"spipbb_admin_debug",'racine-24.gif');

	if($GLOBALS['spipbb']['configure']=='oui') {
		// tous ces menus necessitent que spipbb soit configure et active
		if ($GLOBALS['spipbb']['config_id_secteur'] == 'oui' AND !empty($GLOBALS['spipbb']['id_secteur'])) {
			// ces menus ont besoin d'un secteur/forums defini (a priori)
			$modules['01_general'][15] = array('inscrits',"spipbb_inscrits","redacteurs-24.gif");

			if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
			$modules['01_general'][10] = array('gestion',"spipbb_admin",_DIR_IMG_SPIPBB.'spipbb-24.png');
			# h. 28/11 .. ordonner art/rub : inclus dans spipbb_admin
			#$modules['01_general'][20] = array('ordonner',"spipbb_admin_gestion_forums",'descendre-16.png');
			$modules['01_general'][25] = array('effacer',"spipbb_effacer",'poubelle.gif') ;

			$modules['outils'][10] = array('fromphpbb',"spipbb_admin_fromphpbb",_DIR_IMG_SPIPBB.'fromphpbb-24.png');

				if ($GLOBALS['spipbb']['config_spam_words']=='oui') {
					// ces menus ont besoin que le spam soit active
					$modules['spam'][15] = array('swwords',"spipbb_admin_anti_spam_words",'cadenas-24.gif');
					$modules['spam'][16] = array('swlog',"spipbb_admin_anti_spam_log",'doc-24.gif');
					$modules['spam'][17] = array('swforum',"spipbb_admin_anti_spam_forum",'petition-24.gif');
				}
			}

		}
		$modules['01_general'][30] = array('02_etat',"spipbb_admin_etat",'statistiques-24.gif');
		$modules['spam'][10] = array('swconfig',"spipbb_admin_anti_spam_config",'administration-24.gif');
	}

	#
	# entete (icone + titre)
	#
	echo entete_colonne_gauche($script);

	#
	# bloc hierarchie
	#
	if(!empty($id_salon) OR !empty($id_art)) {
		bloc_hierarchie($id_salon,$id_art);
	}

	#
	# rubriques de l_admin-restreint connecte
	#
	if($connect_id_rubrique AND (!empty($id_salon) OR !empty($id_art)) ) {
		echo rubriques_admin_restreint($connect_id_auteur);
	}

	#
	# Les posts ('prop') en attente de moderation
	#
	if ($connect_toutes_rubriques OR acces_restreint_rubrique($id_salon)) {
		echo posts_proposes_attente_moderation();
	}

	#
	# Les moderateurs
	#
	echo liste_moderateurs($modos,$id_salon,$id_art);

	#
	# alerte fermeture pour maintenance
	#
	echo alerte_maintenance();

	#
	# boutons fonctions
	#
	echo spipbb_admin_gauche($script,$modules);

	#
	# assumons notre oeuvre ! ;-)
	#
	echo signature_spipbb();

} // spipbb_menus_gauche



#
# contenu Entete page
#
function entete_colonne_gauche($titre_page) {
	$aff = "<div style='float:left; margin-right:5px; margin-bottom:20px;'>"
		. "<img src='"._DIR_PLUGIN_SPIPBB."img_pack/spipbb-48.png' alt='ico' />"
		. "</div>"
		. gros_titre(_T('spipbb:titre_spipbb'),'',false)
		. debut_boite_info(true)
		. _T('spipbb:admin_titre_page_'.$titre_page)
		. fin_boite_info(true);

	if(_request('exec')=='spipbb_rubriques_edit') {
		$aff.="<span class='verdana2'>";
		$aff.= _request('new')? _T('nouveau_salon') : _T('modifier_salon');
		$aff.="</span>";
	}
	if(_request('exec')=='spipbb_articles_edit') {
		$aff.="<span class='verdana2'>";
		$aff.= _request('new')? _T('nouveau_forum') : _T('modifier_forum');
		$aff.="</span>";
	}
	
	$aff.= "<div class='nettoyeur'></div>";
	
	return $aff;
}



#
# hierarchie sur élément unique
#
function bloc_hierarchie($id_rubrique, $id_article, $parents='') {
	global $spip_lang_left, $lang_dir;

	$id_rub_courant=intval(_request('id_salon'));
	$id_art_courant=intval(_request('id_article'));

	if (!empty($id_article)) {
		$result=sql_query("SELECT id_article, id_rubrique, titre 
							FROM spip_articles 
							WHERE id_article=$id_article");
		while($row = sql_fetch($result))
			{
			$id_article = $row['id_article'];
			$id_rubrique = $row['id_rubrique'];
			$titre = supprimer_numero($row['titre']);
			$logo = _DIR_IMG_SPIPBB."gaf_forum-12.gif";
			
			if($id_article!=$id_art_courant)
				{
				$parents = "<div class='verdana3' ". 
			  	http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px;"). 
			  	"><a href='".generer_url_ecrire("spipbb_forum", "id_article=".$id_article)."'>".
				typo($titre)."</a></div>\n<div style='margin-$spip_lang_left: 3px;'>".$parents."</div>";
				}
			}
		bloc_hierarchie($id_rubrique, "", $parents);
	}
	elseif(!empty($id_rubrique)) {

		$query = "SELECT id_rubrique, id_parent, titre, lang 
				FROM spip_rubriques 
				WHERE id_rubrique=$id_rubrique";
		$result = sql_query($query);

		while ($row = sql_fetch($result)) {

			$id_rubrique = $row['id_rubrique'];
			$id_parent = $row['id_parent'];
			$titre = supprimer_numero($row['titre']);
			changer_typo($row['lang']);

			if (acces_restreint_rubrique($id_rubrique)) $logo = "admin-12.gif";

			if (!$id_parent) $logo = "secteur-12.gif";
			else $logo = "rubrique-12.gif";

			if($id_rubrique!=$id_rub_courant)
				{

				$parents = "<div class='verdana3' ". 
				  http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px").
				  ">".
				  "<a href='".generer_url_ecrire("spipbb_admin", "id_salon=".$id_rubrique)."'>".
				  typo($titre)."</a></div>\n<div style='margin-$spip_lang_left: 3px;'>".
				  $parents.
				  "</div>";
				}
			}
		bloc_hierarchie($id_parent, '', $parents);
	}
	else {
		$logo = "racine-site-12.gif";

		$parents = "<div class='verdana3' " 
		  . http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px") 
		  . "><a href='".generer_url_ecrire("spipbb_admin"). "'><b>"
		  . _T('spipbb:secteur_forum')."</b></a></div>\n<div style='margin-$spip_lang_left: 3px;'>"
		  . $parents."</div>";

		echo $parents."<br />";
	}
}
//


#
# contenu : rubrique de admin-restreint
#
function rubriques_admin_restreint($connect_id_auteur) {
	$aff = "<br />";
	$aff.= debut_cadre_relief("../"._DIR_IMG_SPIPBB."spipbb-24.gif", true, '',_T('moderation'));

	$q = sql_query("SELECT R.id_rubrique, R.titre, R.descriptif 
					FROM spip_rubriques AS R, spip_auteurs_rubriques AS A 
					WHERE A.id_auteur=$connect_id_auteur AND A.id_rubrique=R.id_rubrique 
					ORDER BY titre");
	$rubs = array();
	while ($r = sql_fetch($q)) {
		$rubs[] = "<a title='" .
		    typo($r['descriptif']) .
		    "' href='" . generer_url_ecrire('spipbb_admin', "id_salon=" .$r['id_rubrique']) . "'>" .
		    supprimer_numero(typo($r['titre'])) .
		    '</a>';
	}
	$aff.= "<ul style='margin:0px; padding-left: 20px; margin-bottom: 5px;'>\n<li>". join("</li>\n<li>", $rubs). "\n</li></ul>";
	$aff.= fin_cadre_relief(true);

	return $aff;
}


#
# contenu : les posts en attente de moderation
#
function posts_proposes_attente_moderation() {
	$result = sql_query ("SELECT SQL_CALC_FOUND_ROWS id_forum, titre, id_thread 
							FROM spip_forum WHERE statut='prop' 
							ORDER BY date_heure LIMIT 0,10");
	// récup nombre total d'entrées de $result (mysql 4.0.0 mini)
	$ttligne= sql_query("SELECT FOUND_ROWS()");

	list($nbrprop) = @spip_fetch_array($ttligne);

	$aff='';
	if($nbrprop) {
		$aff.= "<br />"
			. "\n<div class='bandeau_rubriques' style='z-index: 1;'>"
			. bandeau_titre_boite2(_L('poste_valide'),"gaf_p_prop.gif",'','',false)
			. "<div class='plan-articles'>";
		
		while($row = sql_fetch($result))
			{
			$idprop=$row['id_forum'];
			$titreprop=$row['titre'];
			$idthread=$row['id_thread'];
			$urlprop = url_post_tranche($idprop, $idthread);
			$ico_prop = ($idprop==$idthread) ? "gaf_sujet-12.gif" : "gaf_post-12.gif" ;
			$aff.= "<a href='".$urlprop."'>".couper($titreprop,30)."</a>\n";
			}
		
		if($nbrprop>10) { $aff.= _T('spipbb:etplus'); }
		$aff.= "</div></div>\n";
	}
	return $aff;
}

#
# contenu : liste des modos	
function liste_moderateurs($modos,$id_salon,$id_art) {
	if(!is_array($modos)) { $modos=array(); }

	# sur page sujet, recherche rub de art (du thread en cours) + auteurs
	if (empty($id_salon) and !empty($id_art)) {
		$r_s = sql_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_art");
		if(sql_count($r_s)) {
			$row=sql_fetch($r_s);
			$id_salon = $row['id_rubrique'];
		}
		#auteurs article
		$result = sql_query("SELECT a.id_auteur, a.nom, a.statut 
							FROM spip_auteurs as a, spip_auteurs_articles as b 
							WHERE a.id_auteur=b.id_auteur AND b.id_article=$id_art");
		while ($ro = sql_fetch($result)) {
			$modos[$ro['id_auteur']]['nom'] = $ro["nom"];
			$modos[$ro['id_auteur']]['statut'] = bonhomme_statut($ro);
			$modos[$ro['id_auteur']]['acces'] = generer_url_ecrire('auteur_infos', "id_auteur=".$ro['id_auteur']);
		}
	}
	# doublons
	$where_modos='';
	if(count($modos)>=1) {
		$modos_dbl = join(',', array_keys($modos));
		$where_modos = "A.id_auteur NOT IN ($modos_dbl) AND";
	}

	# admins rubrique
	if (!empty($id_salon)) {

		$res = sql_query("SELECT DISTINCT A.nom, A.id_auteur, A.statut 
							FROM  spip_auteurs AS A, spip_auteurs_rubriques AS B 
							WHERE $where_modos A.id_auteur=B.id_auteur AND id_rubrique=$id_salon");
		if (sql_count($res)) { // c: 18/12/7 count un peu inutile car le while fait quasi le meme test
			while ($row = sql_fetch($res)) {
				$modos[$row['id_auteur']]['nom'] = $row['nom'];
				$modos[$row['id_auteur']]['statut'] = bonhomme_statut($row);
				$modos[$row['id_auteur']]['acces'] = generer_url_ecrire('auteur_infos', "id_auteur=".$row['id_auteur']);
			}
		}
	}

	# aff.liste modos
	$aff='';
	if(count($modos)>=1) {
		$aff.= debut_cadre_relief("fiche-perso-24.gif", true, '', _T('spipbb:moderateurs'));
		foreach($modos as $k => $v) {
			$aff.= "\n<a href='".$v['acces']."'>".$v['statut']."&nbsp;".typo($v['nom'])."</a><br />";
		}
		$aff.= fin_cadre_relief(true);
	}
	return $aff;
}


#
# contenu : message lock pour maintenance
#
function alerte_maintenance() {
	if ($ds = @opendir(_DIR_SESSIONS)) {
		while (($file = @readdir($ds)) !== false) {
			if (preg_match('/^gafart_([0-9]+)-([0-9]+)\.lck$/', $file, $match)) {
				$datime=date("d/m/y H:i",@filemtime(_DIR_SESSIONS.$file));
				$art_mt=$match[1];
				$aut_mt=$match[2];
				$req=sql_query("SELECT nom FROM spip_auteurs WHERE id_auteur=$aut_mt");
				$row=sql_fetch($req);
				$req2=sql_query("SELECT titre FROM spip_articles WHERE id_article=$art_mt");
				$row2=sql_fetch($req2);

				$aff = "<br />"
					. debut_cadre_trait_couleur("../"._DIR_IMG_SPIPBB."gaf_verrou2.gif",true,"",_T('spipbb:maintenance'))
					. "<div class='verdana3'><b>".$row['nom']."</b></div>\n"
					. "<div class='verdana2'>"._T('spipbb:admfermer')."<br />\n"
					. "<b><a href='".generer_url_ecrire("gaf_forum","id_article=".$art_mt)."'>"
					. $row2['titre']."</a></b><br />"._T('spipbb:pour_maintenance')."<br />".$datime."</div>\n"
					. fin_cadre_trait_couleur(true);
			}
		}
	}
}

#
# contenu : les boutons de fonctions (chrys - spipbb_admin_gauche() )
#
function spipbb_admin_gauche($script,$modules) {

	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler'); // voir un charger fonction

	ksort($modules);
	$affichage = "\n";
	while( list($cat, $rang) = each($modules) ) {
		$cat = _T('spipbb:admin_cat_'.$cat); // on traduit le nom de chaque categorie

		$affichage.= debut_boite_info(true). "<b>".$cat."</b>";
		ksort($rang);

		while( list($num,$action) = each($rang) ) {
			$nom=$action[0];
			$file=$action[1];
			$icone=$action[2];

			$nom = _T('spipbb:admin_action_'.$nom) ; // on traduit le nom de chaque action(exec)

			if ( $script <> $file ) { $lien = generer_url_ecrire($file); }
			else { $lien="0"; }	// pas de lien sur l'action en cours ! 

			if($icone) {
				$icone = http_img_pack($icone,""," border='0' align='absmiddle'")."\n";
			}

			$contexte = array(
							'lien' => $lien,
							'action' => $nom,
							'icone_menu' => $icone
							);

			// chryjs: desactive et remplace par le bloc ci apres 
			$affichage.= recuperer_fond("prive/spipbb_bloc_admin_menu",$contexte);

		}
		$affichage .= fin_boite_info(true)."<br />\n";
	}
	$affichage.= "\n";

	return $affichage;
} // spipbb_boutons_fonctions



?>
