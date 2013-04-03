<?php
/**
 * Plugin Coordonnees pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function coordonnees_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['adresses'] = 'adresses';
	$interface['table_des_tables']['numeros'] = 'numeros';
	$interface['table_des_tables']['emails'] = 'emails';

	$interface['tables_jointures']['spip_auteurs'][] = 'adresses_liens';
	$interface['tables_jointures']['spip_adresses'][] = 'adresses_liens';

	$interface['tables_jointures']['spip_auteurs'][] = 'numeros_liens';
	$interface['tables_jointures']['spip_numeros'][] = 'numeros_liens';

	$interface['tables_jointures']['spip_auteurs'][] = 'emails_liens';
	$interface['tables_jointures']['spip_emails'][] = 'emails_liens';

	$interface['tables_jointures']['spip_auteurs'][] = 'syndic_liens';
	$interface['tables_jointures']['spip_articles'][] = 'syndic_liens';
	$interface['tables_jointures']['spip_breves'][] = 'syndic_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'syndic_liens';
	$interface['tables_jointures']['spip_syndic'][] = 'syndic_liens';

	$interface['table_des_traitements']['VILLE'][] = _TRAITEMENT_TYPO;

	return $interface;
}


function coordonnees_declarer_tables_principales($tables_principales){

	//-- Table adresses ------------------------------------------
	$adresses = array(
		"id_adresse" => "BIGINT NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // perso, pro, vacance...
		"voie" => "TINYTEXT DEFAULT '' NOT NULL", // p. ex. 21 rue de cotte
		"complement" => "TINYTEXT DEFAULT '' NOT NULL", // p. ex. 3e etage
		"boite_postale" => "VARCHAR(40) DEFAULT '' NOT NULL", // attention : outre le numero de CP/BP, il est mentionne la localite si elle est differente du "bureau distributeur". ce champ peut contenir un autre nom (affichage au meme endroit) en plus du nom de la ville dans certains cas : Argentine (nom de "quartier"), Bielorussie (nom de "Raion" ici et nom de "village" en complement en zone rurale), Bresil (nom de "quartier"), Chilie (cp a Santiago et nom de "quartier" ailleurs, le nom plus loin etant celui de la "municipalite" de rattachement), Chine (nom de "district"), Grande Bretagne (nom de "localite" si different de la ville postale), , Hong-Kong (nom de "district"), Inde (nom de "village" en complement puis nom de "district" ici, ou nom de "localite" ici puis nom de ville de distribution/rattachement plus loin), Indonesie (Village/Kelurahan, Sous-district/Kecamatan), Iran ("Premise"), Irak (nom de "Mahla" et numero, l'autre nom plus loin etant le nom de "district"), Letonie (noms "village" puis "paroise" en milieu rural et nom "ville" en milieu urbain, l'autre nom etant pour la municipalite de reference), Mexica ("city ward"), Pakistan (nom de "district"), Perou (nom de "district" pour les municipalites de Lima et Callao), Roumanie (nom de "localite" suivi de "jud.", l'autre nom/numero plus loin etant celui du "comte" precede de "sector"), Russie (nom de "sous-region"), Taiwan (nom de "localite", l'autre nom plus loin etant celui du "district" ou l'inverse je sais pas), Turkie (nom de "localite", l'autre nom plus loin etant celui du "district" ou l'inverse je sais pas),
		"code_postal" => "VARCHAR(40) DEFAULT '' NOT NULL", // pays qui utilisent et format : http://fr.wikipedia.org/wiki/Code_postal
		"ville" => "TINYTEXT DEFAULT '' NOT NULL", // en fait la "commune du bureau de distribution" ou "ville postale" quand il y a un code postal...
		"region" => "VARCHAR(40) DEFAULT '' NOT NULL", // usage en : Argentine (region), Biolorussie (departement?), Australie (code "etat"), Bresil (dode "etat"), Canada (code/nom "province"), Chine (nom de "province"), Espagne (nom "province"), Etats-Unis (code "etat"), Hong Kong (nom du "district"), Inde (nom "etat"), Indonesie (nom "province"), Iran (nom "province"), Irak (nom "province"), Italie (abreviation "province"), Malaisie (nom "etat"), Mexico (nom "etat"), Oman (nom "zone?"), Pakistan (nom "province"), Perou (nom de "province" en dehors des regions de Lima et Callao), Russie (nom "region"), Taiwan (nom de "comte"), Turquie, Ukraine (noms "raion, region"),
		"pays" => "VARCHAR(3) DEFAULT '' NOT NULL", // peut etre sur 2 caracteres (codes : ISO alpha ou numerique, ONU, etc.) ou sur 3 caracterse (codes : ISO alpha, CIO, etc.)
		"maj" => "TIMESTAMP"
		);
	$adresses_key = array(
		"PRIMARY KEY"	=> "id_adresse",
		"KEY iso3166"	=> "pays",
		"KEY zip"	=> "region, code_postal"
		);
	$tables_principales['spip_adresses'] =
		array(
			'field' => &$adresses, 'key' => &$adresses_key);

	//-- Table numeros ------------------------------------------
	$numeros = array(
		"id_numero" => "BIGINT NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // peut etre domicile, bureau, portable
		"numero" => "VARCHAR(255) DEFAULT '' NOT NULL",
		"maj" => "TIMESTAMP"
		);
	$numeros_key = array(
		"PRIMARY KEY" => "id_numero",
		"KEY numero"	=> "numero" // on ne met pas unique pour le cas ou 2 contacts partagent le meme numero
		);
	$tables_principales['spip_numeros'] =
		array('field' => &$numeros, 'key' => &$numeros_key);

	//-- Table emails ------------------------------------------
	$emails = array(
		"id_email" => "BIGINT NOT NULL auto_increment",
		"titre" => "VARCHAR(255) DEFAULT '' NOT NULL", // alias: perso, boulot, etc.
		"format" => "VARCHAR(9) DEFAULT 'internet' NOT NULL", // pour les vCard : 'internet'|'x400' ; pour nombre de proxy : 'SMTP'|'X400'|'SIP' ...parfois (Lotus) 'Inet'|'X.400' pfff
		"email" => "VARCHAR(255) DEFAULT '' NOT NULL",
		"maj" => "TIMESTAMP"
		);
	$emails_key = array(
		"PRIMARY KEY"	=> "id_email",
		"KEY email"	=> "email" // on ne met pas unique pour le cas ou 2 contacts partagent le meme mail generique
		);
	$tables_principales['spip_emails'] =
		array('field' => &$emails, 'key' => &$emails_key);


	return $tables_principales;

}



function coordonnees_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table adresses_liens ---------------------------------------
	$adresses_liens = array(
		"id_adresse" => "BIGINT NOT NULL",
		"id_objet" => "BIGINT NOT NULL",
		"objet" => "VARCHAR(25) NOT NULL", // peut etre un compte ou un contact
		'type' => "VARCHAR(25) NOT NULL DEFAULT ''"
	);
	$adresses_liens_key = array(
		"PRIMARY KEY" => "id_adresse, id_objet, objet, type", // on rajoute le type car on en rajoute un par liaison et qu'il peut y en avoir plusieurs
		"KEY id_adresse" => "id_adresse"
	);
	$tables_auxiliaires['spip_adresses_liens'] =
		array('field' => &$adresses_liens, 'key' => &$adresses_liens_key);


	//-- Table numeros_liens ------------------------------------------
	$numeros_liens = array(
		"id_numero" => "BIGINT NOT NULL DEFAULT 0",
		"id_objet" => "BIGINT NOT NULL DEFAULT 0",
		"objet" => "VARCHAR(25) NOT NULL", // peut etre un contact ou un compte
		'type' => "VARCHAR(25) NOT NULL DEFAULT ''"
	);
	$numeros_liens_key = array(
		"PRIMARY KEY" => "id_numero, id_objet, objet, type", // on rajoute le type car on en rajoute un par liaison et qu'il peut y en avoir plusieurs
		"KEY id_numero" => "id_numero"
		);
	$tables_auxiliaires['spip_numeros_liens'] =
		array('field' => &$numeros_liens, 'key' => &$numeros_liens_key);


	//-- Table emails_liens ------------------------------------------
	$emails_liens = array(
		"id_email" => "BIGINT NOT NULL DEFAULT 0",
		"id_objet" => "BIGINT NOT NULL DEFAULT 0",
		"objet" => "VARCHAR(25) NOT NULL", // peut etre un contact ou un compte
		'type' => "VARCHAR(25) NOT NULL DEFAULT ''"
		);
	$emails_liens_key = array(
		"PRIMARY KEY" => "id_email, id_objet, objet, type", // on rajoute le type car on en rajoute un par liaison et qu'il peut y en avoir plusieurs
		"KEY id_email" => "id_email"
		);
	$tables_auxiliaires['spip_emails_liens'] =
		array('field' => &$emails_liens, 'key' => &$emails_liens_key);


	//-- Table syndic_liens ------------------------------------------
	// nota: "syndic" (sans S final) est le nom hitorique de la table
	// mais la boucle est SYNDICATIONS ou SITES (synonyme) pour les
	// "sites" references (la table "urls" est reservee aux URLs propres.)
	// ET C'est la table "syndic_articles" qui va lier aux articles syndiques
	// Et c'est la table "syndic_liens" qui va lier aux objets declares dans SPIP
	$syndic_liens = array(
		"id_syndic" => "BIGINT NOT NULL DEFAULT 0",
		"id_objet" => "BIGINT NOT NULL DEFAULT 0",
		"objet" => "VARCHAR(25) NOT NULL", // peut etre un contact ou un compte
		'type' => "VARCHAR(25) NOT NULL DEFAULT ''" // euh..?
		);
	$syndic_liens_key = array(
		"PRIMARY KEY" => "id_syndic, id_objet, objet",
		"KEY id_syndic" => "id_syndic"
		);
	$tables_auxiliaires['spip_syndic_liens'] =
		array('field' => &$syndic_liens, 'key' => &$syndic_liens_key);


	return $tables_auxiliaires;
}

?>