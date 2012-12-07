<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');

	function exec_ajouter_document()
	{
		$idAnnonce=_request('id');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('SPIP-Immo'), "configuration", 'spipimmo');

		//Titre du plugin
		echo '<br/>';
		echo gros_titre(_T(_SPIPIMMO_TITRE), '', false);

		//Menu gauche
		echo debut_gauche("spipimmo", true);
			echo debut_boite_info("spipimmo", true);
				echo icone_horizontale("Cr&eacute;er une annonce", "?exec=ajouter_annonce", "mot-cle-24.gif","rien.gif", false);
				echo icone_horizontale("Liste des annonces", "?exec=spipimmo", "breve-24.gif","rien.gif", false);
			echo fin_boite_info("spipimmo", true);

		//Contenu principal
		//echo creer_colonne_droite("spipimmo", true);
		echo debut_droite("spipimmo", true);

		//Vérification chargement document
		if((isset($_GET["charg"])) and ((empty($_GET["charg"]))==false))
		{
			echo(debut_cadre_relief("", true));
				switch(_request('charg'))
				{
					case 1 :
						echo '<p class="chargementok">Chargement du document r&eacute;ussi</p>';
						break;
					case 2 :
						echo '<p class="chargementrate">Chargement &eacute;chou&eacute : format de fichier non valide</p>';
						break;
					case 3 :
						echo '<p class="chargementrate">Chargement &eacute;chou&eacute</p>';
						break;
					case 4 :
						echo '<p class="chargementrate">Chargement du document r&eacute;ussi; mais insertion dans la base de donn&eacute;e &eacute;chou&eacute;</p>';
						break;
				}
			echo(fin_cadre_relief(true));
			echo '<br/>';
		}

		//Espace chargement
		echo debut_cadre_trait_couleur("synchro-24.gif", true, "", _T("Ajouter un document (ou une image)"));
			$action_link=generer_action_auteur("spipimmo_ajouter_document", "$idAnnonce-ajouter");
			$out='<form action="' . $action_link . '" method="post" enctype="multipart/form-data">
					<table class="annonce">
						<tr>
							<td class="libelle_annonce">Charger : </td>
							<td class="saisie_annonce">
								<input type="file" name="fichier" />&nbsp;&nbsp;&nbsp;
								<input type="submit" class="fondo" value="Enregistrer" />
							</td>
						</tr>
					</table>
				</form>';
			echo $out;
		echo fin_cadre_trait_couleur(true);

		echo '<br/>';

		//Les images
		echo debut_cadre_trait_couleur("vignette-24.png", true, "", _T("Liste des images"));
			$action_link=generer_action_auteur("spipimmo_supprimer_document", "$idAnnonce-ajouter");
			$out='<form id="annonce" action="' . $action_link . '" method="post">';
			$handle=opendir(_DIR_IMG);
				$j=0;
				while ($fichier = readdir($handle))
				{
					if(ereg("^immo" . _request('id') . "-[0-9]*.[a-zA-Z]*$", $fichier))
					{
						if(substr($fichier,-3)=="jpg" or substr($fichier,-3)=="png" or substr($fichier,-3)=="gif")
						{
							if($j%4==0)
							{
								$out.='<div class="ligneimage">';
							}

							$out.='<div class="galerieimage">
									<div>
										' . redimage(_DIR_IMG . $fichier, _SPIPIMMO_REP_VIGNETTES .  $fichier, "100", "", "", 1) . '
									</div>
									<div class="main">
										<label for="suppr_' . substr($fichier, 0, -4) . '">
											<input type="checkbox" id="suppr_' . substr($fichier, 0, -4) . '" name="suppr_' . substr($fichier, 0, -4) . '" />
											<span>Supprimer</span>
										</label>
									</div>
								</div>';

							if($j%4==0)
							{
								$out.='</div>';
							}
							$j++;
						}
					}
				}
				if($j==0)
				{
					$out.='<p class="aucuneimage">Aucune image</p>';
				}
				else
				{
					$out.='<div class="supprimerselection">
						<input class="fondo" type="submit" value="Supprimer la s&eacute;l&eacute;ction" /></div>';
				}
			closedir($handle);
			$out.='</form>';
			echo $out;
		echo fin_cadre_trait_couleur(true);

		echo '<br/>';

		//Les documents
		echo debut_cadre_trait_couleur("petition-24.gif", true, "", _T("Liste des documents"));
			$action_link=generer_action_auteur("spipimmo_supprimer_document", "$idAnnonce-ajouter");
			$out='<form id="annonce" action="' . $action_link . '" method="post">';
			// affichage des documents déjà existants
			$handle=opendir(_DIR_IMG);
				$j=0;
				while ($fichier = readdir($handle))
				{
					if(ereg("^immo" . _request('id') . "-[0-9]*.[a-zA-Z]*$", $fichier))
					{
						$tabDocument=split('\.', $fichier);
						if($tabDocument[1]!="jpg" and $tabDocument[1]!="png" and $tabDocument[1]!="gif")
						{
							if($j%4==0)
							{
								$out.='<div class="ligneimage">';
							}

							$out.='<div class="galerieimage">
									<div>
										<a href="' . _DIR_IMG . $fichier . '"><img src="' . _DIR_IMG_ICONES_DIST . $tabDocument[1] . '.png" alt="' . $fichier . '"  /></a>
									</div>
									<div class="main">
										<label for="suppr_' . $tabDocument[0] . '">
											<input type="checkbox" id="suppr_' . $tabDocument[0] . '" name="suppr_' . $tabDocument[0] . '" />
											<span>Supprimer</span>
										</label>
									</div>
								</div>';
							if($j%4==0)
							{
								$out.='</div>';
							}
							$j++;
						}
					}
				}
				if($j==0)
				{
					$out.='<p class="aucuneimage">Aucun document</p>';
				}
				else
				{
					$out.='<div class="supprimerselection">
							<input class="fondo" type="submit" value="Supprimer la s&eacute;l&eacute;ction" />
						</div>';
				}
			closedir($handle);
		$out.='</form>';
		echo $out;
		echo fin_cadre_trait_couleur(true);

		echo fin_gauche(true), fin_page(true);

	}
?>
