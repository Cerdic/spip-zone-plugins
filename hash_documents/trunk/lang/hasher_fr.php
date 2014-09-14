<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'action_hasher' => 'hasher @n@ documents' ,
	'action_dehasher' => 'déhasher @n@ documents' ,
	'action_corriger' => 'Corriger le chemin en base pour @n@ documents' ,
	'action_corriger_explication' => 'Il peut arriver que des documents aient été déplacés mais que leur chemin en base n\'ai pas été corrigé. Ils apparaissent alors comme « ne pouvant pas être hachés » : ' ,

	// B
	'bilan_titre' => 'Bilan' ,
	'bilan' => 'Ce site comporte @oui@ documents hashés, et @non@ qui ne le sont pas encore (ou ne peuvent pas l\'être).' ,
	
	// C
	'choix_action' => 'Choisir l\'action à effectuer :',
	
	// D
	'documents_modifies' => 'Documents modifiés : ' ,
	'documents_site' => 'Documents du site' ,

	// E
	'erreur_traitement' => 'Erreur dans le traitement de la requête',
	'erreur_action' => 'Action erronée',

	// H
	'htaccess_a_installer' => 'Veuillez installer dans @htaccess@ un fichier contenant les codes suivants :' ,
	'htaccess_installe' => 'Le fichier @htaccess@ semble correctement installé ; pour mémoire, il doit contenir les codes suivants :' ,
	
	// R
	'redirections' => 'Redirections' ,
	
	// T
	'titre' => 'Hash documents' ,
);

?>