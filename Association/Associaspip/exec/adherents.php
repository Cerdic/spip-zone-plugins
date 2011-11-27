<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
	

include_spip('inc/navigation_modules');
	
function exec_adherents() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$url_association = generer_url_ecrire('association');
		$url_adherents = generer_url_ecrire('adherents');
		$url_edit_relances=generer_url_ecrire('edit_relances');
		
		//debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_membres'));
		
		echo debut_gauche("",true);
		
		/* recuperation des variables */
		$critere = request_statut_interne(); // peut appeler set_request
		$statut_interne = _request('statut_interne');
		$lettre= _request('lettre');

		/* bloc des raccourcis */
		$res.=association_icone(_T('asso:gerer_les_groupes'),  generer_url_ecrire("groupes"), 'annonce.gif',  '');
		echo bloc_des_raccourcis($res);

		echo debut_boite_info(true);

		echo association_icone(_T('asso:menu2_titre_relances_cotisations'),  $url_edit_relances, 'ico_panier.png');
		echo '<p>'._T('asso:adherent_liste_legende').'</p>'; 
		
		// TOTAUX	
		
		echo '<div><strong>'._T('asso:adherent_liste_nombre').'</strong></div>';
		$nombre= $nombre_total=0;
		$membres = $GLOBALS['association_liste_des_statuts'];
		array_shift($membres); // ancien membre
		foreach ($membres as $statut) {
			$nombre=sql_countsel('spip_asso_membres', "statut_interne='$statut'");
			echo '<div style="float:right;text_align:right">'.$nombre.'</div>';
			echo '<div>'._T('asso:adherent_liste_nombre_'.$statut).'</div>';
			$nombre_total += $nombre;
		}		
		echo '<div style="float:right;text_align:right">'.$nombre_total.'</div>';
		echo '<div>'._T('asso:adherent_liste_nombre_total').'</div>';
		
		echo association_date_du_jour();		

		echo fin_boite_info(true);	

	

		
		echo 	debut_cadre_enfonce('',true),
				recuperer_fond('prive/inc_cadre_etiquette'),
				fin_cadre_enfonce(true);
		
		
	
	
	

		/* on appelle ici la fonction qui calcule le code du formulaire/tableau de membres pour pouvoir recuperer la liste des membres affiches a transmettre a adherents_table pour la generation du pdf */
		//Filtre ID et groupe : si le filtre id est actif, on ignore le filtre groupe
		$id = intval(_request('id'));
		if (!$id) {
			$id = _T('asso:adherent_libelle_id_auteur');
			//Filtre groupe
			$id_groupe = intval(_request('id_groupe'));
		} else {
			$critere = "a.id_auteur=$id";
			$id_groupe = 0;
		}

		list($liste_id_auteurs, $code_liste_membres) = adherents_liste(intval(_request('debut')), $lettre, $critere, $statut_interne, $id_groupe);
	
		echo debut_cadre_enfonce('',true),
		  '<h3 style="text-align:center;">',
		  _T('plugins_vue_liste'), '</h3>',
		  adherents_table($liste_id_auteurs),
		  fin_cadre_enfonce(true);


		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", true, "", $titre = _T('asso:adherent_titre_liste_actifs'));		
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2'>\n";
		echo "<tr>";
		
		// PAGINATION ALPHABETIQUE
		echo '<td>';
		
		
		if (!$lettre) { $lettre = "%"; }
		
		$query = sql_select("upper( substring( nom_famille, 1, 1 ) )  AS init", 'spip_asso_membres', '',  'init', 'nom_famille, id_auteur');
		
		while ($data = sql_fetch($query)) {
			$i = $data['init'];
			if($i==$lettre) {
				echo ' <strong>'.$i.'</strong>';
			}
			else {
				$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne&lettre=$i");
				echo " <a href='$h'>$i</a>\n";
			}
		}
		if ($lettre == "%") { echo ' <strong>'._T('asso:adherent_entete_tous').'</strong>'; }
		else {
		$h = generer_url_ecrire('adherents', "statut_interne=$statut_interne");
		echo "\n<a href='$h'>"._T('asso:adherent_entete_tous').'</a>'; }
		
		// FILTRES
		//Filtre groupes
		echo '</td><td style="text-align:right;">';
		echo "\n<form method='post' action='".$url_adherents."'><div>";
		echo '<select class="fondl" name="id_groupe" onchange="form.submit()">';
		$qGroupes = sql_select('nom, id_groupe', 'spip_asso_groupes');
		echo '<option value="">'._T('asso:tous_les_groupes').'</option>';
		while ($groupe = sql_fetch($qGroupes)) {
			echo '<option value="'.$groupe['id_groupe'].'"';
			if ($id_groupe==$groupe['id_groupe']) echo ' selected="selected"';
			echo '>'.$groupe['nom'].'</option>';
		}
		echo '</select>';

		//Filtre ID
		echo '</td><td style="text-align:right;">';
		
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
			if ($statut_interne==$statut) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$statut).'</option>';
		}
		echo '</select>';
		echo '</div></form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		
		//Affichage de la liste
		echo $code_liste_membres;
		echo fin_cadre_relief(true);  
		echo fin_page_association();
	}
}

/* adherent liste renvoie un tableau des id des auteurs affiches et le code html */
function adherents_liste($debut, $lettre, $critere, $statut_interne, $id_groupe)
{

	$max_par_page=30;

	if ($lettre)
		$critere .= " AND upper( substring( nom_famille, 1, 1 ) ) like '$lettre' ";

	$jointure_groupe = "";
	if ($id_groupe) {
		$critere .= " AND c.id_groupe=$id_groupe ";
		$jointure_groupe = " LEFT JOIN spip_asso_groupes_liaisons c ON b.id_auteur=c.id_auteur ";
	}
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	include_spip('inc/filtres_images_mini');
	$query = sql_select('a.id_auteur AS id_auteur, b.email AS email, a.sexe, a.nom_famille, a.prenom, a.id_asso, b.statut AS statut, a.validite, a.statut_interne, a.categorie, b.bio AS bio','spip_asso_membres' .  " a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur $jointure_groupe", $critere, '', "nom_famille ", "$debut,$max_par_page" );
	$auteurs = '';
	$liste_id_auteurs = array();
	while ($data = sql_fetch($query)) {	
		$id_auteur=$data['id_auteur'];
		$liste_id_auteurs[] = $id_auteur;		
		$class = $GLOBALS['association_styles_des_statuts'][$data['statut_interne']] . " border1";
		
		$logo = $chercher_logo($id_auteur, 'id_auteur');
		if ($logo) {
			$logo = image_reduire($logo[0], 60);
		}else{
			$logo = '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'ajout.gif"  width="10"/>' ;
		}
		if (empty($data["email"])) { 
			$mail = $data["nom_famille"]; 
		} else {
			$mail = '<a href="mailto:'.$data["email"].'">'.$data["nom_famille"].'</a>';
		}
		if ($data['validite']==""){$valide = '&nbsp;';}else{$valide = association_datefr($data['validite']);}

		$statut = $data['statut'];
		if (!$statut OR $statut == 'nouveau') $statut = $data['bio'];

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

		$auteurs .= "\n<tr>";
		if ($GLOBALS['association_metas']['aff_id_auteur']=="on") {
			$auteurs .= '<td class="'.$class. '">'
			. $id_auteur
			. '</td>';
		}
		if ($GLOBALS['association_metas']['aff_photo']=="on") {
			$auteurs .= '<td class="'.$class. '">'
			. $logo
			. "</td>\n";
		}
		if ($GLOBALS['association_metas']['aff_civilite']=="on" && $GLOBALS['association_metas']['civilite']=="on") $auteurs .= '<td class="'.$class. '">'.$data['sexe']."</td>\n";
		$auteurs .= '<td class="'.$class. '">'
		. $mail . "</td>\n";
		if ($GLOBALS['association_metas']['aff_prenom']=="on" && $GLOBALS['association_metas']['prenom']=="on") $auteurs .= '<td class="'.$class. '">'.$data["prenom"]."</td>\n";
		if ($GLOBALS['association_metas']['aff_groupes']=="on") {
			$auteurs .= '<td class="'.$class. '">';
			$query_groupes = sql_select('g.nom as nom_groupe, g.id_groupe as id_groupe', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'l.id_auteur='.$id_auteur);
			if ($row_groupes = sql_fetch($query_groupes)) {
				$auteurs .= '<a href="'.generer_url_ecrire('voir_groupe', 'id='.$row_groupes['id_groupe']).'">'.$row_groupes['nom_groupe'].'</a>';
				while ($row_groupes = sql_fetch($query_groupes)) {
					$auteurs .= ', <a href="'.generer_url_ecrire('voir_groupe', 'id='.$row_groupes['id_groupe']).'">'.$row_groupes['nom_groupe'].'</a>';
				}
			}
			$auteurs .= '</td>';
		}

		if ($GLOBALS['association_metas']['aff_id_asso']=="on" && $GLOBALS['association_metas']['id_asso']=="on") $auteurs .= '<td class="'.$class. '">'.$data["id_asso"]."</td>\n";
		if ($GLOBALS['association_metas']['aff_categorie']=="on") {
			$auteurs .= '<td class="'.$class. '">'
			. affiche_categorie($data["categorie"])
			. "</td>\n";
		}
		if ($GLOBALS['association_metas']['aff_validite']=="on") {
			$auteurs .= '<td class="'.$class. '">' . $valide . "</td>";
		}
		$auteurs .= '<td class="'.$class. '">'
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
		. '<td class="'.$class. '"><input name="id_auteurs[]" type="checkbox" value="'.$id_auteur.'" /></td>'
		. "</tr>\n";
	}
	
	$res = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. "<tr style='background-color: #DBE1C5;'>\n";
	if ($GLOBALS['association_metas']['aff_id_auteur']=="on") {
		$res .= "<th><strong>"._T('asso:adherent_libelle_id_auteur')."</strong></th>";
	}
	if ($GLOBALS['association_metas']['aff_photo']=="on") {
		$res .= "<th>"._T('asso:adherent_libelle_photo')."</th>";
	}
	if ($GLOBALS['association_metas']['aff_civilite']=="on" && $GLOBALS['association_metas']['civilite']=="on") $res .= "<th>"._T('asso:adherent_libelle_sexe')."</th>";
	$res .= "<th>"._T('asso:adherent_libelle_nom_famille')."</th>\n";
	if ($GLOBALS['association_metas']['aff_prenom']=="on" && $GLOBALS['association_metas']['prenom']=="on") $res .= "<th>"._T('asso:adherent_libelle_prenom')."</th>";
	if ($GLOBALS['association_metas']['aff_groupes']=="on") {
		$res .= "<th>"._T('asso:adherent_libelle_groupes')."</th>";
	}
	if ($GLOBALS['association_metas']['aff_id_asso']=="on" && $GLOBALS['association_metas']['id_asso']=="on") $res .= "<th>"._T('asso:adherent_libelle_id_asso')."</th>";
	if ($GLOBALS['association_metas']['aff_categorie']=="on") {
		$res .= "<th>"._T('asso:adherent_libelle_categorie')."</th>";
	}
	if ($GLOBALS['association_metas']['aff_validite']=="on") {
		$res .= "<th>"._T('asso:adherent_libelle_validite')."</th>";
	}
	$res .= '<th colspan="4" style="text-align:center;">'._T('asso:adherent_entete_action')."</th>\n"
	. '<th><input title="'._T('asso:selectionner_tout').'" type="checkbox" id="selectionnerTous" onclick="var currentVal = this.checked; var checkboxList = document.getElementsByName(\'id_auteurs[]\'); for (var i in checkboxList){checkboxList[i].checked=currentVal;}" /></th>'
	. '</tr>'
	. $auteurs
	. '</table>';
	
	//SOUS-PAGINATION

	$nombre_selection=sql_countsel('spip_asso_membres', $critere);

	$pages=intval($nombre_selection/$max_par_page) + 1;
	
	if ($pages != 1)	{
		for ($i=0;$i<$pages;$i++)	{ 
		$position= $i * $max_par_page;
		if ($position == $debut)	{
			$res .= '<strong>'.$position."</strong>\n";
		}
		else {
			$h = generer_url_ecrire('adherents', 'lettre='.$lettre.'&debut='.$position.'&statut_interne='.$statut_interne);
			$res .= "<a href='$h'>$position</a>\n";
			}
		}	
	}
	
	$res .= "\n<div style='float:right;'>\n"
	.  (!$auteurs ? '' : ('<select name="action_adherents"><option value="" selected="">'._T('asso:choisir_action').'</option><option value="desactive">'.($statut_interne=='sorti'?_T('asso:reactiver_adherent'):_T('asso:desactiver_adherent')).'</option><option value="delete">'._T('asso:supprimer_adherent').'</option><option value="grouper">'._T('asso:rejoindre_groupe').'</option><option value="degrouper">'._T('asso:quitter_un_groupe').'</option></select><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" />'))
	. '<input type="hidden" name="statut_courant" value="'.$statut_interne.'" />'
	.  '</div>';

	return 	array($liste_id_auteurs, generer_form_ecrire('action_adherents', $res));
}

function affiche_categorie($c)
{
  return is_numeric($c) 
    ? sql_getfetsel("valeur", "spip_asso_categories", "id_categorie=$c")
    : $c;
}

function adherents_table($liste_id_auteurs)
{
	$champs = description_table('spip_asso_membres');
	$res = '';
	foreach ($champs['field'] as $k => $v) {
		if (!(($GLOBALS['association_metas']['civilite']!="on" && $k == 'sexe') OR ($GLOBALS['association_metas']['prenom']!="on" && $k == 'prenom') OR ($GLOBALS['association_metas']['id_asso']!="on" && $k == 'id_asso'))) {
			$libelle = 'adherent_libelle_' . $k;
			$trad = _T('asso:' . $libelle);
			if ($libelle != str_replace(' ', '_', $trad)) {
				$res .= "<input type='checkbox' name='champs[$k]' />$trad<br />";
			}
		}
	}
	/* on ajoute aussi le mail */
	$res .= "<input type='checkbox' name='champs[email]' />"._T('asso:email')."<br />";

	/* si le plugin coordonnees est actif, on ajoute l'adresse et le telephone */
	if (test_plugin_actif('COORDONNEES')) {
		$res .= "<input type='checkbox' name='champs[adresse]' />"._T('asso:adresse')."<br />";
		$res .= "<input type='checkbox' name='champs[telephone]' />"._T('asso:telephone')."<br />";
	}

	/* on fait suivre la liste des auteurs a afficher */
	$res .= "<input type='hidden' name='liste_id_auteurs' value='".serialize($liste_id_auteurs)."'/>";

	return  generer_form_ecrire('pdf_adherents', $res, '', _T('asso:bouton_impression'));
}
?>
