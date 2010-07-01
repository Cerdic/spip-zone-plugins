<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL
 * Auteur Cyril MARION - Ateliers CYM
 * 
 */

function peupler_base_comptes() {
	// Attention, specifier le NOM des TABLES et non le nom des BOUCLES !
	sql_insertq_multi('spip_contacts', array(
		array(
			'id_auteur' => 1,
			'civilite' => 'M.',
			'nom' => 'MARION',
			'prenom' => 'Cyril',
			'date_naissance' => '1963-04-06'
			),
		array(
			'id_auteur' => 3,
			'civilite' => 'M.',
			'nom' => 'XAVIER',
			'prenom' => 'Fred',
			'date_naissance' => ''
			),
		array(
			'id_auteur' => 4,
			'civilite' => 'M.',
			'nom' => 'LARTIST',
			'prenom' => 'David',
			'date_naissance' => ''
			)
		)
	);

    sql_insertq_multi('spip_comptes', array(
		array(
			'id_auteur' => 14,
			'nom' => 'Ateliers CYM',
			'type' => 'SARL',
			'date_creation' => '2002-01-01'
			)
		)
	);
  
    sql_insertq_multi('spip_comptes_contacts', array(
		array(
			'id_compte' => 14,
			'id_contact' => 1,
			),
        array(
            'id_compte' => 14,
            'id_contact' => 3,
            )
        )
	);

    sql_insertq_multi('spip_champs', array(
		array(
			'id_champ' => 1,
			'type_champ' => 'niveau',
			'descriptif' => 'intermediaire'
			),
		array(
			'id_champ' => 2,
			'type_champ' => 'niveau',
			'descriptif' => 'debutant'
			),
		array(
			'id_champ' => 3,
			'type_champ' => 'niveau',
			'descriptif' => 'professeur'
			),
		)
	);
	
	sql_insertq_multi('spip_champs_liens', array(
		array(
			'id_champ' => 1,
			'id_objet' => 1,
			'objet' => 'auteur'
			),
		array(
			'id_champ' => 2,
			'id_objet' => 3,
			'objet' => 'auteur'
			),
		array(
			'id_champ' => 3,
			'id_objet' => 4,
			'objet' => 'auteur'
			)
		)
	);

	sql_insertq_multi('spip_adresses', array(
		array(
			'id_adresse' => 1,
			'type_adresse' => 'perso',
			'code_postal' => '75012',
			'ville'	=> 'PARIS'
			),
		array(
			'id_adresse' => 2,
			'type_adresse' => 'perso',
			'code_postal' => '92100',
			'ville'	=> 'BOULOGNE'
			),
		array(
			'id_adresse' => 3,
			'type_adresse' => 'perso',
			'code_postal' => '93000',
			'ville'	=> 'SAINT-DENIS'
			),
		)
	);
	
	sql_insertq_multi('spip_adresses_liens', array(
		array(
			'id_adresse' => 1,
			'id_objet' => 1,
			'objet' => 'auteur'
			),
		array(
			'id_adresse' => 2,
			'id_objet' => 2,
			'objet' => 'auteur'
			),
		array(
			'id_adresse' => 3,
			'id_objet' => 3,
			'objet' => 'auteur'
			)
		)
	);
}

?>