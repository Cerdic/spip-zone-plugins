<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function lister_caches() {
	$caches = array();

	$dir_caches = _DIR_VAR . 'cache-boussoles';
	if ($fichiers_cache = glob($dir_caches . "/boussole*.xml")) {
		// (on sait déjà que le mode serveur est actif)
		$boussoles = $GLOBALS['serveur_boussoles_disponibles'];
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		foreach ($fichiers_cache as $_fichier) {
			$cache = array();
			$cache['fichier'] = $_fichier;
			$cache['nom'] = basename($_fichier);
			$cache['maj'] = date('Y-m-d H:i:s', filemtime($_fichier));

			$cache['sha'] = '';
			$cache['plugin'] = '';

			lire_fichier($_fichier, $contenu);
			$convertir = charger_fonction('simplexml_to_array', 'inc');
			$tableau = $convertir(simplexml_load_string($contenu), false);
			if ($cache['nom'] == 'boussoles.xml') {
				// C'est le cache qui liste les boussoles hébergées
				$cache['description'] = _T('boussole:info_cache_boussoles');
				if  (isset($tableau['name'])
				AND ($tableau['name'] == 'boussoles')) {
					$cache['sha'] = $tableau['attributes']['sha'];
				}
			}
			else {
				// C'est le cache d'une boussole hébergée
				$alias_boussole = str_replace('boussole-', '', basename($_fichier, '.xml'));
				$cache['description'] = _T('boussole:info_cache_boussole', array('boussole' => $alias_boussole));
				if  (isset($tableau['name'])
				AND ($tableau['name'] == 'boussole')) {
					$cache['sha'] = $tableau['attributes']['sha'];
					$cache['nom'] .= " ({$tableau['attributes']['version']})";
				}
				if (isset($boussoles[$alias_boussole]['prefixe'])) {
					$informer = charger_fonction('informer_plugin', 'inc');
					$infos = $informer($boussoles[$alias_boussole]['prefixe']);
					if ($infos)
						$cache['plugin'] = "{$infos['nom']} ({$boussoles[$alias_boussole]['prefixe']}/{$infos['version']})";
				}
				else
					$cache['plugin'] = _T('boussole:info_boussole_manuelle');
			}
			$caches[] = $cache;
		}
	}

	return $caches;
}

?>
