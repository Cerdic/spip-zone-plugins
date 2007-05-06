<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
if ($var_color=_request('var_color')) {
	include_spip('inc/cookie');
	spip_setcookie('sedna_color', $var_color, time()+365*24*3600);
	$_COOKIE['sedna_color'] = $var_color;
}
$GLOBALS['marqueur'].=isset($_COOKIE['sedna_color'])?(":".$_COOKIE['sedna_color']):"";
function sedna_utils(){
	$GLOBALS['forcer_lang']= true;

	// Descriptifs : affiches ou masques ?
	// l'accessibilite sans javascript => affiches par defaut
	if ($_COOKIE['sedna_style'] == 'masquer') {
		$class_desc = "desc_masquer";
	} else {
		$class_desc = "desc_afficher";
	}
	// l'identifiant du lien est fonction de son url et de sa date
	// ce qui permet de reperer les "updated" *et* les doublons
	include_spip('inc/filtres');
	function afficher_lien(
		$id_syndic_article,
		$id_lien,
		$id_syndic,
		$date,
		$url,
		$titre,
		$lesauteurs,
		$desc,
		$lang
		) {
		static $vu, $lus, $ferme_ul, $id, $iddesc;
		global $ex_syndic, $class_desc;

		// Articles a ignorer
		if (!_request('id_syndic')
		AND $_COOKIE['sedna_ignore_'.$id_syndic])
			return;

		// initialiser la liste des articles lus
		if (!is_array($lus))
			$lus = array_flip(split('-', '-'.$_COOKIE['sedna_lu']));

		if ($vu[$id_lien]++) return;

		// regler la classe des liens, en fonction du cookie sedna_lu
		$class_link = $lus[$id_lien] ? 'vu' : '';

		if (unique(substr($date,0,10)))
			$affdate = '<h1 class="date">'
				.jour($date).' '.nom_mois($date).'</h1>';


		// indiquer un intertitre si on change de source ou de date
		if ($affdate OR ($id_syndic != $ex_syndic)) {
			echo $ferme_ul; $ferme_ul="</ul>\n";
			echo $affdate;
		}

		// Suite intertitres
		if ($affdate OR ($id_syndic != $ex_syndic)) {
			echo "<h2 id='site${id_syndic}_".(++$id)."'
			onmouseover=\"getElementById('url".$id."').className='urlsiteon';\"
			onmouseout=\"getElementById('url".$id."').className='urlsite';\"
			>";
			echo "<a href=\"".parametre_url(self(),'id_syndic',$id_syndic);
			if ($age = intval($GLOBALS['age']))
				echo "&amp;age=$age";
			echo "\">".$GLOBALS['nom_site_'.$id_syndic]
				."</a>";
			echo " <a class=\"urlsite\"
					href=\""
					.$GLOBALS['url_site_'.$id_syndic]
					.'" id="url'.$id.'">'
					.$GLOBALS['url_site_'.$id_syndic]
					."</a>";
			echo "</h2>\n<ul>\n";
			$ex_syndic = $id_syndic;
		}

		echo "<li class='hentry'";
		if (!$_GET['id_syndic'] AND !strlen($_GET['recherche']))
			echo " id='item${id_syndic}_${id_syndic_article}'";
		echo "	onmousedown=\"jai_lu('$id_lien');\">\n",
#		"<small>".affdate($date,'H:i')."</small>",
		"<abbr class='published updated'
		title='".date_iso($date)."'>".affdate($date,'H:i')."</abbr>", 
		"<div class=\"titre\">",
		"<a href=\"$url\"
			title=\"$url\"
			class=\"link$class_link\"
			id=\"news$id_lien\"
			rel=\"bookmark\"";
		if ($lang) echo " hreflang=\"$lang\"";
		echo ">",
		"<span class=\"entry-title\">", # le "title" du microformat hAtom.hfeed.hentry
		$titre, "</span></a>",
		$lesauteurs,
		"\n<span class=\"source\"><a href=\"",
		$GLOBALS['url_site_'.$id_syndic]."\">",
		$GLOBALS['nom_site_'.$id_syndic]."</a></span>\n",
		"</div>\n";

		if ($desc)
			echo "<div class=\"desc\">",
			"<div class=\"$class_desc\" id=\"desc_".(++$iddesc)."\">\n",
			"<span class=\"entry-summary\">", $desc, "</span>\n",
			'</div></div>';
		

		echo "\n</li>\n";
	}
	
	// Si synchro active il faut comparer le contenu du cookie et ce
	// qu'on a stocke dans le champ spip_auteurs.sedna (a creer au besoin)
	$synchro = '';
	if ($_COOKIE['sedna_synchro'] == 'oui'
	AND $id = $GLOBALS['auteur_session']['id_auteur']) {
		// Recuperer ce qu'on a stocke
		if (!$s = spip_query("SELECT sedna FROM spip_auteurs
		WHERE id_auteur=$id")) {
			// creer le champ sedna si ce n'est pas deja fait
			spip_query("ALTER TABLE spip_auteurs
			ADD sedna TEXT NOT NULL DEFAULT ''");
		}
		$champ = spip_fetch_array($s);
		$champ = $champ['sedna'];
		// mixer avec le cookie en conservant un ordre chronologique
		if ($_COOKIE['sedna_lu'] <> $champ) {
			$lus_cookie = preg_split(',[- +],',$_COOKIE['sedna_lu']);
			$lus_champ = preg_split(',[- +],',$champ);
			$lus = array();
			while (count($lus_cookie) OR count($lus_champ)) {
				if ($a = array_shift($lus_cookie))
					$lus[$a] = true;
				if ($a = array_shift($lus_champ))
					$lus[$a] = true;
			}
			$lus = substr(join('-', array_keys($lus)),0,3000); # 3ko maximum
			// Mettre la base a jour
			spip_query("UPDATE spip_auteurs SET sedna='"
				.addslashes($lus)."'
				WHERE id_auteur=$id");
			$synchro = ' *';

			// Si le cookie n'est pas a jour, on l'update sur le brouteur
			if ($lus <> $_COOKIE['sedna_lu']) {

				spip_setcookie('sedna_lu', $lus,
					time()+365*24*3600);
					$_COOKIE['sedna_lu'] = $lus;
				// Signaler que la synchro a eu lieu
				$synchro = ' &lt;&lt;';
			}
		}
	}
	// forcer le refresh ?
	if ($id = intval(_request('refresh'))) {
		include_ecrire('inc_syndic');
		spip_touch(_DIR_TMP.'syndic.lock');
		syndic_a_jour($id);
	}

	// Calcul du delais optimal (on est tjs a jour, mais quand meme en cache)
	// valeur max = 15 minutes (900s) (et on hacke #ENV{max_maj} pour affichage
	// de "Derniere syndication..." en pied de page).
	$GLOBALS['sedna_max_maj'] = @filemtime(_DIR_TMP.'syndic.lock');
	if ($GLOBALS['sedna_max_maj'] > lire_meta('derniere_modif')) {
		include_spip('inc/meta');
		ecrire_meta('derniere_modif', $GLOBALS['sedna_max_maj']);
		ecrire_metas();
	}
	$GLOBALS['sedna_max_maj'] = date('Y-m-d H:i:s', $GLOBALS['sedna_max_maj']); # format SPIP
}

?>