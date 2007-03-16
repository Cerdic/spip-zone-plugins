<?php
 /**************************************************************************\
*  SPIP, Systeme de publication pour l'internet                              *
*                                                                            *
*  Copyright (c) 2001-2007                                                   *
*  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James     *
*                                                                            *
*  Ce script fait partie d'un logiciel libre distribue sous licence GNU/GPL. *
*  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.      *
 \**************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@exec_auteurs_dist
function exec_auteurs()
{
	include_spip('inc/presentation');
	include_spip('inc/navigation_liste');

	(($tri = preg_replace('/\W/', '', _request('tri'))) || ($tri='nom'));
	$statut = preg_replace('/\W/', '', _request('statut'));
	
	$len_debut_etape = strlen(_request('debut_etape'))+1;
	
	$navig = new NavigationListe(array(
		'requete_liste' => requete_auteurs($tri, $statut),
//		'callback_liste' => 'complement_auteur',
		'requete_comptage' => 'SELECT COUNT(*) FROM spip_auteurs',
		'requete_etapes' =>
			'SELECT DISTINCT UPPER(LEFT(nom,'.$len_debut_etape.')) etape, COUNT(*) compte FROM spip_auteurs
			'.($len_debut_etape>1?'WHERE nom LIKE "'._request('debut_etape').'%"':'').'
			 GROUP BY etape ORDER BY etape',
		'debut_etape' => _request('debut_etape'),
		'max_par_page' => 30,
		'debut' => intval(_request('debut')),
		'fragment' => intval(_request('fragment')),
		'contenu_ligne' => 'ligne_auteur',
		'url' => '?exec=auteurs'
	));
	
	$res = $navig->show();
	//var_dump($navig); 
/*
	$result = requete_auteurs($tri, $statut);
	$nombre_auteurs = spip_num_rows($result);
	$max_par_page = 30;
	$debut = intval(_request('debut'));
	if ($debut > $nombre_auteurs - $max_par_page)
		$debut = max(0,$nombre_auteurs - $max_par_page);

	list($auteurs, $lettre)= lettres_d_auteurs($result, $debut, $max_par_page, $tri);

	$res = auteurs_tranches(afficher_n_auteurs($auteurs), $debut, $lettre, $tri, $statut, $max_par_page, $nombre_auteurs);
*/

	if (_request('var_ajaxcharset')) {
		$ret = ajax_retour($res);
	} else {
		$ret =
		  pipeline('exec_init',array('args'=>array('exec'=>'auteurs'),'data'=>''))

		. bandeau_auteurs($tri, $statut)

		. '<div id="auteurs">' . $res . '</div>'
		. pipeline('affiche_milieu',array('args'=>array('exec'=>'auteurs'),'data'=>''))
		. fin_gauche() . fin_page();
	}
	echo $ret;
}

function ligne_auteurs($ligne = array(), $pos = 0, $reste = 0)
{
	static $formater_auteur = '';
	($formater_auteur || ($formater_auteur = charger_fonction('formater_auteur', 'inc')));
	global $connect_statut, $options;

	if ($ligne['statut'] == '0minirezo') {
		$count = spip_fetch_array(spip_query(
			'SELECT COUNT(*) FROM spip_auteurs_rubriques WHERE id_auteur='
			. $ligne['id_auteur']), SPIP_NUM);
		$ligne['restreint'] = $count[0];
	}

	list($s, $mail, $nom, $w, $p) = $formater_auteur($ligne['id_auteur']);
	return "\n<tr style='background-color: #eeeeee;'>"
	. "\n<td style='border-top: 1px solid #cccccc;'>"
	. $s
	. "</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
	. $mail
	. "</td><td class='verdana1' style='border-top: 1px solid #cccccc;'>"
	. $nom
	. ((isset($row['restreint']) AND $ligne['restreint'])
	   ? (" &nbsp;<small>"._T('statut_admin_restreint')."</small>")
	   : '')
	 ."</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
	 . $w
	 . "</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
	 . $p
	.  "</td></tr>\n";
}

function requete_auteurs($tri, $statut)
{
	global $connect_statut, $spip_lang, $connect_id_auteur;

	//
	// Construire la requete
	//

	// si on n'est pas minirezo, ignorer les auteurs sans article publie
	// sauf les admins, toujours visibles.
	// limiter les statuts affiches
	if ($connect_statut == '0minirezo') {
		if ($statut) {
			$sql_visible = "aut.statut IN ('$statut')";
			$tri = 'nom';
		} else {
			$sql_visible = "aut.statut IN ('0minirezo','1comite','5poubelle')";
		}
	} else {
		$sql_visible = "(
			aut.statut = '0minirezo'
			OR art.statut IN ('prop', 'publie')
			OR aut.id_auteur=$connect_id_auteur
		)";
	}

	$sql_sel = '';

	// tri
	switch ($tri) {
	case 'nombre':
		$sql_order = ' compteur DESC, unom';
		break;

	case 'statut':
		$sql_order = ' statut, unom';
		break;

	case 'nom':
		$sql_order = ' unom';
		break;

	case 'multi':
	default:
		$sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
		$sql_order = " multi";
	}

	return "SELECT
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.nom AS nom,
		UPPER(aut.nom) AS unom,
		count(lien.id_article) as compteur
		$sql_sel
		FROM spip_auteurs as aut
		LEFT JOIN spip_auteurs_articles AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_articles AS art ON (lien.id_article = art.id_article)
		WHERE $sql_visible
		GROUP BY aut.id_auteur
		ORDER BY $sql_order";
}

// http://doc.spip.org/@bandeau_auteurs
function bandeau_auteurs($tri, $statut)
{
	global $options, $spip_lang_right, $connect_id_auteur,
		$connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	return ($statut == '6forum' ?
		$commencer_page(_T('titre_page_auteurs'), "auteurs", "redacteurs") :
		$commencer_page(_T('info_auteurs_par_tri', array('partri' =>
			 $tri=='nombre' ? _T('info_par_nombre_articles') :
			($tri=='statut' ? _T('info_par_statut') : _T('info_par_nom')))),
			"auteurs", "redacteurs"))
	. debut_gauche(null, true)

	. debut_boite_info(true)
	. "\n<p class='arial1'>"
	. ($statut == '6forum' ? _T('info_gauche_visiteurs_enregistres') : _T('info_gauche_auteurs'))
	. '</p>'
	. ($connect_statut == '0minirezo' ? "\n<br />". _T('info_gauche_auteurs_exterieurs') : '')
	. fin_boite_info(true)
	. pipeline('affiche_gauche',array('args'=>array('exec'=>'auteurs'),'data'=>''))

	. bloc_des_raccourcis(
		($connect_statut == '0minirezo' ?

		($connect_toutes_rubriques ?
			icone_horizontale(_T('icone_creer_nouvel_auteur'), generer_url_ecrire("auteur_infos", 'new=oui'), "auteur-24.gif", "creer.gif", false) : '') : '')

		. icone_horizontale(_T('icone_informations_personnelles'), generer_url_ecrire("auteur_infos","id_auteur=$connect_id_auteur"), "fiche-perso-24.gif","rien.gif", false)

		. (spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE statut='6forum' LIMIT 1")) ?
			($statut == '6forum' ?
				icone_horizontale (_T('icone_afficher_auteurs'), generer_url_ecrire("auteurs"), "auteur-24.gif", "", false) :
				icone_horizontale (_T('icone_afficher_visiteurs'), generer_url_ecrire("auteurs","statut=6forum"), "auteur-24.gif", "", false)
			) : '')
		)
	. creer_colonne_droite(null, true)
	. pipeline('affiche_droite',array('args'=>array('exec'=>'auteurs'),'data'=>''))
	. debut_droite(null, true)

	. "\n<br />"
	. gros_titre(_T($statut == '6forum' ? 'info_visiteurs' : 'info_auteurs'), '', false)
	. "\n<br />";
}

// http://doc.spip.org/@auteurs_tranches
function auteurs_tranches($auteurs, $debut, $lettre, $tri, $statut, $max_par_page, $nombre_auteurs)
{
	global $options, $spip_lang_right;

	$res ="\n<tr style='background-color: #dbe1c5'>"
	. "\n<td style='width: 20px'>";

	if ($tri=='statut')
  		$res .= http_img_pack('admin-12.gif','', " class='lang'");
	else {
	  $t =  _T('lien_trier_statut');
	  $res .= auteurs_href(http_img_pack('admin-12.gif', $t, "class='lang'"),'tri=statut', " title=\"$t\"");
	}

	$res .= "</td><td style='width: 20px'></td><td colspan='2'>";

	if ($tri == '' OR $tri=='nom')
		$res .= '<b>'._T('info_nom').'</b>';
	else
		$res .= auteurs_href(_T('info_nom'), "tri=nom", " title='"._T('lien_trier_nom'). "'");


	$res .= "</td><td>";

	if ($statut != '6forum') {
		if ($tri=='nombre')
			$res .= '<b>'._T('info_articles').'</b>';
		else
			$res .= auteurs_href(_T('info_articles_2'), "tri=nombre", " title=\""._T('lien_trier_nombre_articles'). '"');
	}

	$res .= "</td></tr>\n";

	if ($nombre_auteurs > $max_par_page) {
		$res .= "\n<tr style='background-color: white'><td class='arial1' colspan='5'>";

		for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
			if ($j > 0) 	$res .= " | ";

			if ($j == $debut)
				$res .= "<b>$j</b>";
			else if ($j > 0)
				$res .= auteurs_href($j, "tri=$tri&statut=$statut&debut=$j");
			else
				$res .= auteurs_href('0', "tri=$tri&statut=$statut");
			if ($debut > $j  AND $debut < $j+$max_par_page){
				$res .= " | <b>$debut</b>";
			}
		}
		$res .= "</td></tr>\n";

		if ($tri == 'nom' AND $options == 'avancees') {
			$res .= "\n<tr style='background-color: white'><td class='arial11' colspan='5'>";
			foreach ($lettre as $key => $val) {
				if ($val == $debut)
					$res .= "<b>$key</b>\n";
				else
					$res .= auteurs_href($key, "tri=$tri&statut=$statut&debut=$val") . "\n";
			}
			$res .= "</td></tr>\n";
		}
	}

	$nav = '';
	$debut_suivant = $debut + $max_par_page;
	if ($debut_suivant < $nombre_auteurs OR $debut > 0) {
		$nav = "\n<table id='bas' style='width: 100%' border='0'>"
		. "\n<tr style='background-color: white'><td align='left'>";

		if ($debut > 0) {
			$debut_prec = max($debut - $max_par_page, 0);
			$nav .= auteurs_href('&lt;&lt;&lt;',"tri=$tri&debut=$debut_prec&statut=$statut");
		}
		$nav .= "</td><td style='text-align: $spip_lang_right'>";
		if ($debut_suivant < $nombre_auteurs) {
			$nav .= auteurs_href('&gt;&gt;&gt;',"tri=$tri&debut=$debut_suivant&statut=$statut");
		}
		$nav .= "</td></tr></table>\n";
	}

	return 	debut_cadre_relief('auteur-24.gif',true)
	. "\n<table  class='arial2' border='0' cellpadding='2' cellspacing='0' style='width: 100%; border: 1px solid #aaaaaa;'>\n"
	. $res
	. $auteurs
	. "</table>\n<br />"
	.  $nav
	. fin_cadre_relief(true);
}

// http://doc.spip.org/@auteurs_href
function auteurs_href($clic, $args='', $att='')
{
	$h = generer_url_ecrire('auteurs', $args);
	$a = 'auteurs';

	if (_SPIP_AJAX === 1 )
		$att .= ("\nonclick=" . ajax_action_declencheur($h,$a));

	return "<a href='$h#$a'$att>$clic</a>";
}

// http://doc.spip.org/@afficher_n_auteurs
function afficher_n_auteurs($auteurs) {
	global $connect_statut, $options;

	$res = '';
	$formater_auteur = charger_fonction('formater_auteur', 'inc');
	foreach ($auteurs as $row) {

		list($s, $mail, $nom, $w, $p) = $formater_auteur($row['id_auteur']);
		$res .= "\n<tr style='background-color: #eeeeee;'>"
		. "\n<td style='border-top: 1px solid #cccccc;'>"
		. $s
		. "</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
		. $mail
		. "</td><td class='verdana1' style='border-top: 1px solid #cccccc;'>"
		. $nom
		. ((isset($row['restreint']) AND $row['restreint'])
		   ? (" &nbsp;<small>"._T('statut_admin_restreint')."</small>")
		   : '')
		 ."</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
		 . $w
		 . "</td><td class='arial1' style='border-top: 1px solid #cccccc;'>"
		 . $p
		.  "</td></tr>\n";
	}
	return $res;
}

?>
