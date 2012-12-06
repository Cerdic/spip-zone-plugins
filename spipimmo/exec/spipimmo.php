<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');
	include_spip('inc/spipimmo_pagination');

	function exec_spipimmo()
	{
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('SPIP-Immo'), "configuration", 'spipimmo');
		//debut_page(_T('SPIP-Immo'), "", "spipimmo");

		//Titre du plugin
		echo '<br/>';
		echo gros_titre(_T('spipimmo:' . _SPIPIMMO_TITRE), '', false);

		//Menu gauche
		echo debut_gauche("spipimmo", true);

			//Menu gauche
			echo debut_boite_info("spipimmo", true);
				echo icone_horizontale("Cr&eacute;er une annonce", "?exec=ajouter_annonce", "mot-cle-24.gif","rien.gif", false);
				echo icone_horizontale("Liste des annonces", "?exec=spipimmo", "breve-24.gif","rien.gif", false);
			echo fin_boite_info("spipimmo", true);

		//Contenu principal
		echo creer_colonne_droite("spipimmo", true);
		echo debut_droite("spipimmo", true);

		//Vérification de la suppression d'une annonce
		if((isset($_GET["suppr"])) and ((empty($_GET["suppr"]))==false))
		{
			echo(debut_cadre_relief("", true));
				switch(_request('suppr'))
				{
					case 1 :
						echo("<p class=\"chargementok\">Suppression de l'annonce r&eacute;ussie</p>");
						break;
					case 0 :
						echo("<p class=\"chargementrate\">Suppression de l'annonce &eacute;chou&eacute</p>");
						break;
				}
			echo(fin_cadre_relief(true));
			echo '<br/>';
		}

		echo debut_cadre_trait_couleur("breve-24.gif", true, "", _T("Liste des annonces"));

			$ret=spipimmo_pagination();

			$order=$ret[1];
			$limit=$ret[2];
			$nPage=$ret[3];
			$nbAnnonceTotal=$ret[4];

			$resListeAnnonces=sql_select("*", "spip_annonces", "", $order, "", $limit);
			$nbAnnonces=sql_count($resListeAnnonces);

			if($nbAnnonces==0)
			{
				$out='<table id="tableannonces">';
				$out.='<tr><td id="aucuneannonce">Aucune annonce disponible</td></tr>';
				$out.='</table>';
			}
			else
			{
				if($nbAnnonceTotal>_SPIPIMMO_PAGE_NBRES)
				{
					$out.=$ret[0] . '<hr class="hrpagination" />';
				}

				$out.='<table id="tableannonces">
				<tr>
					<td class="tableannoncestitre" style="width:5px;">&nbsp;</td>
					<td class="tableannoncestitre">N&deg; dossier</td>
					<td class="tableannoncestitre">Type</td>
					<td class="tableannoncestitre">Ville</td>
					<td class="tableannoncestitre">Prix</td>
					<td class="tableannoncestitre" style="width:115px;">Photo</td>
					<td class="tableannoncestitre" style="width:20px;">Sup.</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><a href="?exec=spipimmo&tri=dossier-desc&pg=' . $nPage . '" title="Trier par num&eacute;ro de dossier"><img src="../prive/images/monter-16.png" alt="Trier par num&eacute;ro de dossier" /></a><a href="?exec=spipimmo&tri=dossier-asc&pg=' . $nPage . '" title="Trier par num&eacute;ro de dossier"><img src="../prive/images/descendre-16.png" alt="Trier par num&eacute;ro de dossier" /></a></td>
					<td><a href="?exec=spipimmo&tri=type-desc&pg=' . $nPage . '" title="Trier par type d\'offre"><img src="../prive/images/monter-16.png" alt="Trier par type d\'offre" /></a><a href="?exec=spipimmo&tri=type-asc&pg=' . $nPage . '" title="Trier par type d\'offre"><img src="../prive/images/descendre-16.png" alt="Trier par type d\'offre" /></a></td>
					<td><a href="?exec=spipimmo&tri=ville-desc&pg=' . $nPage . '" title="Trier par ville"><img src="../prive/images/monter-16.png" alt="Trier par ville" /></a><a href="?exec=spipimmo&tri=ville-asc&pg=' . $nPage . '" title="Trier par ville"><img src="../prive/images/descendre-16.png" alt="Trier par ville" /></a></td>
					<td><a href="?exec=spipimmo&tri=prix-asc&pg=' . $nPage . '" title="Trier par prix"><img src="../prive/images/monter-16.png" alt="Trier par prix" /></a><a href="?exec=spipimmo&tri=prix-desc&pg=' . $nPage . '" title="Trier par prix"><img src="../prive/images/descendre-16.png" alt="Trier par prix" /></a></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>';
				for($i=0; $i<$nbAnnonces; $i++)
				{
					$annonce=sql_fetch($resListeAnnonces);

					$resListeDocumentsAnnonces=sql_select("*", "spip_documents_annonces", "`numero_dossier`='" . $annonce["id_annonce"] . "' AND (`fichier` LIKE '%.jpg' OR `fichier` LIKE '%.png' OR `fichier` LIKE '%.gif')", "id_document ASC", "", "0,1");
					$enrDocument=sql_fetch($resListeDocumentsAnnonces);

					$out.='<tr class="trtableannonces" onmouseover="javascript:this.style.backgroundColor=\'#EEEEEE\'; this.style.cursor=\'pointer\';" onmouseout="javascript:this.style.backgroundColor=\'transparent\';" onclick="javascript:window.location.replace(\'?exec=modifier_annonce&amp;id=' . $annonce["id_annonce"] . '\');">';
					$action_link=generer_action_auteur("spipimmo_publier_annonce", $annonce["id_annonce"]);
						if($annonce["publier"]==1)
						{
							$out.='<td><a href="' . $action_link . '" title="Ne pas publier"><img src="../prive/images/puce-verte.gif" alt="Ne pas publier" /></a></td>';
						}
						else
						{
							$out.='<td><a href="' . $action_link  . '" title="Publier"><img src="../prive/images/puce-rouge.gif" alt="Publier" /></a></td>';
						}
						$out.='<td>' . afficher_ndossier($annonce["id_annonce"]) . '</td>
							<td>' . $annonce["type_offre"] . '</td>
							<td>' . $annonce["ville_bien"] . '</td>
							<td>' . number_format($annonce["prix_loyer"], 0, ' ', ' ') . ' &euro;</td>
							<td>' . redimage(_DIR_IMG . substr($enrDocument["fichier"], 4), _SPIPIMMO_REP_VIGNETTES . substr($enrDocument["fichier"], 4), "100", "", "", 0) . '</td>';
						$action_link=generer_action_auteur("spipimmo_supprimer_annonce", $annonce["id_annonce"]);
					$out.='<td><a href="' . $action_link . '" onclick="javascript:return confirmerSupprimer()" title="Supprimer l\'annonce"><img src="../prive/images/poubelle.gif" alt="supprimer" /></a></td>
						</tr>';
				}
				$out.='</table>';
				if($nbAnnonceTotal>_SPIPIMMO_REP_VIGNETTES)
				{
					$out.='<hr class="hrpagination" />' . $ret[0];
				}
			}
		echo $out;
		echo fin_cadre_trait_couleur(true);

		echo fin_gauche(true), fin_page(true);
	}
?>
