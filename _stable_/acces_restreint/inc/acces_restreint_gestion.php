<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('base/abstract_sql');

	function AccesRestreint_cree_zone(){
		$titre = addslashes(_request('titre'));
		$descriptif = addslashes(_request('descriptif'));
		$publique = (_request('publique')=='oui')?'oui':'non';
		$privee = (_request('privee')=='oui')?'oui':'non';
		if (strlen($titre)>0){
			$id_zone = spip_abstract_insert('spip_zones', "(titre,descriptif,publique,privee,maj)", "('$titre','$descriptif','$publique','$privee',NOW())");
			if ($id_zone && _request('auto_attribue_droits')=='oui'){
				global $connect_id_auteur;
				if (autoriser('modifier','zone'))
					spip_abstract_insert('spip_zones_auteurs', "(id_zone,id_auteur)", "($id_zone,$connect_id_auteur)");
			}
			return $id_zone;
		} 
		return 0;
	}
	function AccesRestreint_supprimer_zone(){
		$id_zone = intval(_request('supp_zone'));
		if ($id_zone){
			spip_query("DELETE FROM spip_zones WHERE id_zone='$id_zone'");
			spip_query("DELETE FROM spip_zones_rubriques WHERE id_zone='$id_zone'");
			spip_query("DELETE FROM spip_zones_auteurs WHERE id_zone='$id_zone'");
		}
		return 0;
	}

	function AccesRestreint_enregistrer_zone(){
		$titre = addslashes(_request('titre'));
		$descriptif = addslashes(_request('descriptif'));
		$publique = (_request('publique')=='oui')?'oui':'non';
		$privee = (_request('privee')=='oui')?'oui':'non';
		$id_zone = intval(_request('id_zone'));
		if (strlen($titre)>0 && $id_zone){
			spip_query("UPDATE spip_zones SET titre='$titre', descriptif='$descriptif', privee='$privee', publique='$publique' WHERE id_zone=$id_zone");
			// suppression de tous les liens zone-rubriques
			spip_query("DELETE FROM spip_zones_rubriques WHERE id_zone='$id_zone'");
			if (is_array($_POST['restrict'])){
				foreach(array_keys($_POST['restrict']) as $id){
					$id = intval($id);
					spip_abstract_insert('spip_zones_rubriques', "(id_zone,id_rubrique)", "('$id_zone','$id')");
				}
			}
		}
		return 0;
	}

	function AccesRestreint_formulaire_zone($id_zone, $titre, $descriptif, $publique, $privee){
		global $couleur_claire;
		echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
		echo _T('accesrestreint:titre_zones_acces');
		echo "</div>";
		echo "<p>";
		echo _T('accesrestreint:titre')."<br/>";
		echo "<input type='input' name='titre' value='".entites_html($titre)."' class='formo' />";
		echo "</p>";
		echo "<p>";
		echo _T('accesrestreint:descriptif')."<br/>";
		echo "<textarea name='descriptif' class='formo'>";
		echo entites_html($descriptif);
		echo "</textarea>";
		echo "</p>";
		
		echo "<p>";
		$checked = ($publique == 'oui') ? " checked='checked'" : "";
		echo "&nbsp; &nbsp; <input type='checkbox' name='publique' value='oui' id='zone_publique'$checked> ";
		echo "<label for='zone_publique'>"._T("accesrestreint:zone_restreinte_publique")."</label>";
		echo "<br />\n";
		if ($GLOBALS['spip_version_code']>=1.9206){
			$checked = ($privee == 'oui') ? " checked='checked'" : "";
			echo "&nbsp; &nbsp; <input type='checkbox' name='privee' value='oui' id='zone_privee'$checked> ";
			echo "<label for='zone_privee'>"._T("accesrestreint:zone_restreinte_espace_prive")."</label>";
		}
		echo "</p>";
		echo "</div>";
		return;
	}

	// Fonction de presentation
	function AccesRestreint_sous_menu_rubriques($id_zone, $root, $niv, &$data, &$enfants, &$liste_rub_dir, &$liste_rub, $type) {
		global $browser_name, $browser_version;
		static $decalage_secteur;
		global $couleur_claire;
		include_spip('inc/chercher_rubrique');
	
		// Si on a demande l'exclusion ne pas descendre dans la rubrique courante
		if ($exclus > 0
		AND $root == $exclus) return '';
	
		// en fonction du niveau faire un affichage plus ou moins kikoo
		
		$class = "";
		if ($restric = in_array($root,$liste_rub_dir))	$class = " class='selec_rub'";
	
		// le style en fonction de la profondeur
		$a = style_menu_rubriques($niv);
		$espace = array_pop($a);
		$style = "style='padding-left:".($niv*1)."em;";
		if ($restrictherit = in_array($root,$liste_rub))
			$style .= "background-color: $couleur_claire;";
		$style.= "'";

		// creer l'<option> pour la rubrique $root
		if (isset($data[$root])) # pas de racine sauf pour les rubriques
		{
			$r .= "<input type='checkbox' name='restrict[$root]' value='O' id='label_$root'";
			$r .= ($restric!==FALSE)?" checked='checked'":"";
			$r .= " />\n <label for='label_$root' >$espace".$data[$root]."</label>";
		}
			
		// et le sous-menu pour ses enfants
		$sous = '';
		if ($enfants[$root])
			foreach ($enfants[$root] as $sousrub)
				$sous .= AccesRestreint_sous_menu_rubriques($id_rubrique, $sousrub,
					$niv+1, $data, $enfants, $liste_rub_dir, $liste_rub, $type);

		if (strlen($sous)>0){
			$visible = (($restrictherit) OR (strpos($sous,"checked='checked'")!==FALSE));
			if ($visible)
				$r = bouton_block_visible("rub$root") . $r;
			else
				$r = bouton_block_invisible("rub$root") . $r;
			$r = "<div $class $style>" . $r;
			$r .= "</div>\n";

			if ($visible)
				$r .= debut_block_visible("rub$root");
			else
				$r .= debut_block_invisible("rub$root");
			$r .= $sous;
			$r .= fin_block();
		}
		else{
			$r = "<div $class$style>" . $r;
			$r .= "</div>\n";
		}
		
		// et voila le travail
		return $r;
	}

	// Le selecteur de rubriques en mode classique (menu)
	function AccesRestreint_selecteur_rubrique_html($id_zone) {
		$type = 'rubrique';
		$data = array();
		if ($type == 'rubrique')
			$data[0] = _T('info_racine_site');
		if ($type == 'auteur')
			$data[0] = '&nbsp;'; # premier choix = neant (rubriques restreintes)
	
		//
		// creer une structure contenant toute l'arborescence
		//
	
		# oblige les breves a resider a la racine
		if ($type == 'breve') $where = 'WHERE id_parent=0';
	
		$q = spip_query("SELECT
		id_rubrique, id_parent, titre, statut, lang, langue_choisie
		FROM spip_rubriques
		$where
		ORDER BY 0+titre,titre");
		while ($r = spip_fetch_array($q)) {
			// titre largeur maxi a 50
			$titre = couper(supprimer_tags(typo(extraire_multi(
				$r['titre']
			)))." ", 50);
			if ($GLOBALS['meta']['multi_rubriques'] == 'oui'
			AND ($r['langue_choisie'] == "oui" OR $r['id_parent'] == 0))
				$titre .= ' ['.traduire_nom_langue($r['lang']).']';
			$data[$r['id_rubrique']] = $titre;
			$enfants[$r['id_parent']][] = $r['id_rubrique'];
			if ($id_rubrique == $r['id_rubrique']) $id_parent = $r['id_parent'];
		}

		$liste_rub_dir = AccesRestreint_liste_contenu_zone_rub_direct($id_zone);
		$liste_rub = AccesRestreint_liste_contenu_zone_rub($id_zone);
	
		$r = "<div style='font-size: 90%; width: 99%;"
		."font-face: verdana,arial,helvetica,sans-serif;'>\n";
	
		$r .= AccesRestreint_sous_menu_rubriques($id_zone,0,
			0,$data,$enfants,$liste_rub_dir, $liste_rub, $type);
	
		$r .= "</div>\n";
	
		return $r;
	}


	//Affichage de la liste des auteurs

function AccesRestreint_afficher_auteurs($titre_table, $requete)
{
	global $couleur_claire;

	$tmp_var = 't_' . substr(md5(join(' ',$requete)), 0, 4);

	return affiche_tranche_bandeau($requete, "auteur-24.gif", $couleur_claire, "black", $tmp_var, $titre_table, false,  array(''), array('arial2'), 'AccesRestreint_afficher_auteurs_boucle');
}

function AccesRestreint_afficher_auteurs_boucle($row, &$tous_id, $voir_logo, $bof)
{
	global $spip_lang_right;

	$vals = '';
	$id_auteur=$row["id_auteur"];
	if (autoriser('voir','auteur',$id_auteur)){
		$nom=typo($row["nom"]);
		$statut=$row["statut"];
		
		$tous_id[] = $id_auteur;

		switch ($statut) {
			case '0minirezo':
					$puce='admin-12.gif';
					$title = _T('info_administrateur');
					break;
				case '1comite':
					$puce='redac-12.gif';
					$title = _T('info_redacteur_1');
					break;
				case '6forum':
					$puce='visit-12.gif';
					$title = _T('info_visiteur_1');
					break;
		}

		$s = "<a href=\"".generer_url_ecrire("auteur_infos","id_auteur=$id_auteur")."\" title=\"$nom\">";
	
		if ($voir_logo) {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id_auteur, 'id_auteur', 'on'))  {
				list($fid, $dir, $nom_img, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$s .= "<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			}
		}
	
		$s .= http_img_pack($puce, $statut, "") ."&nbsp;&nbsp;";
				
		$s .= typo($nom);
		
		$s .= "</a> &nbsp;&nbsp;";
		$vals[] = $s;

	}

	return $vals;
}

?>
