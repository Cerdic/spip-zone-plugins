<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/webRadio_radio');
include_spip('base/abstract_sql');

// affichage du player
function formulaireTester($id_document, $fichier) {

	return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="200" height="20" id="dewplayer" align="middle"><param name="wmode" value="transparent"><param name="allowScriptAccess" value="sameDomain" /><param name="movie" value="'._DIR_PLUGIN_WEBRADIO.'dewplayer.swf?mp3='.$fichier.'&amp;showtime=1" /><param name="quality" value="high" /><param name="bgcolor" value="FFFFFF" /><embed src="'._DIR_PLUGIN_WEBRADIO.'dewplayer.swf?mp3='.$fichier.'&amp;showtime=1" quality="high" bgcolor="FFFFFF" width="200" height="20" name="dewplayer" wmode="transparent" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
}

// bouton ajouter
function formulaireAjouter($id_document) {
	return '<form method="POST" action="'.generer_url_action("webRadio_radio_action", "arg=ajouter-$id_document").'">'
		.'<input type="hidden" name="redirect" value="'.generer_url_ecrire("webradio_radio").'"/>'
		.'<input type="image" src="'._DIR_PLUGIN_WEBRADIO.'img_pack/ajouter.png" title="'._T('webradio:ajouter_playlist').'"/>'
		.'('._T('webradio:ajouter_playlist').')'
		.'</form>';
}

// bonton retirer
function formulaireRetirer($id_document) {
	return '<form method="POST" action="'.generer_url_action("webRadio_radio_action", "arg=retirer-$id_document").'">'
		.'<input type="hidden" name="redirect" value="'.generer_url_ecrire("webradio_radio").'"/>'
		.'<input type="image"src="'._DIR_PLUGIN_WEBRADIO.'img_pack/retirer.png" title="'._T('webradio:retirer_playlist').'"/>'
		.'('._T('webradio:retirer_playlist').')'
		.'</form>';
}

// champs input titre
function formulaireTitre($id_document,$titre) {
	return '<form method="POST" action="'.generer_url_action("webRadio_radio_action", "arg=changerContenu-$id_document").'">'
		.'<input type="hidden" name="redirect" value="'.generer_url_ecrire("webradio_radio").'"/>'
		.'<input type="text" name="titre" value="'.$titre.'" size="30" /><br />';
		
}

// champ input descriptif
function formulaireDescriptif($id_document,$descriptif) {
	return '<textarea name="descriptif" cols="30" rows="5">'.$descriptif.'</textarea><br />'
		.'&nbsp;<input type="submit" value="ok"/>'
		.'</form>';
}

// la totalité du bloc
function controle_un_radio($row) {
	$texte = '<div style="width: 500px;border: 1px solid #000; background: #fff;margin-bottom: 10px;">'
		.'<div style="color: #e1cccc; font-size: 1.2em; background:#8a5e99;padding-left: 10px; padding-bottom: 5px;">';
	if (empty($row['titre'])) {
		$texte .= '<b>'._T('webradio:pas_de_titre').'</b>';
	}
	else {
		$texte .= '<b>'.$row['titre'].'</b>';
	}
	$texte .= '</div>'
		.'<div style="background: #fff;padding-left : 5px; padding-right: 5px;">'
		.'<table>'
		.'<tr><td>';

	$r = sql_fetch(sql_select( // recuperation de l'id article associe
		Array('id_article'),
		Array('spip_documents_articles'),
		Array('id_document = '.sql_quote($row['id_document']))
	));

	$texte .= '<a href="'.generer_url_ecrire('articles','id_article='.$r['id_article']).'">'._T('webradio:visualiser_article').'</a><br />'
		._T('webradio:titre').' : '.formulaireTitre($row['id_document'],$row['titre']).'<br />'
		._T('webradio:descriptif').' : <br />'.formulaireDescriptif($row['id_document'],$row['descriptif']).'<br />'
		.'</td><td>'
		.'<ul style="float: right; width: 210px; text-align:center;list-style-type: none;">'
		. '<li style="margin-bottom: 5px;">'.formulaireTester($row['id_document'],$row['fichier']).'</li>'
		. '<li style="border:1px solid #000; margin-bottom: 5px;">'.formulaireAjouter($row['id_document']).'</li>'
		. '<li style="border:1px solid #000; margin-bottom: 5px;">'.formulaireRetirer($row['id_document']).'</li>'
		.'</ul>'
		.'</td></tr></table>'
		.'<br style="clear:both;"/>'
		.'</div></div>';
	return $texte;
}

// n'affiche que les 7 en train d'etre parcouru
function affiche_tranche_radio($debut, $i, $pack, $query) {

  $res = '';
  while ($row = spip_fetch_array($query)) {
	if (($i>=$debut) AND ($i<($debut + $pack)))
		$res .= controle_un_radio($row);
	$i ++;
  }
  return $res;
}

// affiche la playlist
function affiche_playlist_radio() {
	echo '<div style="background: #8a5e99; border: 1px solid #000; -moz-border-radius:7px;color: #fff;">';
	echo '<b>La playlist</b><br />';
	
	$query = sql_select(
		array('id_document', 'titre','fichier'),
		array('spip_documents'),
		array('playlist = '.sql_quote('oui'))
	);
	
	echo '<ul>';
	while ($row = sql_fetch($query)) {
		echo '<li>'.$row['titre'].'</li>';
	}
	echo '</ul></div>';
}


function exec_webradio_radio() {
	global $debut_id_radio, $type, $debut, $admin;
	$debut = intval($debut);

	$where = 'id_type = 17'; // where qui selectionne nos fichiers mp3

	// Si un id_controle_radio est demande, on adapte le debut
 	if ($debut_id_radio = intval($debut_id_radio)) {
 		$debut = sql_fetch(sql_query($q = "SELECT COUNT(*) AS n FROM spip_documents WHERE $where "));
 		$debut = $debut['n'];
 	}

	$pack = 7;	// nb d'items affiches par page
	$enplus = 200;	// intervalle affiche autour du debut
	$limitdeb = ($debut > $enplus) ? $debut-$enplus : 0;
	$limitnb = $debut + $enplus - $limitdeb;
	$args =  (!$id_rubrique ? "" : "id_rubrique=$id_rubrique&") . 'type=';
	
	if (!preg_match('/^\w+$/', $type)) $type = "public";
	
	$args = 'type=';
	
	$query = sql_select(
		array('id_document', 'titre', 'descriptif','fichier','playlist'),
		array('spip_documents'),
		array('id_type = '.sql_quote('17').' ORDER BY id_document DESC LIMIT '.$limitdeb.', '.$limitnb)
	);
	
	$ancre = 'webRadio_radio';
	$mess = affiche_navigation_radio('webradio_radio', $args . $type, $debut, $limitdeb, $pack, $ancre, $query)
		. affiche_tranche_radio($debut, $limitdeb, $pack, $query);

	if (_request('var_ajaxcharset') AND !$droit) {
		ajax_retour($mess);
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('webradio:webradio_radio_titre'), "radio", "webradio-radio");

		echo "<br /><br /><br />";
		echo gros_titre(_T('webradio:webradio_radio_titre'),'',false);

		if ($admin AND $connect_statut != "0minirezo") {
			echo _T('avis_non_acces_page');
			exit;
		}

		debut_gauche();

		debut_boite_info();
		echo _T('webradio:infos');
		echo '<a href="'.generer_url_ecrire('cfg','cfg=webRadio').'">'._T('webradio:configuration').'</a>';
		fin_boite_info();


		echo '<br />';
		affiche_playlist_radio();
		echo '<br />';

		debut_droite();

	
		echo "<div id='$ancre' class='serif2'>$mess</div>";
	
		echo fin_gauche(), fin_page();
	}
}

?>