<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

// B
	'bouton_generer' => 'G&eacute;n&eacute;rer',
	'bouton_verifier' => 'V&eacute;rifier',

// I
	'info_chemin_fichier' => 'Racine de l\'arborescence des fichiers (exemple : <em>plugins/rainette/</em>, ou <em>ecrire/</em>)',
	'info_chemin_langue' => 'Dossier dans lequel est install&eacute; le fichier de langue (exemple : <em>plugins/rainette/lang/</em>, ou <em>ecrire/lang/</em>)',
	'info_generer' => 'Cette option vous permet de g&eacute;n&eacute;rer, &agrave; partir d\'une langue source, le fichier de langue d\'un module donn&eacute; dans une langue cible. Si le fichier cible existe d&eacute;j&agrave; son contenu est r&eacute;utilis&eacute; pour construire le nouveau fichier.',
	'info_langue' => 'Abr&eacute;viation de la langue (exemple : <em>fr</em>, <em>en</em>, <em>es</em>...)',
	'info_mode' => 'Correspond &agrave; la chaine qui sera ins&eacute;r&eacute;e lors de la cr&eacute;ation d\'un nouvel item pour la langue cible.',
	'info_module' => 'Correspond au pr&eacute;fixe du fichier de langue hors abr&eacute;viation de la langue (exemple : <em>rainette</em> pour le plugin de m&ecirc;me nom, ou <em>ecrire</em> pour SPIP)',
	'info_verifier' => 'Cette option vous permet de v&eacute;rifier les fichiers de langue d\'un module donn&eacute; sous deux angles compl&eacute;mentaires. Il est possible, soit de v&eacute;rifier si des items de langue utilis&eacute;s dans un groupe de fichiers (un plugin, par exemple) ne sont pas d&eacute;finis dans le fichier de langue idoine, soit que certains items de langue d&eacute;finis ne sont plus utilis&eacute;s.',

// L
	'label_chemin_fichier' => 'Arborescence &agrave; v&eacute;rifier',
	'label_chemin_langue' => 'Localisation du fichier de langue',
	'label_langue_cible' => 'Langue cible',
	'label_langue_source' => 'Langue source',
	'label_langue' => 'Langue',
	'label_mode' => 'Mode de cr&eacute;ation des nouveaux items',
	'label_mode_utilisation' => 'Mode d\'utilisation',
	'label_mode_normal' => 'normal',
	'label_mode_complet' => 'complet',
	'label_module' => 'Module',
	'label_verification_definition' => 'D&eacute;tection des d&eacute;finitions manquantes',
	'label_verification_utilisation' => 'D&eacute;tection des d&eacute;finitions obsol&egrave;tes',
	'label_verification' => 'Type de v&eacute;rification',
	'legende_generer' => 'G&eacute;n&eacute;ration des fichiers de langue',
	'legende_verifier' => 'V&eacute;rification des items de langue',

// M
	'message_choisir_langue' => 'Choisissez le fichier de langue &agrave; v&eacute;rifier.',
	'message_choisir_dossier' => 'Choisissez le r&eacute;pertoire dont l\'arboresence sera scann&eacute;e',
	'message_nok_champ_obligatoire' => 'Ce champ est obligatoire',
	'message_nok_ecriture_fichier' => 'Le fichier de langue &laquo;<em>@langue@</em>&raquo; du module &laquo;<em>@module@</em>&raquo; n\'a pas &eacute;t&eacute; cr&eacute;&eacute; car une erreur s\'est produite lors de son &eacute;criture !',
	'message_nok_fichier_langue' => 'Le fichier de langue &laquo;<em>@langue@</em>&raquo; du module &laquo;<em>@module@</em>&raquo; est introuvable dans le r&eacute;pertoire &laquo;<em>@dossier@</em>&raquo; !',
	'message_ok_fichier_genere' => 'Le fichier de langue &laquo;<em>@langue@</em>&raquo; du module &laquo;<em>@module@</em>&raquo; a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; correctement (voir fichier &laquo;<em>@fichier@</em>&raquo;).',
	'message_ok_definis_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont utilis&eacute;s dans un contexte complexe et pourraient &ecirc;tre non d&eacute;finis dans le fichier de langue  &laquo;<em>@langue@</em>&raquo;. Nous vous invitons &agrave; les v&eacute;rifier un par un :',
	'message_ok_definis_incertains_1' => 'L\'item de langue ci-dessous est utilis&eacute; dans un contexte complexe et pourrait &ecirc;tre non d&eacute;fini dans le fichier de langue  &laquo;<em>@langue@</em>&raquo;. Nous vous invitons &agrave; le v&eacute;rifier :',
	'message_ok_definis_incertains_0' => 'Aucun item de langue n\'est utilis&eacute; dans un contexte complexe (par exemple :  _T(\'@module@_\'.$statut)).',
	'message_ok_non_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilis&eacute;s dans des fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo; mais ne sont pas d&eacute;finis dans le fichier de langue &laquo;<em>@langue@</em>&raquo; :',
	'message_ok_non_definis_1' => 'L\'item de langue ci-dessous est utilis&eacute; dans des fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo; mais n\'est pas d&eacute;fini dans le fichier de langue &laquo;<em>@langue@</em>&raquo; :',
	'message_ok_non_definis_0' => 'Tous les items de langue du module    &laquo;<em>@module@</em>&raquo; utilis&eacute;s dans les fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo; sont bien d&eacute;finis dans le fichier de langue &laquo;<em>@langue@</em>&raquo;.',
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien d&eacute;finis dans le fichier de langue &laquo;<em>@langue@</em>&raquo;, mais ne sont pas utilis&eacute;s dans les fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo; :',
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien d&eacute;fini dans le fichier de langue &laquo;<em>@langue@</em>&raquo;, mais n\'est pas utilis&eacute; dans les fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo; :',
	'message_ok_non_utilises_0' => 'Tous les items de langue d&eacute;finis  dans le fichier de langue &laquo;<em>@langue@</em>&raquo; sont bien utilis&eacute;s dans les fichiers du r&eacute;pertoire &laquo;<em>@ou_fichier@</em>&raquo;.',
	'message_ok_utilises_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont peut-&ecirc;tre utilis&eacute;s dans un contexte complexe. Nous vous invitons &agrave; les v&eacute;rifier un par un :',
	'message_ok_utilises_incertains_1' => 'L\'item de langue ci-dessous est peut-&ecirc;tre utilis&eacute; dans un contexte complexe. Nous vous invitons &agrave; le v&eacute;rifier :',
	'message_ok_utilises_incertains_0' => 'Aucun item de langue n\'est utilis&eacute; dans un contexte complexe (par exemple :  _T(\'@module@_\'.$statut)).',
	'message_nok_plugin_inactif' => 'Le plugin &laquo;<em>@plugin@</em>&raquo; n\'est pas activ&eacute;. Activez-le avant de continuer les v&eacute;rifications.',
	'meteo_test' => 'TEST : Cet item de langue est bien d&eacute;fini dans le fichier de langue, mais est utilis&eacute; sous forme "complexe" dans les fichiers du r&eacute;pertoire scann&eacute;.',

// O
	'option_mode_index' => 'Item de la langue source',
	'option_mode_new_index' => 'Item de la langue source pr&eacute;c&eacute;d&eacute; de &lt;NEW&gt;',
	'option_mode_new_valeur' => 'Chaine dans la langue source pr&eacute;c&eacute;d&eacute;e de &lt;NEW&gt;',
	'option_mode_pas_item' => 'Ne pas cr&eacute;er d\'item',
	'option_mode_new' => 'Balise &lt;NEW&gt; uniquement',
	'option_mode_valeur' => 'Chaine dans la langue source',
	'option_mode_vide' => 'Une chaine vide',


// T
	'test_item_non_utilise' => 'TEST : Cet item de langue est bien d&eacute;fini dans le fichier de langue (), mais n\'est pas utilis&eacute; dans les fichiers du r&eacute;pertoire scann&eacute; ().',
	'titre_onglet' => 'LangOnet',
	'titre_page_navigateur' => 'LangOnet'

);


?>