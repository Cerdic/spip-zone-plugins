<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_200_ok_message'                    => 'Vous pouvez consulter la ressource ou la collection demandée dans l\'index « donnees ».',
	'erreur_200_ok_titre'                      => 'La requête a été traitée avec succès',
	'erreur_400_collection_nok_message'        => 'SVP fournit les collections suivantes : @extra@.',
	'erreur_400_collection_nok_titre'          => 'La collection « @valeur@ » n\'est pas fournie par ce service',
	'erreur_400_critere_nom_nok_message'       => 'La collection  « @collection@ » supporte les critères suivants : @extra@.',
	'erreur_400_critere_nom_nok_titre'         => 'Le critère « @valeur@ » n\'est pas supporté par la collection « @collection@ »',
	'erreur_400_critere_valeur_nok_message'    => 'Veuillez consulter la documentation pour spécifier une valeur valide pour le critère @element@ (@extra@).',
	'erreur_400_critere_valeur_nok_titre'      => 'La valeur « @valeur@ » du critère « @element@ » est invalide',
	'erreur_400_ressource_nok_message'         => 'SVP ne fournit des ressources que pour les collections suivantes : @extra@.',
	'erreur_400_ressource_nok_titre'           => 'La collection « @collection@ » n\'autorise pas l\'accès à une ressource',
	'erreur_400_ressource_prefixe_nok_message' => 'Veuillez vérifier l\'orthographe du préfixe (caractères alphanumériques et souligné) ou si celui-ci est bien fourni par un des dépôts enregistrés sur le serveur.',
	'erreur_400_ressource_prefixe_nok_titre'   => 'Le préfixe « @valeur@ » n\'est pas disponible pour la collection « @collection@ »',
	'erreur_501_serveur_nok_message'           => 'Le serveur est actuellement en mode « SVP runtime » incompatible avec le service REST SVP.',
	'erreur_501_serveur_nok_titre'             => 'Le serveur n\'est pas correctement configuré',
	'extra_critere_compatible_spip'            => 'version SPIP comme « 3.2.0 » ou branche comme « 2.1 » ou liste de branches comme « 2.1,3.0,3.1 »'
);
