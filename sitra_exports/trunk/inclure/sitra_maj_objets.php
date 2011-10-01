<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// *********
// Config
// *********


// langues
$langues = explode(',',SITRA_LANGUES);

$nl = "\n";
$br = '<br />';
$hr = '<hr />';

// pour n'effectuer certains traitements que la première fois avec la première langue
$premiere_langue = true;

// *********
// fichier des listes d'objets
// *********

foreach($langues as $langue){
	$fichier_oi = trouver_fichier_prefixe(SITRA_DIR,'('.SITRA_ID_SITE.')_ListeOI_'.$langue);
	
	if (!$fichier_oi) {
		message($nl.'Pas de fichier ListeOI_'.$langue,'erreur');
		continue;
	}
	
	$fichier_oi = SITRA_DIR.$fichier_oi;
	
	message($nl.'/// Fichier '.$fichier_oi.' ///');
	$xml = simplexml_load_file($fichier_oi);
	
	// analyse de chaque objet
	foreach ($xml -> OI as $oi){
		
		$id_sitra = $oi -> DublinCore -> identifier;
		$titre_objet = $oi -> DublinCore -> title;
		// initialiser complétement le tableau, si une donnée n'est plus présente la mise à jour du champ doit se faire
		$objet = array(
			'id_sitra' => $id_sitra,
			'titre' => $titre_objet,
			'adresse' => '',
			'commune' => '',
			'code_postal' => '',
			'insee' => '',
			'telephone' => '',
			'fax' => '',
			'tel_fax' => '',
			'email' => '',
			'web' => '',
			'date_debut' => '0000-00-00 00:00:00',
			'date_fin' => '0000-00-00 00:00:00',
			'latitude' => '',
			'longitude' => '',
			'altitude' => '',
			'classement_orga' => '',
			'classement_code' => '',
			'classement' => '',
			'reservation_url' => ''
		);
		
		// même chose pour les détails
		$objet_details = array(
			'id_sitra' => $id_sitra,
			'lang' => $langue,
			'titre_lang' => '',
			'lieu' => '',
			'description' => '',
			'description_courte' => '',
			'observation_dates' => '',
			'tarifs_en_clair' => '',
			'tarifs_complementaires' => '',
			'presta_accessibilite' => '',
			'presta_activites' => '',
			'presta_confort' => '',
			'presta_encadrement' => '',
			'presta_equipements' => '',
			'presta_services' => '',
			'presta_sitra' => '',
			'langues' => '',
			'capacites' => ''
			);
		
		if (SITRA_DEBUG) echo $hr;
		message('Traitement '.$titre_objet.' - '.$id_sitra.' - '.$langue);
		
		// les prestations
		if ($oi -> OffresPrestations){
			foreach ($oi -> OffresPrestations -> DetailOffrePrestation as $val) {
				$prestations = array();
				if ($val -> DetailPrestation) {
					foreach($val -> DetailPrestation as $val2) {
						$val3 = $val2 -> Prestation;
						if ($val3['utilise'] == 'O') ajoute_si_present($prestations,$val3);
					}
					$les_prestations = serialize_non_vide($prestations);
					switch ($val['type']) {
						case '15.01': $objet_details['presta_accessibilite'] = $les_prestations; break;
						case '15.02': $objet_details['presta_activites'] = $les_prestations; break;
						case '15.03': $objet_details['presta_confort'] = $les_prestations; break;
						case '15.04': $objet_details['presta_encadrement'] = $les_prestations; break;
						case '15.05': $objet_details['presta_equipements'] = $les_prestations; break;
						case '15.06': $objet_details['presta_services'] = $les_prestations; break;
						case '15.07': $objet_details['presta_sitra'] = $les_prestations; break;
					} // fin switch
				}
			}
		} // fin if OffresPrestations
		
		// Contact
		if($oi -> Contacts -> DetailContact and $premiere_langue){
			$adresse = $telephone = $mel = $web = $fax = $tel_fax = array();
			foreach ($oi -> Contacts -> DetailContact as $val) {
				if ($val['type'] == '04.03.13' and $val -> Adresses -> DetailAdresse) {
					$adr = $val -> Adresses -> DetailAdresse;
					ajoute_si_present($adresse, $adr -> Adr1);
					ajoute_si_present($adresse, $adr -> Adr2);
					ajoute_si_present($adresse, $adr -> Adr3);
					$objet['code_postal'] = $adr -> CodePostal;
					$objet['commune'] = $adr -> Commune;
					$objet['insee'] = $adr -> Commune['code'];
					if ($adr -> Personnes -> DetailPersonne -> MoyensCommunications) {
						
						foreach ($adr -> Personnes -> DetailPersonne -> MoyensCommunications -> DetailMoyenCom as $val2){
							switch ($val2['type']) {
							case '04.02.01' : ajoute_si_present($telephone, $val2 -> Coord); break;
							case '04.02.02' : ajoute_si_present($fax, $val2 -> Coord); break;
							case '04.02.04' : ajoute_si_present($mel, $val2 -> Coord); break;
							case '04.02.05' : ajoute_si_present($web, $val2 -> Coord); break;
							case '04.02.06' : ajoute_si_present($tel_fax, $val2 -> Coord); break;
							} // fin switch
						} // foreach
					}
				}
			}
			$objet['adresse']= serialize_non_vide($adresse);
			$objet['telephone']= serialize_non_vide($telephone);
			$objet['fax'] = serialize_non_vide($fax);
			$objet['email'] = serialize_non_vide($mel);
			$objet['web'] = serialize_non_vide($web);
			$objet['tel_fax'] = serialize_non_vide($tel_fax);
		} // fin if contact
		
		// classement
		if($oi -> Classements -> DetailClassement and $premiere_langue){
			$objet['classement_orga'] = $oi -> Classements -> DetailClassement['libelle'];
			$objet['classement_code'] = $oi -> Classements -> DetailClassement -> Classement['type'];
			$objet['classement'] = $oi -> Classements -> DetailClassement -> Classement;
		}
		
		// Images et logo
		$docs = array();
		$docs_details = array();
		
		// tjs le problème des clés qui doivent être des alphanum
		$types_docs = array(
			'i_03.01.05' => 'principale',
			'i_03.01.01' => 'secondaire',
			'i_03.01.08' => 'logo'
		);
		
		if($oi -> Multimedia -> DetailMultimedia){
			$i = 0;
			foreach ($oi -> Multimedia -> DetailMultimedia as $val){
				if (array_key_exists('i_'.$val['type'],$types_docs)){
					if ($premiere_langue){
						$docs[$i]['id_sitra'] = $id_sitra;
						$docs[$i]['num_doc'] = $i;
						$docs[$i]['type_doc'] = $types_docs['i_'.$val['type']];
						$docs[$i]['url_doc'] = $val -> URL;
						// seules les images principales sont importées eventuellement
						if ($docs[$i]['type_doc'] != 'principale')
							$docs[$i]['lien'] = 'O';
						else
							$docs[$i]['lien'] = $val['lien'];
						$extension = substr(strrchr($val -> URL,'.'),1);
						$docs[$i]['extension'] = strtolower($extension);
					} // fin if premiere_langue
					$docs_details[$i]['id_sitra'] = $id_sitra;
					$docs_details[$i]['num_doc'] = $i;
					$docs_details[$i]['lang'] = $langue;
					$docs_details[$i]['titre'] = $val -> Nom;
					$docs_details[$i]['descriptif'] = $val -> LegendeRessource;
					$docs_details[$i]['copyright'] = $val -> Copyright;
					$i++;
				} // fin if
			} // fin foreach
		} // fin if multimedia
		
		
		// autres détails
		if ($oi -> DescriptionsComplementaires -> DetailDescriptionComplementaire){
			foreach ($oi -> DescriptionsComplementaires -> DetailDescriptionComplementaire as $val){
				$descr = $val -> Description;
				switch ($descr['type']){
					case '16.01.05': $objet_details['titre_lang'] = $descr; break;
					case '16.02.28': $objet_details['description_courte'] = $descr; break;
					case '16.02.30': $objet_details['description'] = $descr; break;
					case '16.02.38': $objet_details['tarifs_complementaires'] = $descr; break;
					case '16.02.42': $objet_details['lieu'] = $descr; break;
					case '16.02.67': $objet_details['tarifs_en_clair'] = $descr; break;
				} // fin switch
			}
		}
		
		// les langues
		if ($oi -> Langues -> Usage -> Langue){
			$langues = array();
			foreach ($oi -> Langues -> Usage as $val){
				ajoute_si_present($langues, $val -> Langue);
			}
			$objet_details['langues'] = serialize_non_vide($langues);
		}
		
		// Capacités
		if ($oi -> Capacites){
			$capacites = array();
			foreach ($oi -> Capacites -> CapacitesPrestations as $presta_capacite){
				foreach($presta_capacite -> DetailCapacitePrestation as $val){
					if ($val['utilise'] == 'O'){
						$details = '';
						foreach($val -> Capacite as $capa){
							if ($capa > 0)
								$details .= $capa.' '.strtolower(substr($capa['libelle'],0,1)).'. ';
						}
						if ($details)
							ajoute_si_present($capacites, $val['libelle'].': '.$details);
					}
				}
			}
			$objet_details['capacites'] = serialize_non_vide($capacites);
		}
		
		// Criteres internes
		if ($oi -> CriteresInternes  and $premiere_langue){
			$i = 0;
			$criteres = array();
			foreach($oi -> CriteresInternes -> CritereInterne as $val){
				$criteres[$i] = array(
					'id_sitra' => $id_sitra,
					'id_critere' => $val['code'],
				);
				$i++;
			}
		}
		
		// geolocalisation
		if ($oi -> Geolocalisations -> DetailGeolocalisation -> Zone -> Points -> DetailPoint -> Coordonnees and $premiere_langue){
			foreach ($oi -> Geolocalisations -> DetailGeolocalisation -> Zone -> Points -> DetailPoint -> Coordonnees -> DetailCoordonnees as $val){
				$type = $val['type'];
				if ($type == '08.02.02.03'){
					$objet['longitude'] = $val -> Longitude;
					$objet['latitude'] = $val -> Latitude;
					$objet['altitude'] = $val -> Altitude;
				}
			}
		}
		
		// Ouverture
		$date_debut = $date_fin = $observations_dates = '';
		if ($oi -> Periodes -> DetailPeriode  and $premiere_langue){
			foreach ($oi -> Periodes -> DetailPeriode as $val) {
				if ($val['type']=='09.01.06') {
					$objet['date_debut'] = date_norme($val -> Dates -> DetailDates -> DateDebut);
					$objet['date_fin'] = date_norme($val -> Dates -> DetailDates -> DateFin);
					$objet_details['observation_dates'] = $val -> Dates -> DetailDates -> ObservationDates;
				}
			}
		}
		
		// les catégories
		$categories = array();
		$i = 0;
		if ($oi -> DublinCore -> Classification and $premiere_langue){
			$categories[$i]['id_sitra'] = $id_sitra;
			$categories[$i]['id_categorie'] = $oi -> DublinCore -> Classification['code'];
			$categories[$i]['categorie'] = normalise_nom($oi -> DublinCore -> Classification['libelle']);
		}
		
		if ($oi -> DublinCore -> ControlledVocabulary and $premiere_langue){
			foreach($oi -> DublinCore -> ControlledVocabulary as $val) {
				if ($val['utilise'] == 'O') {
					$i++;
					$categories[$i]['id_sitra'] = $id_sitra;
					$categories[$i]['id_categorie'] = $val['code'];
					$categories[$i]['categorie'] = normalise_nom($val['libelle']);
				}
			}
		}
		
		// reservation
		if ($oi -> ModesReservations -> DetailModeReservation -> Contacts and $premiere_langue){
			$reservation_url = array();
			foreach($oi -> ModesReservations -> DetailModeReservation -> Contacts -> DetailContact -> Adresses -> DetailAdresse -> Personnes -> DetailPersonne -> MoyensCommunications -> DetailMoyenCom as $val){
				if ($val['type'] == '04.02.05')
					ajoute_si_present($reservation_url, $val -> Coord);
			}
			$objet['reservation_url'] = serialize_non_vide($reservation_url);
		} // fin resa

		// controle des valeurs de $obj
		if (SITRA_DEBUG){
			sitra_debug('objet', $objet);
			sitra_debug('objet_details',$objet_details);
			sitra_debug('categories',$categories);
			sitra_debug('docs',$docs);
			sitra_debug('docs_details',$docs_details);
			sitra_debug('criteres',$criteres);
		}
		
		// On met à jour la base
		
		
		$where = 'id_sitra = \''.$id_sitra.'\'';
		$where_langue = 'lang = \''.$langue.'\'';
		
		if ($premiere_langue) {
			// on cherche si objet déjà présent dans la table pour mise à jour ou création
			$id_sitra_objet = 0;
			$id_sitra_objet = sql_getfetsel('id_sitra_objet','spip_sitra_objets',$where);
			// mise à jour ou insertion dans sitra_objets
			if ($id_sitra_objet)
				$r = sql_updateq('spip_sitra_objets',$objet,'id_sitra_objet='.$id_sitra_objet);
			else
				$r = sql_insertq('spip_sitra_objets',$objet);
			
			// mise à jour table sitra_categories
			sql_delete('spip_sitra_categories', $where);
			if (count($categories))
				sql_insertq_multi('spip_sitra_categories', $categories);
			
			// mise à jour table criteres
			sql_delete('spip_sitra_criteres', $where);
			if (count($criteres))
				sql_insertq_multi('spip_sitra_criteres', $criteres);
			
			// docs
			sql_delete('spip_sitra_docs', $where);
			if (count($docs))
				sql_insertq_multi('spip_sitra_docs',$docs);
			
			// on supprime toutes les données dans les tables annexes qqsoit la langue
			sql_delete('spip_sitra_docs_details', $where);
			sql_delete('spip_sitra_objets_details', $where);
		} // fin if premiere_langue
		
		// les details
			$r = sql_insertq('spip_sitra_objets_details',$objet_details);
		
		// les details docs
		if (count($docs_details))
			$r = sql_insertq_multi('spip_sitra_docs_details',$docs_details);
		
		if (SITRA_DEBUG) echo '//////';
		if ($id_sitra_objet)
			message('mise a jour base : '.$titre_objet.' - '.$id_sitra.' - '.$langue);
		else
			message('import base : '.$titre_objet.' '.$id_sitra.' - '.$langue);
		
	}// fin foreach $oi On passe à l'objet suivant
	
	// si pas en mode debug on supprime le fichier lissteOI
	if (!SITRA_DEBUG) {
		unlink($fichier_oi);
		message('Suppression fichier '.$fichier_oi);
	}

	$premiere_langue = false;
	
}// fin foreach $langue on passe à la langue suivante

?>