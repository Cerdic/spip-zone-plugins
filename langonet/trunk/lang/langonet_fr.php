<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/langonet/branches/v0/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_cocher_tout' => 'Tout cocher',
	'bouton_cocher_aucun' => 'Tout décocher',
	'bouton_cocher_spip' => 'Cocher les modules SPIP',
	'bouton_corriger' => 'Obtenir les corrections',
	'bouton_generer' => 'Générer',
	'bouton_langonet' => 'LangOnet',
	'bouton_lister' => 'Afficher',
	'bouton_rechercher' => 'Rechercher',
	'bouton_verifier' => 'Vérifier',
	'bulle_afficher_fichier_lang' => 'Afficher le fichier de langue généré le @date@',
	'bulle_afficher_fichier_log' => 'Afficher le log du @date@',
	'bulle_corriger' => 'Télécharger le fichier de langue corrigé',
	'bulle_telecharger_fichier_lang' => 'Télécharger le fichier de langue généré le @date@',
	'bulle_telecharger_fichier_log' => 'Télécharger le log du @date@',

	// E
	'entete_log_avertissement_nonmais' => 'AVERTISSEMENT : items n\'appartenant pas au module',
	'entete_log_avertissement_peutetre_definition' => 'AVERTISSEMENT : items peut-être non définis',
	'entete_log_avertissement_peutetre_utilisation' => 'AVERTISSEMENT : items peut-être non utilisés',
	'entete_log_date_creation' => 'Fichier généré le @log_date_jour@ à @log_date_heure@.',
	'entete_log_erreur_definition' => 'ERREUR : items du module non définis',
	'entete_log_erreur_definition_nonmais' => 'ERREUR : items d\'autres modules non définis',
	'entete_log_erreur_fonction_l' => 'ERREUR : cas d\'utilisation de la fonction _L()',
	'entete_log_erreur_utilisation' => 'ERREUR : items non utilisés',

	// I
	'info_arborescence_scannee' => 'Choisissez le répertoire de base dont l\'arborescence sera scannée',
	'info_bloc_langues_generees' => 'Cliquez sur un lien ci-dessous pour télécharger l\'un des fichiers de langue générés.',
	'info_bloc_logs_definition' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des définitions manquantes d\'un fichier de langue.',
	'info_bloc_logs_fonction_l' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des utilisations de _L() dans une arborescence donnée.',
	'info_bloc_logs_utilisation' => 'Cliquez sur un lien ci-dessous pour télécharger le dernier fichier de logs de vérification des définitions obsolètes d\'un fichier de langue.',
	'info_chemin_langue' => 'Dossier dans lequel est installé le fichier de langue (exemple : <em>plugins/rainette/lang/</em>, ou <em>ecrire/lang/</em>)',
	'info_fichier_liste' => 'Choisissez le fichier de langue dont vous voulez afficher les items, parmi ceux présents dans le site.',
	'info_fichier_source' => 'Choisissez le fichier de langue qui servira de référence pour générer le fichier cible.',
	'info_fichier_verifie' => 'Choisissez le fichier de langue à vérifier parmi ceux présents dans le site.',
	'info_generer' => 'Cette option vous permet de générer, à partir d\'une langue source, le fichier de langue d\'un module donné dans une langue cible. Si le fichier cible existe déjà son contenu est réutilisé pour construire le nouveau fichier.',
	'info_langue' => 'Abréviation de la langue (exemple : <em>fr</em>, <em>en</em>, <em>es</em>...)',
	'info_lister' => 'Cette option vous permet de visualiser les items d\'un fichier de langue classés par ordre alphabétique.',
	'info_mode' => 'Correspond à la chaîne qui sera insérée lors de la création d\'un nouvel item pour la langue cible.',
	'info_module' => 'Correspond au préfixe du fichier de langue hors abréviation de la langue (exemple : <em>rainette</em> pour le plugin de même nom, ou <em>ecrire</em> pour SPIP)',
	'info_modules_recherche_item' => 'Par défaut, tous les modules disponibles sont sélectionnés pour la recherche. Si vous préférez choisir les modules à utiliser ouvrez la liste en décochant la case ci-dessous.',
	'info_modules_recherche_texte' => 'Par défaut, seuls les modules de langue SPIP sont sélectionnés pour la recherche. Si vous préférez choisir les modules à utiliser ouvrez la liste en décochant la case ci-dessous.',
	'info_pattern_item_cherche' => 'Saisissez un texte correspondant à tout ou partie d\'un raccourci d\'item de langue. La recherche est toujours insensible à la casse.',
	'info_pattern_texte_cherche' => 'Saisissez en UTF-8 un texte correspondant à tout ou partie d\'une traduction française d\'item de langue. La recherche est toujours insensible à la casse.',
	'info_rechercher_item' => 'Cette option vous permet de chercher des items de langue via leur raccourci dans les fichiers de langue présents sur le site. Par souci de performance, seuls les fichiers de langue française sont utilisés et les fichiers de langue <em>paquet-xxxx_fr.php</em> sont exclus.',
	'info_rechercher_texte' => 'Cette option vous permet de chercher des items de langue via leur traduction française dans les fichiers de langue de SPIP et des plugins disponibles. Par souci de performance, seuls les fichiers de langue française sont utilisés et les fichiers de langue <em>paquet-xxxx_fr.php</em> sont exclus.',
	'info_table' => 'Chaque ligne affiche l\'icone représentant l\'état de traduction (si le module est sous TradLang), le raccourci en gras et la traduction elle-même.',
	'info_tradlang_oui' => 'Ce module est traduit avec TradLang. La légende des couleurs de l\'état est la suivante :',
	'info_tradlang_non' => 'Ce module n\'est pas traduit avec TradLang ou correspond à la langue de référence.',
	'info_tradlang_statut_ok' => 'item correctement traduit',
	'info_tradlang_statut_modif' => 'item dont la traduction est obsolète (référence modifiée)',
	'info_tradlang_statut_new' => 'item non encore traduit',
	'info_verifier' => 'Cette option vous permet, d\'une part,  de vérifier les fichiers de langue d\'un module donné sous deux angles complémentaires. Il est possible, soit de vérifier si des items de langue utilisés dans un groupe de fichiers (un plugin, par exemple) ne sont pas définis dans le fichier de langue idoine, soit que certains items de langue définis ne sont plus utilisés. <br />D\'autre part, il est possible de lister et de corriger toutes les utilisations de la fonction _L() dans les fichiers PHP d\'une arborescence donnée.',

	// L
	'label_arborescence_scannee' => 'Arborescence à scanner',
	'label_avertissement' => 'Avertissements',
	'label_chemin_langue' => 'Localisation du fichier de langue',
	'label_correspondance' => 'Type de correspondance',
	'label_correspondance_commence' => 'Commence par',
	'label_correspondance_contient' => 'Contient',
	'label_correspondance_egal' => 'Égal à',
	'label_erreur' => 'Erreurs',
	'label_defaut_modules_item' => 'Tous les modules disponibles',
	'label_defaut_modules_texte' => 'Les modules SPIP (ecrire, spip, public)',
	'label_fichier_liste' => 'Fichier de langue',
	'label_fichier_source' => 'Fichier de langue source',
	'label_fichier_verifie' => 'Langue à vérifier',
	'label_langue_cible' => 'Langue cible',
	'label_langue_source' => 'Langue source',
	'label_mode' => 'Mode de création des nouveaux items',
	'label_module' => 'Module',
	'label_modules' => 'Modules utilisés pour le recherche',
	'label_pattern' => 'Texte à rechercher',
	'label_verification' => 'Type de vérification',
	'label_verification_definition' => 'Détection des définitions manquantes',
	'label_verification_fonction_l' => 'Détection des cas d\'utilisation de la fonction _L()',
	'label_verification_utilisation' => 'Détection des définitions obsolètes',
	'legende_generer_cible' => 'Fichier cible',
	'legende_generer_source' => 'Fichier source',
	'legende_resultats' => 'Résultats de la vérification',
	'legende_table' => 'Liste alphabétique des items du fichier de langue choisi',
	'legende_trouves' => 'Liste des items trouvés (@total@)',

	// M
	'message_nok_aucun_fichier_log' => 'Aucun fichier de log disponible au téléchargement',
	'message_nok_aucune_langue_generee' => 'Aucun fichier de langue généré disponible au téléchargement',
	'message_nok_champ_obligatoire' => 'Ce champ est obligatoire',
	'message_nok_ecriture_fichier' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» n\'a pas été créé car une erreur s\'est produite lors de son écriture !',
	'message_nok_lecture_fichier' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» n\'est pas accessible ou est vide !',
	'message_nok_fichier_langue' => 'La génération a échoué car le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» est introuvable dans le répertoire «<em>@dossier@</em>» !',
	'message_nok_fichier_log' => 'Le fichier de log contenant les résultats de la vérification n\'a pas pu être créé !',
	'message_nok_fichier_script' => 'Le fichier de script contenant les commandes de remplacement des fonctions _L par _T n\'a pas pu être créé !',
	'message_nok_item_trouve' => 'Aucun item de langue ne correspond à la recherche !',
	'message_ok_definis_incertains_0' => 'Aucun item de langue n\'est utilisé dans un contexte complexe, comme par exemple, _T(\'@module@:item_\'.$variable).',
	'message_ok_definis_incertains_1' => 'L\'item de langue ci-dessous est utilisé dans un contexte complexe et pourrait être non défini dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à le vérifier :',
	'message_ok_definis_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans un contexte complexe et pourraient être non définis dans le fichier de langue  «<em>@langue@</em>». Nous vous invitons à les vérifier un par un :',
	'message_ok_fichier_genere' => 'Le fichier de langue «<em>@langue@</em>» du module «<em>@module@</em>» a été généré correctement.<br />Vous pouvez récupérer le fichier «<em>@fichier@</em>».',
	'message_ok_fichier_log' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats.',
	'message_ok_fichier_log_script' => 'La vérification s\'est correctement déroulée. Vous pouvez consultez les résultats plus bas dans le formulaire.<br />Le fichier «<em>@log_fichier@</em>» a été créé pour sauvegarder ces résultats ainsi que le fichier des commandes de remplacement _L en _T, «<em>@script@</em>».',
	'message_ok_fonction_l_0' => 'Aucun cas d\'utilisation de la fonction _L() n\'a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>».',
	'message_ok_fonction_l_1' => 'Un seul cas d\'utilisation de la fonction _L() a été détecté dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>» :',
	'message_ok_fonction_l_n' => '@nberr@ cas d\'utilisation de la fonction _L() ont été détectés dans les fichiers PHP du répertoire «<em>@ou_fichier@</em>» :',
	'message_ok_item_trouve' => 'La recherche du texte @pattern@ s\'est déroulée correctement.',
	'message_ok_item_trouve_commence_1' => 'L\'item de langue ci-dessous commence par le texte recherché :',
	'message_ok_item_trouve_commence_n' => 'Les @sous_total@ items ci-dessous commencent tous par le texte recherché :',
	'message_ok_item_trouve_contient_1' => 'L\'item de langue ci-dessous contient le texte recherché :',
	'message_ok_item_trouve_contient_n' => 'Les @sous_total@ items ci-dessous contiennent tous le texte recherché :',
	'message_ok_item_trouve_egal_1' => 'L\'item de langue ci-dessous correspond exactement au texte recherché :',
	'message_ok_item_trouve_egal_n' => 'Les @sous_total@ items ci-dessous correspondent exactement au texte recherché :',
	'message_ok_non_definis_0' => 'Tous les items de langue du module «<em>@module@</em>» utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» sont bien définis dans le fichier de langue «<em>@langue@</em>».',
	'message_ok_non_definis_1' => 'L\'item de langue du module «<em>@module@</em>» affiché ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais n\'est pas défini dans le fichier de langue «<em>@langue@</em>» :',
	'message_ok_non_definis_n' => 'Les @nberr@ items de langue du module «<em>@module@</em>» affichés ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais ne sont pas définis dans le fichier de langue «<em>@langue@</em>» :',
	'message_ok_non_utilises_0' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>».',
	'message_ok_non_utilises_0s' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>».',
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers du répertoire «<em>@ou_fichier@</em>» :',
	'message_ok_non_utilises_1s' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers des répertoires «<em>@ou_fichier@</em>» :',
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» :',
	'message_ok_non_utilises_ns' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>» :',
	'message_ok_nonmais_definis_0' => 'Les fichiers du répertoire «<em>@ou_fichier@</em>» n\'utilisent aucun item de langue correctement défini dans un autre module que «<em>@module@</em>».',
	'message_ok_nonmais_definis_1' => 'L\'item de langue ci-dessous est utilisé correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais est défini dans un autre module que «<em>@module@</em>». Nous vous invitons à le vérifier :',
	'message_ok_nonmais_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés correctement dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais sont définis dans un autre module que «<em>@module@</em>». Nous vous invitons à les vérifier un par un :',
	'message_ok_nonmaisnok_definis_0' => 'Les fichiers du répertoire «<em>@ou_fichier@</em>» n\'utilisent aucun item de langue incorrectement défini dans un autre module que «<em>@module@</em>».',
	'message_ok_nonmaisnok_definis_1' => 'L\'item de langue ci-dessous est utilisé dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme un item du module «<em>@module@</em>». Étant donné qu\'il n\'est pas défini dans son module de rattachement, nous vous invitons à le vérifier :',
	'message_ok_nonmaisnok_definis_n' => 'Les @nberr@ items de langue ci-dessous sont utilisés dans des fichiers du répertoire «<em>@ou_fichier@</em>» mais pas comme des items du module «<em>@module@</em>». Étant donné qu\'ils ne sont pas définis dans leur module de rattachement, nous vous invitons à les vérifier un par un :',
	'message_ok_table_creee' => 'La table des items du fichier de langue @langue@ a été correctement créée.',
	'message_ok_utilises_incertains_0' => 'Aucun item de langue n\'est utilisé dans un contexte complexe (par exemple :  _T(\'@module@:item_\'.$variable)).',
	'message_ok_utilises_incertains_1' => 'L\'item de langue ci-dessous est peut-être utilisé dans un contexte complexe. Nous vous invitons à le vérifier :',
	'message_ok_utilises_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont peut-être utilisés dans un contexte complexe. Nous vous invitons à les vérifier un par un :',

	// O
	'onglet_generer' => 'Générer une langue',
	'onglet_lister' => 'Afficher une langue',
	'onglet_rechercher_item' => 'Rechercher un raccourci',
	'onglet_rechercher_texte' => 'Rechercher un texte',
	'onglet_verifier' => 'Vérifier une langue',
	'option_aucun_dossier' => 'aucune arborescence sélectionnée',
	'option_aucun_fichier' => 'aucune langue sélectionnée',
	'option_mode_index' => 'Item de la langue source',
	'option_mode_new' => 'Balise &lt;NEW&gt; uniquement',
	'option_mode_new_index' => 'Item de la langue source précédé de &lt;NEW&gt;',
	'option_mode_new_valeur' => 'Chaîne dans la langue source précédée de &lt;NEW&gt;',
	'option_mode_pas_item' => 'Ne pas créer d\'item',
	'option_mode_valeur' => 'Chaîne dans la langue source',
	'option_mode_vide' => 'Une chaîne vide',

	// T
	'texte_item_defini_ou' => '<em>défini dans :</em>',
	'texte_item_mal_defini' => '<em>mais pas défini dans le bon module :</em>',
	'texte_item_non_defini' => '<em>mais défini nulle part !</em>',
	'texte_item_utilise_ou' => '<em>utilisé dans :</em>',
	'titre_bloc_langues_generees' => 'Fichiers de langue',
	'titre_bloc_logs_definition' => 'Définitions manquantes',
	'titre_bloc_logs_fonction_l' => 'Utilisations de _L()',
	'titre_bloc_logs_utilisation' => 'Définitions obsolètes',
	'titre_form_generer' => 'Génération des fichiers de langue',
	'titre_form_lister' => 'Affichage des fichiers de langue',
	'titre_form_rechercher_item' => 'Recherche de raccourcis dans les fichiers de langue',
	'titre_form_rechercher_texte' => 'Recherche de textes dans les fichiers de langue',
	'titre_form_verifier' => 'Vérification des fichiers de langue',
	'titre_page' => 'LangOnet',
	'titre_page_navigateur' => 'LangOnet',
);

?>
