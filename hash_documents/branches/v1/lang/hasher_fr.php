<?php

// S&eacute;curit&eacute;
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'action_hasher' => 'hasher @n@ documents' ,
	'action_dehasher' => 'd&eacute;hasher @n@ documents' ,
	'action_corriger' => 'Corriger le chemin en base pour @n@ documents' ,
	'action_corriger_explication' => 'Il peut arriver que des documents aient &eacute;t&eacute; d&eacute;plac&eacute;s mais que leur chemin en base n\'ai pas &eacute;t&eacute; corrig&eacute;. Ils apparaissent alors comme &laquo;&nbsp;ne pouvant pas &ecirc;tre hach&eacute;s&nbsp;&raquo;&nbsp;:&nbsp;' ,
	'documents_modifies' => 'Documents modifi&eacute;s : ' ,
	'documents_site' => 'Documents du site' ,
	'bilan' => 'Ce site comporte @oui@ documents hash&eacute;s, et @non@ qui ne le sont pas encore (ou ne peuvent pas l\'&ecirc;tre).' ,
	'htaccess_a_installer' => 'Veuillez installer dans @htaccess@ un fichier contenant les codes suivants :' ,
	'htaccess_installe' => 'Le fichier @htaccess@ semble correctement install&eacute; ; pour m&eacute;moire, il doit contenir les codes suivants :' ,
	'redirections' => 'Redirections' ,
	'titre' => 'Hash documents' ,
);

?>