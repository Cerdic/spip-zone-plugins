<?php

/***************************************************************************
 *  BOUTIQUE : Plugin, version lite d'un e-commerce pour SPIP              *
 *                                                                         *
 *  Copyright (c) 2006-2007                                                *
 *  Laurent RIEFFEL : mailto:laurent.rieffel@laposte.net			   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 ***************************************************************************/

/*
 * Boutique
 * version plug-in d'un e-commerce
 *
 * Auteur : Laurent RIEFFEL
 * 
 * Module pour SPIP version 1.9.x
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */


function afficher_sessions ($titre_table, $requete, $icone = '') 
	{
	global $couleur_claire, $couleur_foncee;
	global $connect_id_auteur;

	$tous_id = array();
	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'] ? $requete['FROM'] : 'spip_ecommerce_sessions';
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
//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("affiche boutique [id_session -> $cpt $tmp_var $deb_aff $nb_aff]")."</strong> ";
//	exit;
//
// FIN
//	

	if (!$icone) $icone = "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png";
	if ($cpt) 
		{
		if ($titre_table) echo "<div style='height: 12px;'></div>";
		echo "<div class='liste'>";
		bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
		echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
		echo $tranches;

		$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
		$num_rows = spip_num_rows($result);
		$ifond = 0;
		$premier = true;
		$compteur_liste = 0;
		$s='' ;
		$t='';
		while ($row = spip_fetch_array($result)) 
			{
			$s='';
			$vals='';
			$somme_chapo=0;
			$somme_quantite=0;

			$id_session = $row['id_session'];
			$code_session = $row['code_session'];
			$nom = $row['nom'];
			$prenom = $row['prenom'];
			$statut = $row['statut'];
			$transaction = $row['transaction'];
			$email = $row['email'];
			$news = $row['news'];
			$telephone = $row['telephone'];
			$zone = $row['zone'];
			$adresse_livraison = $row['adresse_livraison'];
			$code_postal_livraison = $row['code_postal_livraison'];
			$ville_livraison = $row['ville_livraison'];
			$pays_livraison = $row['pays_livraison'];
			$adresse_facturation = $row['adresse_facturation'];
			$code_postal_facturation = $row['code_postal_facturation'];
			$ville_facturation = $row['ville_facturation'];
			$pays_facturation = $row['pays_facturation'];
			$maj = $row['maj'];
			$tous_id[] = $id_session;

			if ($news == 0)
				$news="Non abonné";
			else
				$news="Abonné" ;
			$s .= debut_cadre_relief("../"._DIR_PLUGIN_BOUTIQUE."/img_pack/panier.png", true);
			$s .= "<div style='padding: 0px; background-color: $couleur_claire; text-align: center; color: black;'>";
			$s .= "<div class='verdana2'>";
			$s .= _T("Boutique:".$id_session.":".$code_session."\n<br />".$nom." ".$prenom."\n<br />".urldecode($zone)." ".urldecode($telephone)."\n<br />".urldecode($email)." : ".urldecode($news));
				$s .= "<div style='border: 0px solid #00ff00 ; padding: 0px; width:50% ;float:left ;background-color: $couleur_claire; text-align: left; color: black;'>";
					$s .= _T("Livraison:".urldecode($adresse_livraison)."\n<br />".urldecode($code_postal_livraison)." ".urldecode($ville_livraison)."\n<br />".urldecode($pays_livraison));
				$s .= "</div>";
				$s .= "<div style='border: 0px solid #0000ff ; padding: 0px; width:50% ;float:left ;color: #0000ff; background-color: $couleur_foncee; text-align: left; color: black;'>";
					$s .= _T("Facturation:".urldecode($adresse_facturation)."\n<br />".urldecode($code_postal_facturation)." ".urldecode($ville_facturation)."\n<br />".urldecode($pays_facturation));

				$s .= "</div>";
			$s .= "</div>";
			$s .= "</div>\n";
			$s .= "<br />";
			$result_chapo=spip_query("SELECT chapo, spip_ecommerce_paniers.quantite FROM spip_articles, spip_ecommerce_paniers where spip_articles.id_article=spip_ecommerce_paniers.id_article and spip_ecommerce_paniers.id_session=".$id_session);
			$num_rows_chapo = spip_num_rows($result_chapo);
			while ($row_chapo = spip_fetch_array($result_chapo)) 
				{
				$chapo = $row_chapo['chapo'];
				$quantite = $row_chapo['quantite'];
				$somme_chapo=$somme_chapo+(intval($chapo) *  intval($quantite));
				$somme_quantite=$somme_quantite+(intval($quantite));
				}
			if (!strcmp($zone, "France"))
				$frais_port=8+(($somme_quantite - 1) * 2) ;
			if (!strcmp("$zone", "UE"))
				$frais_port=20+(($somme_quantite - 1) * 5) ;
			if (!strcmp("$zone", "Autres"))
				$frais_port=25+(($somme_quantite - 1) * 15) ;
			spip_free_result($result_chapo);
			$t = "Statut :".$statut." Transaction :".urldecode($transaction)."\n<br />Date de passage :".$maj."\n<br />Nombre d'article :".$num_rows_chapo."/".$somme_quantite."\n<BR \>Total de la facture :".($somme_chapo+$frais_port) ;
			$link = generer_url_ecrire('panier_edit',"id_session=".$id_session."&retour=".urlencode(self()));
			$s .= icone_horizontale(_T("boutique_edit:".$t),
				$link, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/panier.png", "", false);
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