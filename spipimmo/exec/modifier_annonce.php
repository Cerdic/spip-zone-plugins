<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');

	function exec_modifier_annonce()
	{
		//initialisation sortie
		$out="";

		//Récupération de l'identifiant de l'annonce
		$idAnnonce=_request('id');

		//Requete pour récupérer les informations déjà entrées
		$res=sql_select("*", "spip_annonces", "`id_annonce`=" . $idAnnonce);
		$ligneRes=sql_fetch($res);

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('SPIP-Immo'), "configuration", 'spipimmo');

		//Titre du plugin
		echo '<br/>';
		echo gros_titre(_T('spipimmo:' . _SPIPIMMO_TITRE), '', false);

		//Menu gauche
		echo debut_gauche("spipimmo", true);
			echo debut_boite_info("spipimmo", true);
				echo icone_horizontale("Cr&eacute;er une annonce", "?exec=ajouter_annonce", "mot-cle-24.gif","rien.gif", false);
				echo icone_horizontale("Liste des annonces", "?exec=spipimmo", "breve-24.gif","rien.gif", false);
			echo fin_boite_info("spipimmo", true);

		//Contenu principal
		echo creer_colonne_droite("spipimmo", true);
		echo debut_droite("spipimmo", true);


		//Vérification modification
		if((isset($_GET["mod"])) and ((empty($_GET["mod"]))==false))
		{
			echo(debut_cadre_relief("", true));
				switch(_request('mod'))
				{
					case 1 :
						echo("<p class=\"chargementok\">Modification de l'annonce r&eacute;ussie</p>");
						break;
					case 0 :
						echo("<p class=\"chargementrate\">Modification de l'annonce &eacute;chou&eacute</p>");
						break;
				}
			echo(fin_cadre_relief(true));
			echo '<br/>';
		}

		//Vérification chargement document
		if((isset($_GET["charg"])) and ((empty($_GET["charg"]))==false))
		{
			echo(debut_cadre_relief("", true));
				switch(_request('charg'))
				{
					case 1 :
						echo("<p class=\"chargementok\">Chargement du document r&eacute;ussi</p>");
						break;
					case 2 :
						echo("<p class=\"chargementrate\">Chargement &eacute;chou&eacute : format de fichier non valide</p>");
						break;
					case 3 :
						echo("<p class=\"chargementrate\">Chargement &eacute;chou&eacute</p>");
						break;
					case 4 :
						echo("<p class=\"chargementrate\">Chargement du document r&eacute;ussi; mais insertion dans la base de donn&eacute;e &eacute;chou&eacute;</p>");
						break;
				}
			echo(fin_cadre_relief(true));
			echo '<br/>';
		}

		//Formulaire
		echo debut_cadre_trait_couleur("groupe-mot-24.gif", true, "", _T("Modifier une annonce"));

		$dateDispo=split("-", $ligneRes["date_disponibilite"]);

		$action_link=generer_action_auteur("spipimmo_modifier_annonce", $idAnnonce);

		$out.='<form id="annonce" action="' . $action_link . '" method="post" onsubmit="return verificationChamps()">
			<table class="intitule">
				<tr>
					<td id="intitule_tableau1" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'1\'); cacherTableau(\'2,3,4,5,6\');"><a href="javascript:afficherTableau(\'1\'); cacherTableau(\'2,3,4,5,6\');">Dossier</a></td>
					<td id="intitule_tableau2" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'2\'); cacherTableau(\'1,3,4,5,6\');"><a href="javascript:afficherTableau(\'2\'); cacherTableau(\'1,3,4,5,6\');">Informations</a></td>
					<td id="intitule_tableau3" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'3\'); cacherTableau(\'1,2,4,5,6\');"><a href="javascript:afficherTableau(\'3\'); cacherTableau(\'1,2,4,5,6\')">Informations (suite)</a></td>
				</tr>

				<tr>
					<td id="intitule_tableau4" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'4\'); cacherTableau(\'1,2,3,5,6\');"><a href="javascript:afficherTableau(\'4\'); cacherTableau(\'1,2,3,5,6\');">Descriptif</a></td>
					<td id="intitule_tableau5" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'5\'); cacherTableau(\'1,2,3,4,6\');"><a href="javascript:afficherTableau(\'5\'); cacherTableau(\'6,1,2,3,4\');">Descriptif (suite)</a></td>
					<td id="intitule_tableau6" onmouseover="this.style.borderColor=\'#AAAAAA;\'" onmouseout="this.style.borderColor=\'#CCCCCC;\'" onclick="afficherTableau(\'6\'); cacherTableau(\'1,2,3,4,5\');"><a href="javascript:afficherTableau(\'6\'); cacherTableau(\'1,2,3,4,5\')">Texte annonce</a></td>
				</tr>
			</table>

			<div id="tableau1">
				<table class="annonce">
					<tr id="tr_numero_dossier">
							<td class="libelle_annonce">Num&eacute;ro dossier</td>
							<td class="saisie_annonce">' . afficher_ndossier($ligneRes["id_annonce"]) . '</td>
					</tr>

					<tr id="tr_vente_location" style="background:none;">
						<td class="libelle_annonce">Vente/Location</td>
						<td class="saisie_annonce">
							<select name="vente_location" onchange="javascript:formObligatoire(\'vente_location\', \'tr_vente_location\');">';
								if ($ligneRes["vente_location"]=="Vente")
								{
									$out.='<option value=""></option>
									<option selected="selected" value="Vente">Vente</option>
									<option value="Location">Location</option>';
								}
								else if ($ligneRes["vente_location"]=="Location")
								{
									$out.='<option value=""></option>
									<option value="Vente">Vente</option>
									<option selected="selected" value="Location">Location</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="Vente">Vente</option>
									<option value="Location">Location</option>';
								}
							$out.='</select>
						</td>
					</tr>


					<tr id="tr_numero_mandat">
						<td class="libelle_annonce">Num&eacute;ro mandat</td>
						<td class="saisie_annonce"><input type="text" name="numero_mandat" value="' . $ligneRes["n_mandat"]  . '" /></td>
					</tr>

					<tr id="tr_type_mandat" style="background:none;">
						<td class="libelle_annonce">Type mandat</td>
						<td class="saisie_annonce">
							<select name="type_mandat" onchange="javascript:formObligatoire(\'type_mandat\', \'tr_type_mandat\');">';
								if ($ligneRes["type_mandat"]=="SIMPLE")
								{
									$out.='<option value=""></option>
									<option selected="selected" value="SIMPLE">SIMPLE</option>
									<option value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option value="PRIVILEGE">PRIVILEGE</option>
									<option value="EXCLUSIF">EXCLUSIF</option>';
								}
								else if ($ligneRes["type_mandat"]=="SEMI PRIVILEGE")
								{
									$out.='<option value=""></option>
									<option value="SIMPLE">SIMPLE</option>
									<option selected="selected" value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option value="PRIVILEGE">PRIVILEGE</option>
									<option value="EXCLUSIF">EXCLUSIF</option>';
								}
								else if ($ligneRes["type_mandat"]=="PRIVILEGE")
								{
									$out.='<option value=""></option>
									<option value="SIMPLE">SIMPLE</option>
									<option value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option selected="selected" value="PRIVILEGE">PRIVILEGE</option>
									<option value="EXCLUSIF">EXCLUSIF</option>';
								}
								else if ($ligneRes["type_mandat"]=="EXCLUSIF")
								{
									$out.='<option value=""></option>
									<option value="SIMPLE">SIMPLE</option>
									<option value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option value="PRIVILEGE">PRIVILEGE</option>
									<option selected="selected" value="EXCLUSIF">EXCLUSIF</option>';
								}
								else
								{
									$out.='<option value=""></option>
									<option value="SIMPLE">SIMPLE</option>
									<option value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option value="PRIVILEGE">PRIVILEGE</option>
									<option value="EXCLUSIF">EXCLUSIF</option>';
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_negociateur">
						<td class="libelle_annonce">N&eacute;gociateur</td>
						<td class="saisie_annonce"><input type="text" name="negociateur" value="' . $ligneRes["negociateur"]  . '" /></td>
					</tr>

					<tr id="tr_honoraire">
						<td class="libelle_annonce">Honoraires</td>
						<td class="saisie_annonce"><input type="text" name="honoraire" value="' . $ligneRes["honoraires"]  . '" /> &euro;</td>
					</tr>

					<tr id="tr_publier_offre" style="background:none;">
						<td class="libelle_annonce">Publier offre</td>
						<td class="saisie_annonce">
							<select name="publier_offre" onchange="javascript:formObligatoire(\'publier_offre\', \'tr_publier_offre\');">';
								if ($ligneRes["publier"]==1)
								{
									$out.='<option value=""></option>
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>';
								}
								else if ($ligneRes["publier"]==0)
								{
									$out.='<option value=""></option>
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>';
								}
							$out.='</select>
						</td>
					</tr>
				</table>
			</div>

			<div id="tableau2">
				<table class="annonce">
					<tr id="tr_type_offre" style="background:none">
						<td class="libelle_annonce">Type offre</td>
						<td class="saisie_annonce">
							<select name="type_offre" onchange="javascript:formObligatoire(\'type_offre\', \'tr_type_offre\');">
								<option selected="selected" value=""></option>';
								$listeTypesOffres=sql_select("*", "spip_types_offres");
								$nbTypesOffres=sql_count($listeTypesOffres);
								for ($i=0; $i<$nbTypesOffres; $i++)
								{
									$typeOffre=sql_fetch($listeTypesOffres);
									if($typeOffre["libelle_offre"]==$ligneRes["type_offre"])
									{
										$out.='<option selected="selected" value="' . $typeOffre["libelle_offre"] . '">' . $typeOffre["libelle_offre"] . '</option>';
									}
									else
									{
										$out.='<option value="' . $typeOffre["libelle_offre"] . '">' . $typeOffre["libelle_offre"] . '</option>';
									}
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_adresse_1">
						<td class="libelle_annonce">Adresse n&deg;1</td>
						<td class="saisie_annonce"><input type="text" size="45" name="adresse_1" value="' . $ligneRes["adr_bien_1"]  . '" /></td>
					</tr>

					<tr id="tr_adresse_2">
						<td class="libelle_annonce">Adresse n&deg;2</td>
						<td class="saisie_annonce"><input type="text" size="45" name="adresse_2" value="' . $ligneRes["adr_bien_2"]  . '" /></td>
					</tr>

					<tr id="tr_code_postal" style="background:none;">
						<td class="libelle_annonce">Code postal</td>
						<td class="saisie_annonce"><input type="text" size="5" id="code_postal" name="code_postal" value="' . $ligneRes["cp_bien"]  . '" onkeyup="javascript:formObligatoire(\'code_postal\', \'tr_code_postal\');" /></td>
					</tr>

					<tr id="tr_code_postal_internet">
						<td class="libelle_annonce">Code postal (internet)</td>
						<td class="saisie_annonce"><input type="text" size="5" name="code_postal_internet" value="' . $ligneRes["cp_internet"]  . '" /></td>
					</tr>

					<tr id="tr_ville" style="background:none;">
						<td class="libelle_annonce">Ville</td>
						<td class="saisie_annonce"><input type="text" name="ville" id="ville" value="' . $ligneRes["ville_bien"] . '" onkeyup="javascript:formObligatoire(\'ville\', \'tr_ville\');" /></td>
					</tr>

					<tr id="tr_ville_internet">
						<td class="libelle_annonce">Ville (internet)</td>
						<td class="saisie_annonce"><input type="text" name="ville_internet" value="' . $ligneRes["ville_internet"]  . '" /></td>
					</tr>

					<tr id="tr_prix_loyer" style="background:none;">
						<td class="libelle_annonce">Prix/loyer</td>
						<td class="saisie_annonce"><input type="text" size="7" name="prix_loyer" value="' . $ligneRes["prix_loyer"]  . '" onkeyup="javascript:formObligatoire(\'prix_loyer\', \'tr_prix_loyer\');" /> &euro;</td>
					</tr>

					<tr id="tr_taxe_habitation">
						<td class="libelle_annonce">Taxe d\'habitation</td>
						<td class="saisie_annonce"><input type="text" size="7" name="taxe_habitation" value="' . $ligneRes["taxe_habitation"]  . '" /> &euro;</td>
					</tr>

					<tr id="tr_taxe_fonciere">
						<td class="libelle_annonce">Taxe fonci&egrave;re</td>
						<td class="saisie_annonce"><input type="text" size="7" name="taxe_fonciere" value="' . $ligneRes["taxe_fonciere"]  . '" /> &euro;</td>
					</tr>

					<tr id="tr_travaux">
						<td class="libelle_annonce">Travaux</td>
						<td class="saisie_annonce"><input type="text" size="7" name="travaux" value="' . $ligneRes["travaux"]  . '" /> &euro;</td>
					</tr>

					<tr id="tr_charge">
						<td class="libelle_annonce">Charges</td>
						<td class="saisie_annonce"><input type="text" size="7" name="charge" value="' . $ligneRes["charges"]  . '" /> &euro;</td>
					</tr>';
					
					if($ligneRes['DPE'] != '')
					{
						
						if($ligneRes['DPE'] == 'A') $select_A = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'B') $select_B = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'C') $select_C = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'D') $select_D = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'E') $select_E = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'F') $select_F = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'G') $select_G = 'selected="selected"';
						elseif($ligneRes['DPE'] == 'N.C') $select_NC = 'selected="selected"';
					}
					
					$out .='<tr id="tr_dpe">
							<td class="libelle_annonce">DPE</td>
							<td class="saisie_annonce">
								<select name="dpe">
									<option value=""></option>
									<option value="A" '.$select_A.'>A -50</option>
									<option value="B" '.$select_B.'>B 51 &agrave; 90</option>
									<option value="C" '.$select_C.'>C 91 &agrave; 150</option>
									<option value="D" '.$select_D.'>D 151 &agrave; 230</option>
									<option value="E" '.$select_E.'>E 231 &agrave; 330</option>
									<option value="F" '.$select_F.'>F 331 &agrave; 450</option>
									<option value="G" '.$select_G.'>G >450</option>
									<option value="N.C" '.$select_NC.'>N.C Non communiqu&eacute;</option>
								</select>
							</td>
						</tr>
					
				</table>
			</div>

			<div id="tableau3">
				<table class="annonce">
					<tr id="tr_depot_garantie">
						<td class="libelle_annonce">D&eacute;p&ocirc;t garantie</td>
						<td class="saisie_annonce"><input type="text" size="7" name="depot_garantie" value="' . $ligneRes["depot_garantie"]  . '" /> &euro;</td>
					</tr>

					<tr id="tr_date_disponibilite">
						<td class="libelle_annonce">Date de disponibilit&eacute;</td>
						<td class="saisie_annonce">
							<select name="jour_dispo">';
								if($dateDispo[2]=="00")
								{
									$out.='<option value="" /></option>';
								}

								for($i=1; $i<=31; $i++)
								{
									if($dateDispo[2]==$i)
									{
										$out.='<option selected="selected" value="' . $i . '" />' . $i . '</option>';
									}
									else
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								}
							$out.='</select>
							&nbsp;
							<select name="mois_dispo">';
								if($dateDispo[1]=="00")
								{
									$out.='<option value="" /></option>';
								}

								for($i=1; $i<=12; $i++)
								{
									if($dateDispo[1]==$i)
									{
										$out.='<option selected="selected" value="' . $i . '" />' . $i . '</option>';
									}
									else
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								}
							$out.='</select>
							&nbsp;
							<select name="annee_dispo">';
								if($dateDispo[2]=="0000")
								{
									$out.='<option value="" /></option>';
								}

								for($i=date('Y'); $i<=date('Y')+3; $i++)
								{
									if($dateDispo[0]==$i)
									{
										$out.='<option selected="selected" value="' . $i . '" />' . $i . '</option>';
									}
									else
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_quartier">
						<td class="libelle_annonce">Quartier</td>
						<td class="saisie_annonce"><input type="text" name="quartier" value="' . $ligneRes["quartier"]  . '" /></td>
					</tr>

					<tr id="tr_secteur">
						<td class="libelle_annonce">Secteur</td>
						<td class="saisie_annonce"><input type="text" name="secteur" value="' . $ligneRes["secteur"]  . '" /></td>
					</tr>

					<tr id="tr_residence">
						<td class="libelle_annonce">R&eacute;sidence</td>
						<td class="saisie_annonce"><input type="text" name="residence" value="' . $ligneRes["residence"]  . '" /></td>
					</tr>

					<tr id="tr_transport">
						<td class="libelle_annonce">Transport(s)</td>
						<td class="saisie_annonce"><input type="text" size="30" name="transport" value="' . $ligneRes["transport"]  . '" /></td>
					</tr>

					<tr id="tr_proximite">
						<td class="libelle_annonce">Proximit&eacute;(s)</td>
						<td class="saisie_annonce"><input type="text" size="30" name="proximite" value="' . $ligneRes["proximite"]  . '" /></td>
					</tr>

					<tr id="tr_annee_construction">
						<td class="libelle_annonce">Ann&eacute;e de construction</td>
						<td class="saisie_annonce"><input type="text" size="4" name="annee_construction" value="' . $ligneRes["annee_cons"]  . '" /></td>
					</tr>

					<tr id="tr_prestige" style="background:none;">
						<td class="libelle_annonce">Prestige</td><td class="saisie_annonce">
							<select name="prestige" onchange="javascript:formObligatoire(\'prestige\', \'tr_prestige\');">';
								if($ligneRes["prestige"]==1)
								{
									$out.='<option value=""></option>
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>';
								}
								else if ($ligneRes["prestige"]==0)
								{
									$out.='<option value=""></option>
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>';
								}
							$out.='</select>
						</td>
					</tr>
				</table>
			</div>

			<div id="tableau4">
				<table class="annonce">
					<tr id="tr_categorie">
						<td class="libelle_annonce">Cat&eacute;gorie</td>
						<td class="saisie_annonce">
							<select name="categorie" />';
								if ($ligneRes["categorie"]=="Maison")
								{
									$out.='<option value=""></option>
									<option selected="selected" value="Maison">Maison</option>
									<option value="Appartement">Appartement</option>
									<option value="Studio">Studio</option>';
								}
								else if ($ligneRes["categorie"]=="Appartement")
								{
									$out.='<option value=""></option>
									<option value="Maison">Maison</option>
									<option selected="selected" value="Appartement">Appartement</option>
									<option value="Studio">Studio</option>';
								}
								else if ($ligneRes["categorie"]=="Studio")
								{
									$out.='<option value=""></option>
									<option value="Maison">Maison</option>
									<option value="Appartement">Appartement</option>
									<option selected="selected" value="Studio">Studio</option>';
								}
								else
								{
									$out.='<option value="" selected="selected"></option>
									<option value="Maison">Maison</option>
									<option value="Appartement">Appartement</option>
									<option value="Studio">Studio</option>';
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_nombre_piece">
						<td class="libelle_annonce">Nombre de pi&egrave;ce(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_piece">
								' . liste_deroulante_piece($ligneRes["nb_pieces"], _SPIPIMMO_NBPIECE_PIECE) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_chambre">
						<td class="libelle_annonce">Nombre de chambre(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_chambre">
								' . liste_deroulante_piece($ligneRes["nb_chambres"], _SPIPIMMO_NBPIECE_CHAMBRE) . '
							</select>
						</td>
					</tr>

					<tr id="tr_surface_habitable" style="background:none;">
						<td class="libelle_annonce">Surface habitable</td>
						<td class="saisie_annonce"><input type="text" size="5" name="surface_habitable" value="' . $ligneRes["surf_habit"]  . '" onkeyup="javascript:formObligatoire(\'surface_habitable\', \'tr_surface_habitable\');" /> m&sup2;</td>
					</tr>

					<tr id="tr_surface_carre">
						<td class="libelle_annonce">Surface carrez</td>
						<td class="saisie_annonce"><input type="text" size="5" name="surface_carre" value="' . $ligneRes["surf_carrez"]  .'" /> m&sup2;</td>
					</tr>

					<tr id="tr_surface_sejour">
						<td class="libelle_annonce">Surface s&eacute;jour</td>
						<td class="saisie_annonce"><input type="text" size="5" name="surface_sejour" value="' . $ligneRes["surf_sejour"]  .'" /> m&sup2;</td>
					</tr>

					<tr id="tr_surface_terrain">
						<td class="libelle_annonce">Surface terrain</td>
						<td class="saisie_annonce"><input type="text" size="5" name="surface_terrain" value="' . $ligneRes["surf_terrain"]  .'" /> m&sup2;</td>
					</tr>

					<tr id="tr_etage">
						<td class="libelle_annonce">Etage</td>
						<td class="saisie_annonce"><input type="text" size="2" name="etage" value="' . $ligneRes["etage"]  . '" /></td>
					</tr>

					<tr id="tr_code_etage">
						<td class="libelle_annonce">Code &eacute;tage</td>
						<td class="saisie_annonce"><input type="text" size="2" name="code_etage" value="' . $ligneRes["code_etage"]  . '" /></td>
					</tr>

					<tr id="tr_nombre_etage">
						<td class="libelle_annonce">Nombre d\'&eacute;tage(s)</td>
						<td class="saisie_annonce"><input type="text" size="2" name="nombre_etage" value="' . $ligneRes["nb_etage"]  . '" /></td>
					</tr>
				</table>
			</div>

			<div id="tableau5">
				<table class="annonce">
					<tr id="tr_type_cuisine">
						<td class="libelle_annonce">Type de cuisine</td>
						<td class="saisie_annonce"><input type="text" name="type_cuisine" value="' . $ligneRes["type_cuisine"]  . '"></td>
					</tr>

					<tr id="tr_nombre_wc">
						<td class="libelle_annonce">Nombre de toilette(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_wc">
								' . liste_deroulante_piece($ligneRes["nb_wc"], _SPIPIMMO_NBPIECE_WC) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_bain">
						<td class="libelle_annonce">Nombre de salle(s) de bains</td>
						<td class="saisie_annonce">
							<select name="nombre_bain">
								' . liste_deroulante_piece($ligneRes["nb_sdb"], _SPIPIMMO_NBPIECE_SALLE_BAIN) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_eau">
						<td class="libelle_annonce">Nombre de salle(s) d\'eau</td>
						<td class="saisie_annonce">
							<select name="nombre_eau" >
								' . liste_deroulante_piece($ligneRes["nb_sde"], _SPIPIMMO_NBPIECE_SALLE_EAU) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_parking_interieur">
						<td class="libelle_annonce">Nombre parking(s) int&eacute;rieur(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_parking_interieur">
								' . liste_deroulante_piece($ligneRes["nb_park_int"], _SPIPIMMO_NBPIECE_PARK_INT) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_parking_exterieur">
						<td class="libelle_annonce">Nombre parking(s) ext&eacute;rieur(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_parking_exterieur">
								' . liste_deroulante_piece($ligneRes["nb_park_ext"], _SPIPIMMO_NBPIECE_PARK_EXT) . '
							</select>
						</td>
					</tr>

					<tr id="tr_nombre_garage">
						<td class="libelle_annonce">Nombre de garage(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_garage">
								' . liste_deroulante_piece($ligneRes["nb_garages"], _SPIPIMMO_NBPIECE_GARAGE) . '
							</select>
						</td>
					</tr>

					<tr id="tr_type_sous_sol">
						<td class="libelle_annonce">Type de sous-sol</td>
						<td class="saisie_annonce"><input type="text" name="type_sous_sol" value="' . $ligneRes["type_soussol"]  . '" /></td>
					</tr>

					<tr id="tr_nombre_cave">
						<td class="libelle_annonce">Nombre de cave(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_cave">
								' . liste_deroulante_piece($ligneRes["nb_caves"], _SPIPIMMO_NBPIECE_CAVE) . '
							</select>
						</td>
					</tr>

					<tr id="tr_type_chauffage">
						<td class="libelle_annonce">Type de chauffage</td>
						<td class="saisie_annonce"><input type="text" name="type_chauffage" value="' . $ligneRes["type_chauf"]  . '" /></td>
					</tr>

					<tr id="tr_nature_cuisine">
						<td class="libelle_annonce">Nature du chauffage</td>
						<td class="saisie_annonce"><input type="text" name="nature_chauffage"  value="' . $ligneRes["nat_chauf"]  . '" /></td>
					</tr>

					<tr id="tr_ascenseur">
						<td class="libelle_annonce">Ascenseur</td>
						<td class="saisie_annonce">
							<select name="ascenseur">';
								if ($ligneRes["ascenseur"]==1)
								{
									$out.='<option value=""></option>
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>';
								}
								else if ($ligneRes["ascenseur"]==0)
								{
									$out.='<option value=""></option>
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>';
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_balcon">
						<td class="libelle_annonce">Balcon</td>
						<td class="saisie_annonce">
							<select name="balcon">
								' . liste_deroulante_piece($ligneRes["balcon"], _SPIPIMMO_NBPIECE_BALCON) . '
							</select>
						</td>
					</tr>

					<tr id="tr_terrasse">
						<td class="libelle_annonce">Terrasse</td>
						<td class="saisie_annonce">
							<select name="terrasse">
								' . liste_deroulante_piece($ligneRes["terrasse"], _SPIPIMMO_NBPIECE_TERRASSE) . '
							</select>
						</td>
					</tr>

					<tr id="tr_piscine">
						<td class="libelle_annonce">Piscine</td>
						<td class="saisie_annonce">
							<select name="piscine">';
								if ($ligneRes["piscine"]==1)
								{
									$out.='<option value=""></option>
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>';
								}
								else if ($ligneRes["piscine"]==0)
								{
									$out.='<option value=""></option>
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>';
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_acces_handicape">
						<td class="libelle_annonce">Acc&egrave;s personnes &agrave; mobilit&eacute; r&eacute;duite</td>
						<td class="saisie_annonce">
							<select name="acces_handicape">';
								if ($ligneRes["acces_handi"]==1)
								{
									$out.='<option value=""></option>
									<option selected="selected" value="1">Oui</option>
									<option value="0">Non</option>';
								}
								else if ($ligneRes["acces_handi"]==0)
								{
									$out.='<option value=""></option>
									<option value="1">Oui</option>
									<option selected="selected" value="0">Non</option>';
								}
								else
								{
									$out.='<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>';
								}
							$out.='</select>
						</td>
					</tr>

					<tr id="tr_nombre_mur">
						<td class="libelle_annonce">Nombre de mur(s) mitoyen(s)</td>
						<td class="saisie_annonce">
							<select name="nombre_mur">
								' . liste_deroulante_piece($ligneRes["nb_murs_mit"], _SPIPIMMO_NBPIECE_MUR_MITOYEN) . '
							</select>
						</td>
					</tr>

					<tr id="tr_facade">
						<td class="libelle_annonce">Facade</td>
						<td class="saisie_annonce"><input type="text" size="3" name="facade" value="' . $ligneRes["facade_terrain"]  . '" /> m</td>
					</tr>
				</table>
			</div>




		<div id="tableau6">
			<table class="annonce">
				<tr id="tr_texte_francais" style="background:none;">
					<td class="libelle_annonce">Texte annonce (fran&ccedil;ais)</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_francais" onkeyup="javascript:formObligatoire(\'texte_francais\', \'tr_texte_francais\');">' . $ligneRes["texte_annonce_fr"]  . '</textarea></td>
				</tr>

				<tr id="tr_texte_anglais">
					<td class="libelle_annonce">Texte annonce (anglais)</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_anglais">' . $ligneRes["texte_annonce_uk"]  . '</textarea></td>
				</tr>

				<tr id="tr_texte_allemand">
					<td class="libelle_annonce">Texte annonce (allemand)</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_allemand">' . $ligneRes["texte_annonce_de"]  . '</textarea></td>
				</tr>

				<tr id="tr_texte_espagnol">
					<td class="libelle_annonce">Texte annonce (espagnol)</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_espagnol">' . $ligneRes["texte_annonce_sp"]  . '</textarea></td>
				</tr>

				<tr id="tr_texte_italien">
					<td class="libelle_annonce">Texte annonce (italien)</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_italien">' . $ligneRes["texte_annonce_it"]  . '</textarea></td>
				</tr>

				<tr id="tr_texte_mailing">
					<td class="libelle_annonce">Texte mailing</td>
					<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_mailing">' . $ligneRes["texte_mailing"]  . '</textarea></td>
				</tr>
			</table>
		</div>

		<div>
			<table>
				<tr>
					<td colspan="2" class="bouton">
						&nbsp;&nbsp;<input class="fondo" type="submit" value="Enregistrer" />
					</td>
				</tr>
				<tr>
					<td colspan="2" class="obligatoire">Les champs surlign&eacute;s en orange sont obligatoires, merci de les compl&eacute;ter. Seul l\'onglet "descriptif (suite)" ne comporte pas de champs obligatoire.</td>
				</tr>
			</table>
		</div>
	</form>';

		echo $out;
		echo fin_cadre_trait_couleur(true);

		echo '<br/>';

		//Espace de chargement
		echo debut_cadre_trait_couleur("synchro-24.gif", true, "", _T("Ajouter un document (ou une image)"));
			$action_link=generer_action_auteur("spipimmo_ajouter_document", "$idAnnonce-modifier");
			//Formulaire pour ajouter des images
			$out='<form action="' . $action_link . '" method="post" enctype="multipart/form-data">
					<table class="annonce">
						<tr>
							<td class="libelle_annonce">Charger : </td><td class="saisie_annonce">
								<input type="file" name="fichier" />&nbsp;&nbsp;&nbsp;<input type="submit" class="fondo" value="Enregistrer">
							</td>
						</tr>
					</table>
				</form>';
			echo $out;
		echo fin_cadre_trait_couleur(true);

		echo '<br/>';

		//Les images
		echo debut_cadre_trait_couleur("vignette-24.png", true, "", _T("Liste des images"));
		$action_link=generer_action_auteur("spipimmo_supprimer_document", "$idAnnonce-modifier");
		$out='<form id="annonce" action="' . parametre_url($action_link,'','') . '" method="post">';
			$handle=opendir(_DIR_IMG);
				$j=0;
				while ($fichier = readdir($handle))
				{
					if(ereg("^immo" . $idAnnonce . "-[0-9]*.[a-zA-Z]*$", $fichier))
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
					$out.='<div class="supprimerselection"><input class="fondo" type="submit" value="Supprimer la s&eacute;l&eacute;ction" /></div>';
				}
			closedir($handle);
		$out.='</form>';
		echo $out;
		echo fin_cadre_trait_couleur(true);

		echo '<br/>';

		//Les documents
		echo debut_cadre_trait_couleur("petition-24.gif", true, "", _T("Liste des documents"));
		$action_link=generer_action_auteur("spipimmo_supprimer_document", "$idAnnonce-modifier");
		$out='<form id="annonce" action="' . parametre_url($action_link,'','') . '" method="post">';
			$handle=opendir(_DIR_IMG);
				$j=0;
				while ($fichier = readdir($handle))
				{
					if(ereg("^immo" . $idAnnonce . "-[0-9]*.[a-zA-Z]*$", $fichier))
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
									<span>Supprimer</span></label>
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
					$out.='<div class="supprimerselection"><input class="fondo" type="submit" value="Supprimer la s&eacute;l&eacute;ction" /></div>';
				}
			closedir($handle);
		$out.='</form>';
		echo $out;
		echo fin_cadre_trait_couleur(true);

		echo fin_gauche(true), fin_page(true);
	}

?>
