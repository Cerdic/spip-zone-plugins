<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Autoloader
 * @throws Exception
 */
function coordonnees_loader() {
	static $done = false;
	if (!$done) {
		$done = true;
		require_once __DIR__ . '/../vendor/autoload.php';
	}
}


/**
 * Lister les saisies nécessaires par champ de l'API Addressing
 */
function coordoonnes_adresses_addressing_saisies() {
	$saisies = array(
		'addressLine1' => array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'voie',
				'label' => _T('coordonnees:label_voie'),
			),
		),
		'addressLine2' => array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'complement',
				'label' => _T('coordonnees:label_complement'),
				'placeholder' => _T('coordonnees:placeholder_complement_adresse')
			),
		),
		'postalCode' => array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'code_postal',
				'label' => _T('coordonnees:label_code_postal')
			),
		),
		'locality' => array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'ville',
				'label' => _T('coordonnees:label_ville')
			),
		),
		'administrativeArea' => array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'zone_administrative',
				'label' => _T('coordonnees:label_zone_administrative')
			),
		),
	);
	
	return $saisies;
}

/**
 * Chercher les bonnes saisies à afficher pour l'adresse d'un pays donné
 * 
 * @param string $code_pays
 * 		Code ISO du pays
 * @return
 * 		Retourne un tableau de saisies dans le bon ordre et avec les bons termes suivant le pays
 */
function coordonnees_adresses_saisies_par_pays($code_pays, $adresse_obligatoire=false) {
	$saisies = array();
	
	if ($code_pays = strtoupper($code_pays)) {
		// Si on trouve une fonction en SPIP qui les définit, c'est ça en priorité
		// ce qui permet de personnaliser encore plus que ce qu'on va générer automatiquement avec la lib
		if ($fonction_pays = charger_fonction("$code_pays", 'adresses/', true)) {
			$saisies = $fonction_pays();
		}
		// Sinon on va générer ça avec la super lib !
		else {
			coordonnees_loader();
			
			$addressFormatRepository = new CommerceGuys\Addressing\AddressFormat\AddressFormatRepository();
			
			// Si on trouve un description pour ce code pays
			if ($addressFormat = $addressFormatRepository->get($code_pays)) {
				$format = $addressFormat->getFormat();
				$obligatoires = $addressFormat->getRequiredFields();
				
				// On scanne la chaine pour lister les champs dans l'ordre attendu
				preg_match_all('|%(\w+)|', $format, $trouver);
				$champs = $trouver[1];
				
				// Chaque champ de la lib correspond à une saisie de chez nous
				$mapping = coordoonnes_adresses_addressing_saisies();
				foreach ($champs as $champ) {
					// Si on trouve bien une saisie pour un des champs de la lib
					if (isset($mapping[$champ])) {
						$saisie = $mapping[$champ];
						
						// Si l'adresse à remplir est obligatoire et que ce champ l'est
						if ($adresse_obligatoire and in_array($champ, $obligatoires)) {
							$saisie['options']['obligatoire'] = 'oui';
						}
						
						// Pour le code postal, on personnalise
						if ($champ == 'postalCode') {
							// S'il y a un type, on utilise son label si différent du code postal
							if (($postal_type = $addressFormat->getPostalCodeType()) != 'postal') {
								$saisie['options']['label'] = _T("coordonnees:adresse_champ_code_postal_${postal_type}_label");
							}
							
							// S'il y un une regex qu'on peut tester, utilisons là
							if ($postal_pattern = $addressFormat->getPostalCodePattern()) {
								$saisie['verifier'] = array(
									'type' => 'regex',
									'options' => array(
										'modele' => "|$postal_pattern|",
									),
								);
							}
						}
						
						// Pour la ville, on personnalise
						if ($champ == 'locality') {
							// S'il y a un type, on utilise son label si différent du code postal
							if (($ville_type = $addressFormat->getLocalityType()) != 'city') {
								$saisie['options']['label'] = _T("coordonnees:adresse_champ_ville_${ville_type}_label");
							}
						}
						
						// Pour la zone administrative, on personnalise
						if ($champ == 'administrativeArea') {
							// S'il y a un type, on utilise son label
							if ($zone_type = $addressFormat->getAdministrativeAreaType()) {
								$saisie['options']['label'] = _T("coordonnees:adresse_champ_zone_administrative_${zone_type}_label");
							}
						}
						
						// On ajoute la saisie générée
						$saisies[] = $saisie;
					}
				}
			}
		}
	}
	
	
	
	return $saisies;
}
