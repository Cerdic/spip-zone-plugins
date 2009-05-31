<?php
/*
 * boutique
 * version plug-in de spip_boutique
 *
 * Auteur :
 * 
 * 
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/outils');

//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("UPDATE [resultat -> $result]")."</strong> ";
//	exit;
//
// FIN
//	



//
// Afficher une liste de panier
//
function afficher_paniers($titre_table, $requete, $icone = '') 
	{
	global $couleur_claire, $couleur_foncee;
	global $connect_id_auteur;

	$tous_id = array();
	$id_session = _request('id_session');
	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'] ? $requete['FROM'] : 'spip_ecommerces_paniers';
	$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
	$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
	$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
	$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
	$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';
	$cpt = "$from$join$where$group";
	$tmp_var = substr(md5($cpt), 0, 4);

	if (!$group)
		{
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
		if (! ($cpt = $cpt['n'])) return $tous_id ;
		}
	else
		$cpt = spip_num_rows(spip_query("SELECT $select FROM $cpt"));
	if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);
	$nb_aff = 1.5 * _TRANCHES;
	$deb_aff = intval(_request('t_' .$tmp_var));

	if ($cpt > $nb_aff) 
		{
		$nb_aff = (_TRANCHES); 
		$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		}

	if (!$icone) $icone = "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/panier.png";
	if ($cpt) 
		{
		if ($titre_table) echo "<div style='height: 12px;'></div>";
		echo "<div class='liste'>";
		bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
		echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
		echo $tranches;
		$result_paniers = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
		$num_rows_paniers = spip_num_rows($result_paniers);
		$ifond = 0;
		$premier = true;
		$compteur_liste = 0;
		while ($row_paniers = spip_fetch_array($result_paniers)) 
			{
			$link='';
			$s='';
			$vals = '';
			$id_panier = $row_paniers['id_panier'];
			$id_session = $row_paniers['id_session'];
			$tous_id[] = $id_panier;
			$somme_chapo=0;
			$somme_quantite=0;

			$result_chapo=spip_query("
				SELECT spip_articles.chapo, spip_ecommerce_paniers.quantite
				FROM spip_articles, spip_ecommerce_paniers 
				WHERE spip_articles.id_article=spip_ecommerce_paniers.id_article 
				AND spip_ecommerce_paniers.id_session=".$id_session." LIMIT 0, 30");
			$num_rows_chapo = spip_num_rows($result_chapo);	
			while ($row_chapo = spip_fetch_array($result_chapo)) 
				{
				$chapo = $row_chapo['chapo'];
				$quantite = $row_chapo['quantite'];
				$somme_chapo=$somme_chapo+(intval($chapo)*intval($quantite));
				$somme_quantite=$somme_quantite+(intval($quantite));
				}
			$s .= debut_cadre_relief("../"._DIR_PLUGIN_BOUTIQUE."/img_pack/panier.png", true);
			$s .= "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
			$s .= "</div>\n";
			$s .= "<div class='verdana2'>";
			$s .= _T("Panier:Boutique :".$id_session."\n<br />Nombre d'article :".$num_rows_chapo."/".$somme_quantite." Montant total des achats :".$somme_chapo );
			$s .= "</div>";
			$s .= "<br />";
			$result_articles=spip_query("
				SELECT spip_articles.*, spip_ecommerce_paniers.* 
				FROM spip_articles, spip_ecommerce_paniers 
				WHERE spip_articles.id_article=spip_ecommerce_paniers.id_article 
				AND spip_ecommerce_paniers.id_session=".$id_session." LIMIT 0, 30");
			$num_rows_articles = spip_num_rows($result_articles);	
			while ($row_articles = spip_fetch_array($result_articles)) 
				{
				$t='';
				$t .= "Reference de l'article :".$row_articles['titre'];
				$t .= " Pointure :".$row_articles['pointure'];
				$t .= " Quantite :".$row_articles['quantite'];
				$link = generer_url_ecrire('articles',"id_article=".$row_articles['id_article']."&retour=".urlencode(self()));
				$s .= icone_horizontale(_T("boutique_edit:".$t),
					$link, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/chaussure.png", "", false);
				}
			$s .= fin_block();
			$s .= fin_cadre_relief(true);
			$vals[] = $s;
			$table[] = $vals;
			}
		spip_free_result($result);
		$largeurs = array('','','','','');
		$styles = array('arial11', 'arial11', 'arial1', 'arial1','arial1');
		echo afficher_liste($largeurs, $table, $styles);
		echo "</table>";
		echo "</div>\n";
		}
	return $tous_id;
	}

?>