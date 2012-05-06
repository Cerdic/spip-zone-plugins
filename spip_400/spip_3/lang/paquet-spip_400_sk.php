<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/paquet-spip_400?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spip_400_description' => 'Ce plugin tente de compléter la distribution de SPIP en proposant des modèles de pages d\'erreur HTTP ({codes 401 et 404}) avec un texte explicatif et la possibilité pour l\'internaute de transmettre un "ticket de bug" au webmestre du site.

Il propose notamment : 
- un message sur les pages publiques pour que l\'internaute ne se perde pas, 
- l\'envoi d\'un mail au webmestre avec une info complète sur l\'erreur en question ({utilisateur SPIP, URL, REFERER, backtrace PHP, etc}), 
- l\'écriture de messages de LOG dans un fichier spécifique ...

Une page de configuration est proposée en option si vous utilisez le plugin [CFG : moteur de configuration->http://www.spip-contrib.net/?rubrique575].', # NEW
	'spip_400_slogan' => 'Ovládanie vynútených chybových stránok (401, 404) pre SPIP'
);

?>
