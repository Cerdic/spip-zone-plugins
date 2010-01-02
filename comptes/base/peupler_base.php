<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function peupler_base() {
	// Attention, spécifier le NOM des TABLES et non le nom des BOUCLES !
	sql_insertq_multi('spip_contacts', array(
		array(
			'id_auteur' => 1,
			'civilite' => 'M.',
			'prenom' => 'Cyril',
			'nom' => 'MARION',
			'naissance' => '1963-04-06'
			),
		array(
			'id_auteur' => 3,
			'civilite' => 'M.',
			'prenom' => 'Fred',
			'nom' => 'XAVIER',
			'naissance' => ''
			),
		array(
			'id_auteur' => 4,
			'civilite' => 'M.',
			'prenom' => 'David',
			'nom' => 'LARTIST',
			'naissance' => ''
			)
		)
	);
  
	sql_insertq_multi('spip_coordonnees', array(
		array(
			'id_coordonnee' => 1,
			'type' => 'niveau',
			'titre' => 'niveau',
			'descriptif' => 'intermediaire'
			),
		array(
			'id_coordonnee' => 2,
			'type' => 'niveau',
			'titre' => 'niveau',
			'descriptif' => 'debutant'
			),
		array(
			'id_coordonnee' => 3,
			'type' => 'niveau',
			'titre' => 'niveau',
			'descriptif' => 'professeur'
			),
		)
	);
	
	sql_insertq_multi('spip_coordonnees_liens', array(
		array(
			'id_objet' => 1,
			'objet' => 'contact'
			),
		array(
			'id_objet' => 2,
			'objet' => 'contact'
			),
		array(
			'id_objet' => 3,
			'objet' => 'contact'
			)
		)
	);
	

}

?>