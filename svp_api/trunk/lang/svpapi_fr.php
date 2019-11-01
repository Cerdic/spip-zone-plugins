<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_400_critere_compatible_spip_nok_message' => 'Veuillez spécifier une valeur autorisée pour le critère « @element@ » : version comme « 3.2.0 » ou branche comme « 2.1 » ou liste de branches comme « 2.1,3.0,3.1 ».',
	'erreur_400_critere_compatible_spip_nok_titre'   => 'La valeur « @valeur@ » du critère « @element@ » est invalide',
	'erreur_400_prefixe_malforme_titre'              => 'Le préfixe « @valeur@ » est mal formé',
	'erreur_400_prefixe_malforme_message'            => 'Le préfixe d\'un plugin est un mot d\'au moins 2 caractères',
	'erreur_400_prefixe_nok_titre'                   => 'Le préfixe « @valeur@ » est invalide',
	'erreur_400_prefixe_nok_message'                 => 'Le préfixe doit correspondre à un celui d\'un plugin référencé dans un des dépôts chargés',
	'erreur_501_runtime_nok_message'                 => 'Le serveur est actuellement en mode « SVP runtime » incompatible avec le service REST SVP.',
	'erreur_501_runtime_nok_titre'                   => 'Le serveur SVP n\'est pas correctement configuré',
);
