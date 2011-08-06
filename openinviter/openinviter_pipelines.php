<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function openinviter_importateur_contacts_moteurs($moteurs){
	include_spip('OpenInviter/openinviter');
	include_spip('inc/config');
	
	$infos = array(
		'titre' => 'OpenInviter',
		'url' => 'http://openinviter.com/',
		'fournisseurs' => array(),
	);
	
	$inviter = new OpenInviter();
	$inviter->settings['cookie_path'] = _DIR_TMP;
	$inviter->settings['transport'] = lire_config('openinviter/transport', 'curl');
	
	$plugins = $inviter->getPlugins();
	
	// On force la catégorie de certains
	$force = array(
		'linkedin' => 'social',
		'youtube' => 'social',
	);
	
	foreach ($plugins as $listes_plugins) {
		foreach ($listes_plugins as $nom_plugin => $plugin) {
			$nom_fournisseur = strtolower(preg_replace('[\W]', '', $plugin['name']));

			// Type du fournisseur
			$type = $plugin['type'] == 'email' ? 'webmail' : 'social';
			if (isset($force[$nom_fournisseur])) {
				$type = $force[$nom_fournisseur];
			}

			// Add Provider.
			$infos['fournisseurs'][$nom_fournisseur] = array(
				'titre' => $plugin['name'],
				'type' => $type,
				'nom_plugin' => $nom_plugin,
			);
			$fournisseur = & $infos['fournisseurs'][$nom_fournisseur];

			// Add domains.
			// OpenInviter does this strange thing where it uses both 'allowed_domains' and
			// 'detected_domains'.  Not sure what the difference is :P
			foreach (array('allowed_domains', 'detected_domains') as $key) {
				if (isset($plugin[$key]) && is_array($plugin[$key])) {
					foreach ($plugin[$key] as $regex) {
						$titre_domaine = str_replace(array(')/i', '/('), '', $regex);
						$cle_domaine = strtolower(str_replace(' ', '_', $titre_domaine));
						$fournisseur['domaines'][$cle_domaine] = array(
							'regex' => $regex,
							'titre' => $titre_domaine,
						);
					}
				}
			}

			// If there's no domains, then this provider can work with all domains.
			if (empty($fournisseur['domaines'])) {
				$fournisseur['domaines'] = array(
					array(
						'regex' => '/(.*)/',
						'titre' => '*',
					)
				);
			}
		}
	}
	
	$moteurs['openinviter'] = $infos;
	
	return $moteurs;
}

?>
