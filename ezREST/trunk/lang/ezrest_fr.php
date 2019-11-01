<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'collection_ressource_oui'                         => 'lecture autorisée, identifiant « @ressource@ »',
	'collection_ressource_non'                         => 'lecture non autorisée',
	'collection_filtre_facultatif'                     => 'facultatif',
	'collection_filtre_obligatoire'                    => 'obligatoire',
	'collection_filtre_fournisseur'                    => 'ajouté par le plugin « @module@ »',

	// E
	'erreur_200_ok_message'                            => 'Les données collectées sont consultables à l\'index « donnees ».',
	'erreur_200_ok_titre'                              => 'La requête a été traitée avec succès',
	'erreur_400_collection_indisponible_message'       => 'L\'API permet l\'utilisation des collections suivantes : @extra@.',
	'erreur_400_collection_indisponible_titre'         => 'La collection « @valeur@ » n\'est pas fournie par l\'API',
	'erreur_400_collection_nok_titre'                  => 'Problème avec la collection « @valeur@ »',
	'erreur_400_critere_nom_nok_message'               => 'La collection  « @collection@ » supporte les critères suivants : @extra@.',
	'erreur_400_critere_nom_nok_titre'                 => 'Le critère « @valeur@ » n\'est pas supporté par la collection « @collection@ »',
	'erreur_400_critere_obligatoire_nok_message'       => 'Veuillez utiliser le critère « @valeur@ » avec une valeur autorisée pour obtenir une réponse valide.',
	'erreur_400_critere_obligatoire_nok_titre'         => 'Le critère « @valeur@ » est obligatoire',
	'erreur_400_ressource_indisponible_message'        => 'L\'API ne fournit des ressources que pour les collections suivantes : @extra@.',
	'erreur_400_ressource_indisponible_titre'          => 'La collection « @collection@ » n\'autorise pas l\'accès à une ressource',
	'erreur_400_ressource_nok_titre'                   => 'Problème avec la ressource « @element@ » de « @valeur@ » pour la collection « @collection@ »',
);
