<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/navigation_modules');
include_spip ('inc/voir_adherent'); // pour voir_adherent_infos

function exec_adherents() {

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$url_association = generer_url_ecrire('association');
		$url_adherents = generer_url_ecrire('adherents');
		$url_edit_relances = generer_url_ecrire('edit_relances');

		//debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		association_onglets(_T('asso:titre_onglet_membres'));

		echo debut_gauche("",true);

		$critere = request_statut_interne(); // peut appeler set_request
		$statut_interne = _request('statut_interne');

		echo debut_boite_info(true);

		echo association_icone(_T('asso:menu2_titre_relances_cotisations'),  $url_edit_relances, 'ico_panier.png');
		echo '<p>'._T('asso:adherent_liste_legende').'</p>';

		// TOTAUX

		echo '<div><strong>'._T('asso:adherent_liste_nombre').'</strong></div>';
		$nombre = $nombre_total = 0;
		$membres = $GLOBALS['association_liste_des_statuts'];
		array_shift($membres); // ancien membre
		foreach ($membres as $statut) {
			$nombre = sql_countsel('spip_asso_membres', "statut_interne='$statut'");
			echo '<div style="float:right;text_align:right">'.$nombre.'</div>';
			echo '<div>'._T('asso:adherent_liste_nombre_'.$statut).'</div>';
			$nombre_total += $nombre;
		}
		echo '<div style="float:right;text_align:right">'.$nombre_total.'</div>';
		echo '<div>'._T('asso:adherent_liste_nombre_total').'</div>';

		echo association_date_du_jour();

		echo fin_boite_info(true);

		echo debut_cadre_enfonce('',true),
		  '<h3 style="text-align:center;">',
		  _T('plugins_vue_liste'), '</h3>',
		  adherents_table(),
		  fin_cadre_enfonce(true);

		echo debut_droite("",true);

		echo debut_cadre_relief("", true, "", $titre = _T('asso:adherent_titre_liste_actifs'));

		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2'>\n";
		echo "<tr>";

		// PAGINATION ALPHABETIQUE
		echo '<td>';

		$lettre = _request('lettre');
		if (!$lettre) { 
			$lettre = "%"; 
		}

		$query = sql_select("upper( substring( nom_famille, 0, 1 ) )  AS init", 'spip_asso_membres', '',  'init', 'nom_famille, id_auteur');

		while ($data = sql_fetch($query)) {
			$i = $data['init'];
			if($i==$lettre) {
				echo ' <strong>'.$i.'</strong>';
			} else {
				$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne&lettre=$i");
				echo " <a href='$h'>$i</a>\n";
			}
		}
		if ($lettre == "%") { 
			echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; 
		} else {
		$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne");
		echo "\n<a href='$h'>"._T('asso:adherent_entete_tous').'</a>'; }

		// FILTRES
		echo '</td><td style="text-align:right;">';

		//Filtre ID
		$id = intval(_request('id'));
		if (!$id) {
			$id = _T('asso:adherent_libelle_id_auteur');
		} else {
			$critere = "A.id_auteur=$id";
		}

		echo "\n<form method='post' action='".$url_adherents."'><div>";
		echo '<input type="text" name="id"  class="fondl" style="padding:0.5px" onfocus=\'this.value=""\' size="10"  value="'. $id .'" onchange="form.submit()" />';
		echo '</div></form>';
		echo '</td>';
		echo '<td style="text-align:right;">';

		//Filtre statut
		echo "\n<form method='post' action='".$url_adherents."'><div>\n";
		echo '<input type="hidden" name="lettre" value="'.$lettre.'" />';
		echo "\n<select name='statut_interne' class='fondl' onchange='form.submit()'>\n";
		echo "<option value='defaut'>"._T('asso:adherent_entete_statut_defaut')."</option>";
		foreach ($GLOBALS['association_liste_des_statuts'] as $statut) {
			echo "\n<option value='".$statut."'";
			if ($statut_interne==$statut) {
				echo ' selected="selected"';
			}
			echo '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
		}
		echo '</select>';
		echo '</div></form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';

		//Affichage de la liste
		echo adherents_liste(intval(_request('debut')), $lettre, $critere, $statut_interne);
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}

function adherents_liste($debut, $lettre, $critere, $statut_interne)
{

	$max_par_page = 30;

	if ($lettre)
		$critere .= " AND upper( substring( A.nom_famille, 0, 1 ) ) like '$lettre' ";
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$query = voir_adherent_infos("*", '', $critere, '', "A.nom_famille ", "$debut,$max_par_page" );
	$auteurs = '';
	while ($data = sql_fetch($query)) {
		$id_auteur = $data['id_auteur'];
		$class = $GLOBALS['association_styles_des_statuts'][$data['statut_interne']] . " border1";

		$logo = $chercher_logo($id_auteur, 'id_auteur');
		if ($logo) {
			$logo = '"'. $logo[0] .  '" width="60"';
		} else {
			$logo = '"'._DIR_PLUGIN_ASSOCIATION_ICONES.'ajout.gif"  width="10"' ;
		}
		if (empty($data["email"])) {
			$mail = $data["nom_famille"];
		} else {
			$mail = '<a href="mailto:'.$data["email"].'">'.$data["nom_famille"].'</a>';
		}
		if ($data['validite']=="") {
			$valide = '&nbsp;';
		} else {
			$valide = association_datefr($data['validite']);
		}

		$statut = $data['statut'];
		if (!$statut OR $statut == 'nouveau') 
			$statut = $data['bio'];

		switch($statut)	{
		case "0minirezo":
			$icone= "admin-12.gif"; break;
		case "1comite":
			$icone="redac-12.gif"; break;
		case "5poubelle":
			$icone="poubelle-12.gif"; break;
		case "6forum":
			$icone="visit-12.gif"; break;
		default :
			$icone='';#"adher-12.gif"; break;
		}
		$icone = !$icone ? strlen($statut) :  http_img_pack($icone,'','', _T('asso:adherent_label_modifier_visiteur'));

		$auteurs .= "\n<tr>"
		. '<td style="text-align:right;" class="'.$class. '">'
		. $id_auteur
		. '</td>'
		. '<td class="'.$class. '">'
		. "<img src=$logo" . ' alt="&nbsp;"  title="'
		. $data["nom_famille"].' '.$data["prenom"].'" />'
		. "</td>\n";
		if ($GLOBALS['association_metas']['civilite']=="on") 
			$auteurs .= '<td class="'.$class. '">'.$data['sexe']."</td>\n";
		$auteurs .= '<td class="'.$class. '">'
		. $mail . "</td>\n";
		if ($GLOBALS['association_metas']['prenom']=="on") 
			$auteurs .= '<td class="'.$class. '">'.$data["prenom"]."</td>\n";
		if ($GLOBALS['association_metas']['id_asso']=="on") 
			$auteurs .= '<td class="'.$class. '">'.$data["id_asso"]."</td>\n";
		$auteurs .= '<td class="'.$class. '">'
		. affiche_categorie($data["categorie"])
		. "</td>\n"
		. '<td class="'.$class. '">' . $valide . "</td>\n"
		. '<td class="'.$class. '">'
		. '<a href="'
		. generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur)
		.'">'
		. $icone
		."</a></td>\n"
		. '<td class="'.$class. '">'
		. association_bouton(_T('asso:adherent_label_ajouter_cotisation'), 'cotis-12.gif', 'ajout_cotisation','id='.$id_auteur)
		."</td>\n"
		. '<td class="'.$class. '">'
		. association_bouton(_T('asso:adherent_label_modifier_membre'), 'edit-12.gif', 'edit_adherent','id='.$id_auteur)
		."</td>\n"
		. '<td class="'.$class. '">'
		. association_bouton(_T('asso:adherent_label_voir_membre'), 'voir-12.png', 'voir_adherent','id='.$id_auteur)
		. "</td>\n"
		. '<td class="'.$class. '"><input name="delete[]" type="checkbox" value="'.$id_auteur.'" /></td>'
		. "</tr>\n";
	}

	$res = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. "<tr style='background-color: #DBE1C5;'>\n"
	. "<td><strong>"._T('asso:adherent_libelle_id_auteur')."</strong></td>\n"
	. "<th>"._T('asso:adherent_libelle_photo')."</th>\n";
	if ($GLOBALS['association_metas']['civilite']=="on") 
		$res .= "<th>"._T('asso:adherent_libelle_sexe')."</th>\n";
	$res .= "<th>"._T('asso:adherent_libelle_nom_famille')."</th>\n";
	if ($GLOBALS['association_metas']['prenom']=="on") 
		$res .= "<th>"._T('asso:adherent_libelle_prenom')."</th>\n";
	if ($GLOBALS['association_metas']['id_asso']=="on") 
		$res .= "<th>"._T('asso:adherent_libelle_id_asso')."</th>\n";
	$res .= "<th>"._T('asso:adherent_libelle_categorie')."</th>\n"
	. "<th>"._T('asso:adherent_libelle_validite')."</th>\n"
	. '<th colspan="4" style="text-align:center;">'._T('asso:adherent_entete_action')."</th>\n"
	. "<th>"._T('asso:adherent_entete_supprimer_abrev')."</th>\n"
	. '</tr>'
	. $auteurs
	. '</table>';

	//SOUS-PAGINATION

	$nombre_selection = sql_countsel('spip_asso_membres A', $critere);
	$pages = intval($nombre_selection/$max_par_page) + 1;

	if ($pages != 1) {
		$res .= "\n<div style='float:left; background-color:#DBE1C5;'>\n";
		for ($i=0; $i<$pages; $i++) {
			$position = $i * $max_par_page;
			if ($position == $debut) {
				$res .= '<strong>'.$position."</strong>\n";
			} else {
				$h = generer_url_ecrire('adherents', 'lettre='.$lettre.'&debut='.$position.'&statut_interne='.$statut_interne);
				$res .= "<a href='$h'>$position</a>\n";
			}
		}
		$res .= "\n</div>";
	}

	$res .= "\n<div style='float:right;'>\n"
	.  (!$auteurs ? '' : ('<input type="submit" value="'._T('asso:bouton_supprimer').'" class="fondo" />'))
	.  "\n</div>";

	return 	generer_form_ecrire('action_adherents', $res);

}

function affiche_categorie($c)
{
  return is_numeric($c)
    ? sql_getfetsel("valeur", "spip_asso_categories", "id_categorie=$c")
    : $c;
}

function adherents_table()
{
  $champs = $GLOBALS['association_tables_principales']['spip_asso_membres']['field'];
  $res = '';
  foreach ($champs as $k => $v) {
    $libelle = 'adherent_libelle_' . $k;
    $trad = _T('asso:' . $libelle);
    if ($libelle != str_replace(' ', '_', $trad)) {
      $res .= "<input type='checkbox' name='champs[$k]' />$trad<br />";
    }
  }
  return  generer_form_ecrire('pdf_adherents', $res, '', _T('asso:bouton_impression'));
}

?>
