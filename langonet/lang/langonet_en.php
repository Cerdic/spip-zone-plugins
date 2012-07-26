<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/langonet?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_corriger' => 'Retrieve the corrections',
	'bouton_generer' => 'Generate',
	'bouton_langonet' => 'LangOnet',
	'bouton_lister' => 'Display',
	'bouton_rechercher' => 'Search',
	'bouton_verifier' => 'Verify',
	'bulle_afficher_fichier_lang' => 'Display the language file generated on @date@',
	'bulle_afficher_fichier_log' => 'Display the log of @date@',
	'bulle_corriger' => 'Download the corrected language file',
	'bulle_telecharger_fichier_lang' => 'Download the language file generated on @date@',
	'bulle_telecharger_fichier_log' => 'Download the log file of @date@',

	// E
	'entete_log_avertissement_nonmais' => 'AVERTISSEMENT : items n\'appartenant pas au module', # NEW
	'entete_log_avertissement_peutetre_definition' => 'WARNING: items maybe undefined',
	'entete_log_avertissement_peutetre_utilisation' => 'WARNING: items maybe not used',
	'entete_log_date_creation' => 'File generated on @log_date_jour@ at @log_date_heure@.',
	'entete_log_erreur_definition' => 'ERROR : undefined module items',
	'entete_log_erreur_definition_nonmais' => 'ERROR : undefined items of other modules',
	'entete_log_erreur_fonction_l' => 'ERROR : usage cases of the _L() function',
	'entete_log_erreur_utilisation' => 'ERROR : unused items',

	// I
	'info_arborescence_scannee' => 'Choisissez le répertoire de base dont l\'arborescence sera scannée', # NEW
	'info_bloc_langues_generees' => 'Cliquez sur un lien ci-dessous pour télécharger l\'un des fichiers de langue générés.', # NEW
	'info_bloc_logs_definition' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des définitions manquantes d\'un fichier de langue.', # NEW
	'info_bloc_logs_fonction_l' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des utilisations de _L() dans une arborescence donnée.', # NEW
	'info_bloc_logs_utilisation' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des définitions obsolètes d\'un fichier de langue.', # NEW
	'info_chemin_langue' => 'Dossier dans lequel est installé le fichier de langue (exemple : <em>plugins/rainette/lang/</em>, ou <em>ecrire/lang/</em>)', # NEW
	'info_fichier_liste' => 'Choisissez le fichier de langue dont vous voulez afficher les items, parmi ceux présents dans le site.', # NEW
	'info_fichier_verifie' => 'Choisissez le fichier de langue à vérifier parmi ceux présents dans le site.', # NEW
	'info_generer' => 'Cette option vous permet de générer, à partir d\'une langue source, le fichier de langue d\'un module donné dans une langue cible. Si le fichier cible existe déjà son contenu est réutilisé pour construire le nouveau fichier.', # NEW
	'info_langue' => 'Abréviation de la langue (exemple : <em>fr</em>, <em>en</em>, <em>es</em>...)', # NEW
	'info_lister' => 'Cette option vous permet de visualiser les items d\'un fichier de langue classés par ordre alphabétique.', # NEW
	'info_mode' => 'Correspond à la chaîne qui sera insérée lors de la création d\'un nouvel item pour la langue cible.', # NEW
	'info_module' => 'Correspond au préfixe du fichier de langue hors abréviation de la langue (exemple : <em>rainette</em> pour le plugin de même nom, ou <em>ecrire</em> pour SPIP)', # NEW
	'info_pattern_item_cherche' => 'Saisissez une chaîne correspondant à tout ou partie d\'un raccourci d\'item de langue. La recherche est toujours insensible à la casse.', # NEW
	'info_pattern_texte_cherche' => 'Saisissez une chaîne correspondant à tout ou partie d\'une traduction française d\'item de langue. La recherche est toujours insensible à la casse.', # NEW
	'info_rechercher_item' => 'Cette option vous permet de chercher des items de langue dans tous les fichiers de langue présents sur le site. Par souci de performance, seuls les fichiers de langue française sont scannés.', # NEW
	'info_rechercher_texte' => 'Cette option vous permet de chercher des items de langue via leur traduction française dans les fichiers de langue de SPIP <em>ecrire_fr</em>, <em>public_fr</em> et <em>spip_fr</em>. Le but de cette recherche est de vérifier si un texte n\'existe pas déjà dans SPIP avant de le créer.', # NEW
	'info_table' => 'Vous pouvez consulter ci-dessous la liste alphabétique des items de langue du fichier «<em>@langue@</em>» (@total@). Chaque bloc affiche les items ayant la même initiale, le raccourci en gras et le texte affiché en regard. Survolez une initiale pour faire apparaître la liste correspondante.', # NEW
	'info_verifier' => 'Cette option vous permet, d\'une part,  de vérifier les fichiers de langue d\'un module donné sous deux angles complémentaires. Il est possible, soit de vérifier si des items de langue utilisés dans un groupe de fichiers (un plugin, par exemple) ne sont pas définis dans le fichier de langue idoine, soit que certains items de langue définis ne sont plus utilisés. <br />D\'autre part, il est possible de lister et de corriger toutes les utilisations de la fonction _L() dans les fichiers PHP d\'une arborescence donnée.', # NEW

	// L
	'label_arborescence_scannee' => 'Directory tree to be scanned',
	'label_avertissement' => 'Warnings',
	'label_chemin_langue' => 'Location of the language file',
	'label_correspondance' => 'Type de correspondance', # NEW
	'label_correspondance_commence' => 'Begins by',
	'label_correspondance_contient' => 'Contient', # NEW
	'label_correspondance_egal' => 'Equal to',
	'label_erreur' => 'Errors',
	'label_fichier_liste' => 'Language file',
	'label_fichier_verifie' => 'Language to verify',
	'label_langue_cible' => 'Target language',
	'label_langue_source' => 'Source language',
	'label_mode' => 'Mode de création des nouveaux items', # NEW
	'label_module' => 'Module',
	'label_pattern' => 'String to search',
	'label_verification' => 'Type de vérification', # NEW
	'label_verification_definition' => 'Détection des définitions manquantes', # NEW
	'label_verification_fonction_l' => 'Détection des cas d\'utilisation de la fonction _L()', # NEW
	'label_verification_utilisation' => 'Détection des définitions obsolètes', # NEW
	'legende_resultats' => 'Verification results',
	'legende_table' => 'Liste des items du fichier de langue choisi', # NEW
	'legende_trouves' => 'List of found items (@total@)',

	// M
	'message_nok_aucun_fichier_log' => 'Aucun fichier de log disponible au téléchargement', # NEW
	'message_nok_aucune_langue_generee' => 'Aucun fichier de langue généré disponible au téléchargement', # NEW
	'message_nok_champ_obligatoire' => 'This field is required',
	'message_nok_ecriture_fichier' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» n\'a pas été créé car une erreur s\'est produite lors de son écriture !', # NEW
	'message_nok_fichier_langue' => 'La génération a échoué car le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» est introuvable dans le répertoire «<em>@dossier@</em>» !', # NEW
	'message_nok_fichier_log' => 'Le fichier de log contenant les résultats de la vérification n\'a pas pu être créé!', # NEW
	'message_nok_fichier_script' => 'Le fichier de script contenant les commandes de remplacement des fonctions _L par _T n\'a pas pu être créé!', # NEW
	'message_nok_item_trouve' => 'Aucun item de langue ne correspond à la recherche !', # NEW
	'message_ok_definis_incertains_0' => 'Aucun item de langue n\'est utilisé dans un contexte complexe, comme par exemple, _T(\'@module@:item_\'.$variable).', # NEW
	'message_ok_definis_incertains_1' => 'L\'item de langue ci-dessous est utilisé dans un contexte complexe et pourrait être non défini dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_definis_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans un contexte complexe et pourraient être non définis dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_fichier_genere' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» a été généré correctement.<br />Vous pouvez récupérer le fichier «<em>@fichier@</em>».', # NEW
	'message_ok_fichier_log' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats.', # NEW
	'message_ok_fichier_log_script' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats ainsi que le fichier des commandes de remplacement _L en _T, «<em>@script@</em>».', # NEW
	'message_ok_fonction_l_0' => 'Aucun cas d\'utilisation de la fonction _L() n\'a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_fonction_l_1' => 'Un seul cas d\'utilisation de la fonction _L() a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_fonction_l_n' => '@nberr@ cas d\'utilisation de la fonction _L() ont été détectés dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_item_trouve' => 'The search for the string @pattern@ is successful.',
	'message_ok_item_trouve_commence_1' => 'The language item below begins by the search string:',
	'message_ok_item_trouve_commence_n' => 'The @sous_total@ language items below all begin by the search string:',
	'message_ok_item_trouve_contient_1' => 'The language item below contains the searched string:',
	'message_ok_item_trouve_contient_n' => 'The @sous_total@ items below contain all the searched string:',
	'message_ok_item_trouve_egal_1' => 'The item below correspond exactly to the search string:',
	'message_ok_item_trouve_egal_n' => 'The @sous_total@ items below correspond exactly to the search string:',
	'message_ok_non_definis_0' => 'Tous les items de langue du module «<em>@module@</em>» utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» sont bien définis dans le fichier de langue «<em>@langue@</em>».', # NEW
	'message_ok_non_definis_1' => 'L\'item de langue du module «<em>@module@</em>» affiché ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais n\'est pas défini dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_definis_n' => 'Les @nberr@ items de langue du module «<em>@module@</em>» affichés ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais ne sont pas définis dans le fichier de langue «<em>@langue@</em>» :', # NEW
	'message_ok_non_utilises_0' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_0s' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_1s' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_ns' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_nonmais_definis_0' => 'Les fichiers du répertoire «<em>@ou_fichier@</em>» n\'utilisent aucun item de langue correctement défini dans un autre module que «<em>@module@</em>».', # NEW
	'message_ok_nonmais_definis_1' => 'L\'item de langue ci-dessous est utilisé correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais est défini dans un autre module que «<em>@module@</em>». Nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmais_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais sont définis dans un autre module que «<em>@module@</em>». Nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_nonmaisnok_definis_0' => 'Les fichiers du répertoire «<em>@ou_fichier@</em>» n\'utilisent aucun item de langue incorrectement défini dans un autre module que «<em>@module@</em>».', # NEW
	'message_ok_nonmaisnok_definis_1' => 'L\'item de langue ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme un item du module «<em>@module@</em>». Etant donné qu\'il n\'est pas défini dans son module de rattachement, nous vous invitons à le vérifier :', # NEW
	'message_ok_nonmaisnok_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme des items du module «<em>@module@</em>». Etant donné qu\'ils ne sont pas définis dans leur module de rattachement, nous vous invitons à les vérifier un par un :', # NEW
	'message_ok_table_creee' => 'The table of items od the language file @langue@ has been correctly created',
	'message_ok_utilises_incertains_0' => 'No language item is used in a complex context (for example:  _T(\'@module@:item_\'.$variable)).',
	'message_ok_utilises_incertains_1' => 'The item language below may be used in a complex context. We invite you to check it:',
	'message_ok_utilises_incertains_n' => 'The @nberr@ language items belowmay be used in a complex context. We invite you to check them one by one:',

	// O
	'onglet_generer' => 'Generate a language',
	'onglet_lister' => 'Display a language',
	'onglet_rechercher' => 'Search for an item',
	'onglet_verifier' => 'Check a language',
	'option_aucun_dossier' => 'no directory tree selected',
	'option_aucun_fichier' => 'no language selected',
	'option_mode_index' => 'Item of the source language',
	'option_mode_new' => ' &lt;NEW&gt; tag only',
	'option_mode_new_index' => 'Item de la langue source précédé de &lt;NEW&gt;', # NEW
	'option_mode_new_valeur' => 'Chaîne dans la langue source précédée de &lt;NEW&gt;', # NEW
	'option_mode_pas_item' => 'Do not create the item',
	'option_mode_valeur' => 'String in the source language',
	'option_mode_vide' => 'An empty string',

	// T
	'test' => 'TEST : Cet item de langue sert pour la recherche de raccourci et est égal à test.', # NEW
	'test_item_1_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_2_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_non_utilise_1' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().', # NEW
	'test_item_non_utilise_2' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().', # NEW
	'texte_item_defini_ou' => '<em>defined in:</em>',
	'texte_item_mal_defini' => '<em>but is not defined in the good module:</em>',
	'texte_item_non_defini' => '<em>but never defined!</em>',
	'texte_item_utilise_ou' => '<em>used in:</em>',
	'titre_bloc_langues_generees' => 'Language files',
	'titre_bloc_logs_definition' => 'Missing definitions',
	'titre_bloc_logs_fonction_l' => 'Uses of _L()',
	'titre_bloc_logs_utilisation' => 'Obsolete definitions',
	'titre_form_generer' => 'Creation of language files',
	'titre_form_lister' => 'Display of language files',
	'titre_form_rechercher_item' => 'Search of shortcuts in the language files',
	'titre_form_rechercher_texte' => 'Search of texts in the SPIP language files',
	'titre_form_verifier' => 'Verification of the language files',
	'titre_page' => 'LangOnet',
	'titre_page_navigateur' => 'LangOnet',

	// Z
	'z_test' => 'TEST: this language item is used to search shortcuts and contains test.'
);

?>
