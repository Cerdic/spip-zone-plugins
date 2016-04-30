<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_200_ok_message'             => 'La ressource ou la collection que vous avez demandée a bien été fournie par le service. Vous pouvez la consulter dans l\'index « donnees ».',
	'erreur_200_ok_titre'               => 'La requête a été traité avec succès',
	'erreur_400_collection_nok_message' => 'Vous avez demandé une collection qui n\'est pas supportée par ce service. SVP ne fournit que des collections de plugins (/svp/plugins) et de dépôts (/svp/depots).',
	'erreur_400_collection_nok_titre'   => 'La collection« « @valeur@ » n\'est pas fournie par ce service',
	'erreur_400_format_nok_message'     => 'Vous avez demandé de renvoyer les données dans un format qui n\'est pas supportée par ce service. SVP n\'utilise que les formats de sortie JSON et XML.',
	'erreur_400_format_nok_titre'       => 'Le format « @valeur@ » n\'est pas supporté par ce service',
	'erreur_400_critere_nok_message'    => 'Vous avez demandé de filtrer une collection avec un critère dont la valeur est invalide. Veuillez consulter la documentation pour spécifier un critère valide.',
	'erreur_400_critere_nok_titre'      => 'La valeur « @valeur@ » du critère « @element@ » est invalide',
	'erreur_400_prefixe_nok_message'    => 'Vous avez demandé un plugin mais le préfixe spécifié « @valeur@ » est invalide. Un préfixe ne peut contenir que des caractères alphanumériques et le souligné.',
	'erreur_400_prefixe_nok_titre'      => 'La valeur « @valeur@ » du préfixe de plugin est invalide',
	'erreur_400_ressource_nok_message'  => 'Vous avez demandé un type de ressource qui n\'est pas supporté par ce service. SVP ne fournit que des ressources de type plugin.',
	'erreur_400_ressource_nok_titre'    => 'Le type de ressource « @valeur@ » n\'est pas fourni par ce service',
	'erreur_404_plugin_nok_message'     => 'Vous avez demandé un plugin mais le préfixe spécifié ne correspond à aucun plugin enregistré dans la base de données du serveur. Soit vous avez fait une erreur sur le préfixe, soit le plugin spécifié n\'est pas fourni par un des dépôts enregistrés sur le serveur',
	'erreur_404_plugin_nok_titre'       => 'Le plugin de préfixe « @valeur@ » n\'est pas disponible sur le serveur',
	'erreur_501_serveur_nok_message'    => 'Vous avez essayé d\'utiliser l\'API SVP sur un serveur qui n\'est pas correctement configuré. En effet, ce serveur est actuellement en mode « SVP runtime » incompatible avec le service REST SVP.',
	'erreur_501_serveur_nok_titre'      => 'Le serveur n\'est pas correctement configuré',
);
