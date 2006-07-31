<?php

function exec_flickr_bookmarklet_photo() {
  global $connect_id_auteur, $connect_statut;
  include_spip('inc/presentation');
  include_spip('inc/flickr_api');

  ///// debut de la page

  pipeline('exec_init',array('args'=>array('exec'=>'flickr_bookmarklet_photo'),'data'=>''));


  $id = _request('id');
  $secret = _request('secret');

  $photo_details = flickr_photos_getInfo($id,$secret);
  
  debut_page(_T('fpipr:ajouter_une_photo'),
			 "documents",
			 'plugin');

  debut_gauche();

  
  debut_boite_info();
  echo flickr_bookmarklet_info();
  fin_boite_info();

  echo '<div>&nbsp;</div>';
  echo icone(_T('icone_retour'), $photo_details->urls['photopage'], "article-24.gif", "rien.gif", '',false);

  debut_droite();
  echo '<div style="margin-top: 14px;" class="cadre-r">
<div style="position: relative;">
<div style="position: absolute; top: -16px; left: 10px;">
<img src="'.find_in_path('fpipr.gif').'"/>
</div>
</div>
<div style="overflow: hidden;" class="cadre-padding">';
  gros_titre(_T('fpipr:ajouter_une_photo'));


  echo '<div>';
  echo '<img style="float:right;" src="'.$photo_details->source('m').'"/>';
  echo '<span>'._T('fpipr:ajouter_une_photo_info',array('title'=>$photo_details->title,'owner'=>$photo_details->owner_username)).'</span>';
  echo '</div>';
  echo '<br clear="both"/>';

  if($connect_statut == '0minirezo')
	$requete = array('WHERE' => "", 'ORDER BY' => "date DESC");
  else {
	$rub = '';
	foreach(array_keys($connect_id_rubrique) as $id_rub) $rub .= 'OR id_rubrique='.$id_rub;
	$rub = substr($rub,3);
	$requete = array('WHERE' => "id_auteur='$connect_id_auteur' AND (statut='prop' OR statut='prepa' OR statut='poubelle')".(($rub)?" AND $rub":''), 'ORDER BY' => "date DESC");
  }
  echo '<form method="post" action="'.generer_action_auteur("flickr_ajouter_documents","article").'">';
  echo '<input type="hidden" name="type" value="article"/>';
  echo '<input type="hidden" name="photos[]" value="'."$id@#@$secret".'"/>';
  flickr_afficher_articles(_T('fpipr:choisir_un_article'),$requete);
  echo '<button type="submit">'._T('spip:bouton_valider').'</button>';
  echo '</form>';
  echo '</div>';
  fin_page();
}

//
// Afficher tableau d'articles
//
function flickr_afficher_articles($titre_table, $requete) {
  include_spip('inc/presentation');
	global $connect_id_auteur, $dir_lang;
	global $spip_display;
	global $spip_lang_left, $spip_lang_right;

	if (!isset($requete['FROM']))  $requete['FROM'] = 'spip_articles AS articles';

	$activer_statistiques = $GLOBALS['meta']["activer_statistiques"];
	$langue_defaut = $GLOBALS['meta']['langue_site'];
	// Preciser la requete (alleger les requetes)
	if (!isset($requete['SELECT'])) {
		$requete['SELECT'] = "articles.id_article, articles.titre, articles.id_rubrique, articles.statut, articles.date";
	}
	
	$jjscript["fonction"] = "afficher_articles";
	$jjscript["titre_table"] = $titre_table;
	$jjscript["requete"] = $requete;
	$jjscript["afficher_visites"] = false;
	$jjscript["afficher_auteurs"] = false;
	$jjscript = (serialize($jjscript));
	$hash = "0x".substr(md5($connect_id_auteur.$jjscript), 0, 16);

	$tmp_var = substr($hash, 2, 6);
	$javascript = "charger_id_url('" . generer_url_ecrire("ajax_page","fonction=sql&id_ajax_fonc=::id_ajax_fonc::::deb::", true) . "','$tmp_var')";

	if (!isset($requete['GROUP BY'])) $requete['GROUP BY'] = '';
	$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM " . $requete['FROM'] . ($requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '') . ($requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '')));
	if (! ($cpt = $cpt['n'])) return;
	if (isset($requete['LIMIT'])) $cpt = min($requete['LIMIT'], $cpt);

	$nb_aff = 1.5 * _TRANCHES;
	$deb_aff = intval(_request('t_' .$tmp_var));
	$tranches = '';
	if ($cpt > $nb_aff) {
		$nb_aff = (_TRANCHES); 
		$tranches = afficher_tranches_requete($cpt, 4, $tmp_var, $javascript, $nb_aff);
	}

	$res_proch = spip_query("SELECT id_ajax_fonc FROM spip_ajax_fonc WHERE hash=$hash AND id_auteur=$connect_id_auteur ORDER BY id_ajax_fonc DESC LIMIT 1");
	if ($row = spip_fetch_array($res_proch)) {
			$id_ajax_fonc = $row["id_ajax_fonc"];
	} else  {
			include_spip('base/abstract_sql');
			$id_ajax_fonc = spip_abstract_insert("spip_ajax_fonc", "(id_auteur, variables, hash, date)", "($connect_id_auteur, " . spip_abstract_quote($jjscript) . ", $hash, NOW())");
	}

	if (!$deb_aff) {

			echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";

			$id_img = "img_".$tmp_var;
			$texte_img = http_img_pack("searching.gif", "*", "style='visibility: hidden; float: $spip_lang_right' id = '$id_img'");

			bandeau_titre_boite2($texte_img.$titre_table, "article-24.gif");

			echo "<div id='$tmp_var'>";

	}
		
	$voir_logo = ($spip_display != 1 AND $spip_display != 4 AND $GLOBALS['meta']['image_process'] != "non");
		

	//echo "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";
	echo afficher_liste_debut_tableau(), str_replace("::id_ajax_fonc::", $id_ajax_fonc, $tranches);

	$result = spip_query("SELECT " . $requete['SELECT'] . " FROM " . $requete['FROM'] . ($requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '') . ($requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '') . ($requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '') . " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : ($requete['LIMIT'] ? $requete['LIMIT'] : "99999")));

	$table = array();
	while ($row = spip_fetch_array($result)) {
	  $table[]= flickr_afficher_articles_boucle($row,$langue_defaut, $voir_logo);
	}
	spip_free_result($result);

	$largeurs = array(11, '', 100,'');
	$styles = array('', 'arial2', 'arial1','');

	echo afficher_liste($largeurs, $table, $styles);
	echo afficher_liste_fin_tableau();
	echo "</div>";
		
	if (!$deb_aff) {
			echo "</div>";
	}
}

function flickr_afficher_articles_boucle($row, $langue_defaut, $voir_logo)
{
  global $connect_id_auteur, $dir_lang, $spip_lang_right;
  include_spip('inc/presentation');
	$vals = '';

	$id_article = $row['id_article'];
	$tous_id[] = $id_article;
	$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
	$id_rubrique = $row['id_rubrique'];
	$date = $row['date'];
	$statut = $row['statut'];
	if ($lang = $row['lang']) changer_typo($lang);

	// La petite puce de changement de statut
	$vals[] = puce_statut_article($id_article, $statut, $id_rubrique);

	// Le titre (et la langue)
	$s = "<div>";

	if (acces_restreint_rubrique($id_rubrique))
		$s .= http_img_pack("admin-12.gif", "", "width='12' height='12'", _T('titre_image_admin_article'));

	$s .= "<a href='" .generer_url_ecrire('articles',"id_article=$id_article")."'$descriptif$dir_lang style=\"display:block;\">";
	
	if ($voir_logo) {
		$logo_f = charger_fonction('chercher_logo', 'inc');
		if ($logo = $logo_f($id_article, 'id_article', 'on'))
			if ($logo = decrire_logo("id_article", 'on', $id_article, 26, 20, $logo))
				$s .= "<div style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</div>";
	}

	$s .= typo($titre);
	$s .= "</a>";
	$s .= "</div>";
	
	$vals[] = $s;

	// La date
	$vals[] = affdate_jourcourt($date);

	$input .= '<input type="radio" name="id" value="'.$id_article.'"/>';
	$input .= '<input type="hidden" name="redirect" value="'.generer_url_ecrire('articles',"id_article=$id_article").'"/>';

	$vals[] = $input;

	return $vals;
}


?>
