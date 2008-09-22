<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('base/abstract_sql');

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
