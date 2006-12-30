<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'titre' => 'Tweak SPIP',
	'help'	=> "{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin 'Tweak&nbsp;SPIP'.</p>",
	'actif' => 'Tweak actif',
	'inactif' => 'Tweak inactif',
	'activer_tweak' => 'Activer le tweak',
	'tweak'	=> 'Tweak :',
	'tweaks_liste' => 'Liste des tweaks',
	'presente_tweaks' => "Cette page liste les tweaks disponibles.<br />Vous pouvez activer les tweaks n&eacute;cessaires en cochant la case correspondante.",
// erreurs
	'erreur:nom' => 'Erreur !',
	'erreur:description'	=> 'id manquant dans la d&eacute;finition du tweak !',

// categories
	'administration' => "1. Administration",
	'typographie' => "2. Typographie",
	'divers' => "3. Divers",
	
// Les tweaks
	'desactive_cache:nom' => 'D&eacute;sactiver le cache',
	'desactive_cache:description'	=> 'Inhibition du cache de SPIP pour le d&eacute;veloppement du site.',

	'supprimer_numero:nom' => 'Supprimer le num&eacute;ro',
	'supprimer_numero:description'	=> "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des titres du site, sans qu'elle soit pr&eacute;sente dans les squelettes.",

	'paragrapher:nom' => 'Paragrapher',
	'paragrapher:description'	=> "Applique la fonction SPIP paragrapher() aux textes qui sont d&eacute;pourvus de paragraphes en insérant des balises &lt;p&gt;.",

	'verstexte:nom' => 'Version texte',
	'verstexte:description'	=> "2 filtres pour vos squelettes. 
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein.",

/*
	':nom' => '',
	':description'	=> '',
*/
);


?>