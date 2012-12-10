<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');

	function exec_ajouter_annonce()
	{
		//initialisation sortie
		$out="";

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

		echo debut_cadre_trait_couleur("mot-cle-24.gif", true, "", _T("Cr&eacute;er une annonce"));

		$action_link=generer_action_auteur("spipimmo_ajouter_annonce", '');

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
							<td class="saisie_annonce">En cours de traitement</td>
						</tr>

						<tr id="tr_vente_location">
							<td class="libelle_annonce">Vente/Location</td>
							<td class="saisie_annonce">
								<select name="vente_location" onchange="javascript:formObligatoire(\'vente_location\', \'tr_vente_location\');">
									<option selected="selected" value=""></option>
									<option value="Vente">Vente</option>
									<option value="Location">Location</option>
								</select>
							</td>
						</tr>

						<tr id="tr_numero_mandat">
							<td class="libelle_annonce">Num&eacute;ro mandat</td>
							<td class="saisie_annonce"><input type="text" name="numero_mandat" /></td>
						</tr>

						<tr id="tr_type_mandat">
							<td class="libelle_annonce">Type mandat</td>
							<td class="saisie_annonce">
								<select name="type_mandat" onchange="javascript:formObligatoire(\'type_mandat\', \'tr_type_mandat\');">
									<option value=""></option>
									<option value="SIMPLE">SIMPLE</option>
									<option value="SEMI PRIVILEGE">SEMI PRIVILEGE</option>
									<option value="PRIVILEGE">PRIVILEGE</option>
									<option value="EXCLUSIF">EXCLUSIF</option>
								</select>
							</td>
						</tr>

						<tr id="tr_negociateur">
							<td class="libelle_annonce">N&eacute;gociateur</td>
							<td class="saisie_annonce"><input type="text" name="negociateur" /></td>
						</tr>

						<tr id="tr_honoraire">
							<td class="libelle_annonce">Honoraires</td>
							<td class="saisie_annonce"><input type="text" name="honoraire" /> &euro;</td>
						</tr>

						<tr id="tr_publier_offre" >
							<td class="libelle_annonce">Publier offre</td>
							<td class="saisie_annonce">
								<select name="publier_offre" onchange="javascript:formObligatoire(\'publier_offre\', \'tr_publier_offre\');">
									<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</td>
						</tr>
					</table>
				</div>

				<div id="tableau2">
					<table class="annonce">
						<tr id="tr_type_offre">
							<td class="libelle_annonce">Type offre</td>
							<td class="saisie_annonce">
								<select name="type_offre" onchange="javascript:formObligatoire(\'type_offre\', \'tr_type_offre\');">
									<option selected="selected" value=""></option>';
									$listeTypesOffres=sql_select("*", "spip_types_offres");
									$nbTypesOffres=sql_count($listeTypesOffres);
									for ($i=0; $i<$nbTypesOffres; $i++)
									{
										$typeOffre=sql_fetch($listeTypesOffres);
										$out.='<option value="' . $typeOffre["libelle_offre"] . '">' . $typeOffre["libelle_offre"] . '</option>';
									}
							$out.='</select>
							</td>
						</tr>

						<tr id="tr_adresse_1">
							<td class="libelle_annonce">Adresse n&deg;1</td>
							<td class="saisie_annonce"><input type="text" size="45" name="adresse_1" /></td>
						</tr>

						<tr id="tr_adresse_2">
							<td class="libelle_annonce">Adresse n&deg;2</td>
							<td class="saisie_annonce"><input type="text" size="45" name="adresse_2" /></td>
						</tr>

						<tr id="tr_code_postal">
							<td class="libelle_annonce">Code postal</td>
							<td class="saisie_annonce"><input type="text" size="5" id="code_postal" name="code_postal" onblur="javascript:formObligatoire(\'code_postal\', \'tr_code_postal\');" onkeyup="javascript:formObligatoire(\'code_postal\', \'tr_code_postal\');" /></td>
						</tr>

						<tr id="tr_code_postal_internet">
							<td class="libelle_annonce">Code postal (internet)</td>
							<td class="saisie_annonce"><input type="text" size="5" name="code_postal_internet" /></td>
						</tr>

						<tr id="tr_ville">
							<td class="libelle_annonce">Ville</td>
							<td class="saisie_annonce"><input type="text" name="ville" id="ville" onblur="javascript:formObligatoire(\'ville\', \'tr_ville\');" onkeyup="javascript:formObligatoire(\'ville\', \'tr_ville\');" /></td>
						</tr>

						<tr id="tr_ville_internet">
							<td class="libelle_annonce">Ville (internet)</td>
							<td class="saisie_annonce"><input type="text" name="ville_internet" /></td>
						</tr>

						<tr id="tr_prix_loyer">
							<td class="libelle_annonce">Prix/loyer</td>
							<td class="saisie_annonce"><input type="text" size="7" name="prix_loyer" onblur="javascript:formObligatoire(\'prix_loyer\', \'tr_prix_loyer\');" onkeyup="javascript:formObligatoire(\'prix_loyer\', \'tr_prix_loyer\');" /> &euro;</td>
						</tr>

						<tr id="tr_taxe_habitation">
							<td class="libelle_annonce">Taxe d\'habitation</td>
							<td class="saisie_annonce"><input type="text" size="7" name="taxe_habitation" /> &euro;</td>
						</tr>

						<tr id="tr_taxe_fonciere">
							<td class="libelle_annonce">Taxe fonci&egrave;re</td>
							<td class="saisie_annonce"><input type="text" size="7" name="taxe_fonciere" /> &euro;</td>
						</tr>

						<tr id="tr_travaux">
							<td class="libelle_annonce">Travaux</td>
							<td class="saisie_annonce"><input type="text" size="7" name="travaux" /> &euro;</td>
						</tr>

						<tr id="tr_charge">
							<td class="libelle_annonce">Charges</td>
							<td class="saisie_annonce"><input type="text" size="7" name="charge" /> &euro;</td>
						</tr>
						
						<tr id="tr_dpe">
							<td class="libelle_annonce">DPE</td>
							<td class="saisie_annonce">
								<select name="dpe">
									<option value=""></option>
									<option value="A">A -50</option>
									<option value="B">B 51 &agrave; 90</option>
									<option value="C">C 91 &agrave; 150</option>
									<option value="D">D 151 &agrave; 230</option>
									<option value="E">E 231 &agrave; 330</option>
									<option value="F">F 331 &agrave; 450</option>
									<option value="G">G >450</option>
									<option value="N.C">N.C Non communiqu&eacute;</option>
								</select>
							</td>
						</tr>
						
					</table>
				</div>

				<div id="tableau3">
					<table class="annonce">

						<tr id="tr_depot_garantie">
							<td class="libelle_annonce">D&eacute;p&ocirc;t garantie</td>
							<td class="saisie_annonce"><input type="text" size="7" name="depot_garantie" /> &euro;</td>
						</tr>

						<tr id="tr_date_disponibilite">
							<td class="libelle_annonce">Date de disponibilit&eacute;</td>
							<td class="saisie_annonce">
								<select name="jour_dispo">
									<option value="" /></option>';
									for($i=1; $i<=31; $i++)
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								$out.='</select>
								&nbsp;
								<select name="mois_dispo">
									<option value="" /></option>';
									for($i=1; $i<=12; $i++)
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								$out.='</select>
								&nbsp;
								<select name="annee_dispo">
									<option value="" /></option>';
									for($i=date('Y'); $i<=date('Y')+3; $i++)
									{
										$out.='<option value="' . $i . '" />' . $i . '</option>';
									}
								$out.='</select>
							</td>
						</tr>

						<tr id="tr_quartier">
							<td class="libelle_annonce">Quartier</td>
							<td class="saisie_annonce"><input type="text" name="quartier" /></td>
						</tr>

						<tr id="tr_secteur">
							<td class="libelle_annonce">Secteur</td>
							<td class="saisie_annonce"><input type="text" name="secteur" /></td>
						</tr>

						<tr id="tr_residence">
							<td class="libelle_annonce">R&eacute;sidence</td>
							<td class="saisie_annonce"><input type="text" name="residence" /></td>
						</tr>

						<tr id="tr_transport">
							<td class="libelle_annonce">Transport(s)</td>
							<td class="saisie_annonce"><input type="text" size="30" name="transport" /></td>
						</tr>

						<tr id="tr_proximite">
							<td class="libelle_annonce">Proximit&eacute;(s)</td>
							<td class="saisie_annonce"><input type="text" size="30" name="proximite" /></td>
						</tr>

						<tr id="tr_annee_construction">
							<td class="libelle_annonce">Ann&eacute;e de construction</td>
							<td class="saisie_annonce"><input type="text" size="4" name="annee_construction" /></td>
						</tr>

						<tr id="tr_prestige">
							<td class="libelle_annonce">Prestige</td><td class="saisie_annonce"><select name="prestige" onchange="javascript:formObligatoire(\'prestige\', \'tr_prestige\');">
								<option selected="selected" value=""></option>
								<option value="1">Oui</option>
								<option value="0">Non</option>
							</select></td>
						</tr>
					</table>
				</div>

				<div id="tableau4">
					<table class="annonce">
						<tr id="tr_categorie">
							<td class="libelle_annonce">Cat&eacute;gorie</td>
							<td class="saisie_annonce">
								<select name="categorie" />
									<option value="" selected="selected"></option>
									<option value="Maison">Maison</option>
									<option value="Appartement">Appartement</option>
									<option value="Studio">Studio</option>
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_piece">
							<td class="libelle_annonce">Nombre de pi&egrave;ce(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_piece">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_PIECE) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_chambre">
							<td class="libelle_annonce">Nombre de chambre(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_chambre">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_CHAMBRE) . '
								</select>
							</td>
						</tr>

						<tr id="tr_surface_habitable">
							<td class="libelle_annonce">Surface habitable</td>
							<td class="saisie_annonce"><input type="text" size="5" name="surface_habitable" onblur="javascript:formObligatoire(\'surface_habitable\', \'tr_surface_habitable\');" onkeyup="javascript:formObligatoire(\'surface_habitable\', \'tr_surface_habitable\');" /> m&sup2;</td>
						</tr>

						<tr id="tr_surface_carre">
							<td class="libelle_annonce">Surface carrez</td>
							<td class="saisie_annonce"><input type="text" size="5" name="surface_carre" /> m&sup2;</td>
						</tr>

						<tr id="tr_surface_sejour">
							<td class="libelle_annonce">Surface s&eacute;jour</td><td class="saisie_annonce">
							<input type="text" size="5" name="surface_sejour" /> m&sup2;</td>
						</tr>

						<tr id="tr_surface_terrain">
							<td class="libelle_annonce">Surface terrain</td>
							<td class="saisie_annonce"><input type="text" size="5" name="surface_terrain" /> m&sup2;</td>
						</tr>

						<tr id="tr_etage">
							<td class="libelle_annonce">Etage</td>
							<td class="saisie_annonce"><input type="text" size="2" name="etage" /></td>
						</tr>

						<tr id="tr_code_etage">
							<td class="libelle_annonce">Code &eacute;tage</td>
							<td class="saisie_annonce"><input type="text" size="2" name="code_etage" /></td>
						</tr>

						<tr id="tr_nombre_etage">
							<td class="libelle_annonce">Nombre d\'&eacute;tage(s)</td>
							<td class="saisie_annonce"><input type="text" size="2" name="nombre_etage" /></td>
						</tr>
					</table>
				</div>

				<div id="tableau5">
					<table class="annonce">
						<tr id="tr_type_cuisine">
							<td class="libelle_annonce">Type de cuisine</td>
							<td class="saisie_annonce"><input type="text" name="type_cuisine" /></td>
						</tr>

						<tr id="tr_nombre_wc">
							<td class="libelle_annonce">Nombre de toilette(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_wc">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_WC) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_bain">
							<td class="libelle_annonce">Nombre de salle(s) de bains</td>
							<td class="saisie_annonce">
								<select name="nombre_bain">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_SALLE_BAIN) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_eau">
							<td class="libelle_annonce">Nombre de salle(s) d\'eau</td>
							<td class="saisie_annonce">
								<select name="nombre_eau" >
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_SALLE_EAU) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_parking_interieur">
							<td class="libelle_annonce">Nombre parking(s) int&eacute;rieur(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_parking_interieur">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_PARK_INT) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_parking_exterieur">
							<td class="libelle_annonce">Nombre parking(s) ext&eacute;rieur(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_parking_exterieur">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_PARK_EXT) . '
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_garage">
							<td class="libelle_annonce">Nombre de garage(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_garage">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_GARAGE) . '
								</select>
							</td>
						</tr>

						<tr id="tr_type_sous_sol">
							<td class="libelle_annonce">Type de sous-sol</td>
							<td class="saisie_annonce"><input type="text" name="type_sous_sol" /></td>
						</tr>

						<tr id="tr_nombre_cave">
							<td class="libelle_annonce">Nombre de cave(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_cave">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_CAVE) . '
								</select>
							</td>
						</tr>

						<tr id="tr_type_chauffage">
							<td class="libelle_annonce">Type de chauffage</td>
							<td class="saisie_annonce"><input type="text" name="type_chauffage" /></td>
						</tr>

						<tr id="tr_nature_cuisine">
							<td class="libelle_annonce">Nature du chauffage</td>
							<td class="saisie_annonce"><input type="text" name="nature_chauffage" /></td>
						</tr>

						<tr id="tr_ascenseur">
							<td class="libelle_annonce">Ascenseur</td>
							<td class="saisie_annonce">
								<select name="ascenseur">
									<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</td>
						</tr>

						<tr id="tr_balcon">
							<td class="libelle_annonce">Balcon</td>
							<td class="saisie_annonce">
								<select name="balcon">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_BALCON) . '
								</select>
							</td>
						</tr>

						<tr id="tr_terrasse">
							<td class="libelle_annonce">Terrasse</td>
							<td class="saisie_annonce">
								<select name="terrasse">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_TERRASSE) . '
								</select>
							</td>
						</tr>

						<tr id="tr_piscine">
							<td class="libelle_annonce">Piscine</td>
							<td class="saisie_annonce">
								<select name="piscine">
									<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</td>
						</tr>

						<tr id="tr_acces_handicape">
							<td class="libelle_annonce">Acc&egrave;s personnes &agrave; mobilit&eacute; r&eacute;duite</td>
							<td class="saisie_annonce">
								<select name="acces_handicape">
									<option selected="selected" value=""></option>
									<option value="1">Oui</option>
									<option value="0">Non</option>
								</select>
							</td>
						</tr>

						<tr id="tr_nombre_mur">
							<td class="libelle_annonce">Nombre de mur(s) mitoyen(s)</td>
							<td class="saisie_annonce">
								<select name="nombre_mur">
									' . liste_deroulante_piece("", _SPIPIMMO_NBPIECE_MUR_MITOYEN) . '
								</select>
							</td>
						</tr>

						<tr id="tr_facade">
							<td class="libelle_annonce">Facade</td>
							<td class="saisie_annonce"><input type="text" size="3" name="facade" /> m</td>
						</tr>
					</table>
				</div>

				<div id="tableau6">
					<table class="annonce">

						<tr id="tr_texte_francais">
							<td class="libelle_annonce">Texte annonce (fran&ccedil;ais)</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_francais" onblur="javascript:formObligatoire(\'texte_francais\', \'tr_texte_francais\');" onkeyup="javascript:formObligatoire(\'texte_francais\', \'tr_texte_francais\');"></textarea></td>
						</tr>

						<tr id="tr_texte_anglais">
							<td class="libelle_annonce">Texte annonce (anglais)</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_anglais"></textarea></td>
						</tr>

						<tr id="tr_texte_allemand">
							<td class="libelle_annonce">Texte annonce (allemand)</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_allemand"></textarea></td>
						</tr>

						<tr id="tr_texte_espagnol">
							<td class="libelle_annonce">Texte annonce (espagnol)</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_espagnol"></textarea></td>
						</tr>

						<tr id="tr_texte_italien">
							<td class="libelle_annonce">Texte annonce (italien)</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_italien"></textarea></td>
						</tr>

						<tr id="tr_texte_mailing">
							<td class="libelle_annonce">Texte mailing</td>
							<td class="saisie_annonce"><textarea rows="6"cols="40" name="texte_mailing"></textarea></td>
						</tr>
					</table>
				</div>

				<div>
					<table>
						<tr>
							<td colspan="2" class="obligatoire">Les champs surlign&eacute;s en orange sont obligatoires, merci de les compl&eacute;ter. Seul l\'onglet "descriptif (suite)" ne comporte pas de champs obligatoire.</td>
						</tr>
						<tr>
							<td colspan="2" class="bouton" style="vertical-align:bottom;">
								&nbsp;&nbsp;<input class="fondo" type="submit" value="Enregistrer" />
							</td>
						</tr>
					</table>
				</div>
			</form>';

			echo $out;

		echo fin_cadre_trait_couleur(true);
		echo fin_gauche(true), fin_page(true);
	}
?>
