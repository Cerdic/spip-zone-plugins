<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_continuer' => 'Passer à l\'étape 2',
	'bouton_retourner' => 'Retourner à l\'étape 1',
	'bouton_taxonomie' => 'Taxons',
	'bouton_wikipedia_descriptif' => 'Remplir le descriptif avec Wikipedia',

	// C
	'credit_itis' => 'Integrated Taxonomic Information System, @url_site@ (informations taxonomiques de base). Voir aussi la page du taxon @url_taxon@.',
	'credit_cinfo' => 'Commission internationale des noms français des oiseaux (CINFO), @url@.',
	'credit_wikipedia' => 'Wikipedia (@champs@). Voir aussi la page du taxon @url_taxon@.',

	// E
	'erreur_vider_regne' => 'Erreur lors du vidage du règne @regne@ en base de données.',
	'erreur_charger_regne' => 'Erreur lors du chargement du règne @regne@ en base de données.',
	'erreur_wikipedia_descriptif' => 'Aucun descriptif dans la langue choisie n\'a pu être récupéré de Wikipedia.',
	'explication_action_regne' => 'Si le règne est déjà présent en base de données, tous les taxons qui le composent seront supprimés avant le chargement.',
	'explication_langues_regne' => 'Les taxons sont chargés par défaut avec leur nom scientifique. Cette option permet de compléter certains taxons avec leur nom commun dans la ou les langues précisées.',
	'explication_langues_utilisees' => 'Le plugin supporte quelques langues comme le français, l\'anglais et l\'espagnol. Cela permet de charger voire de saisir manuellement les noms communs et descriptifs dans ces langues.
	Néanmoins, en fonction de votre besoin vous pouvez limiter l\'utilisation de ces langues mais une langue est au moins requise.',
	'explication_type_rang' => 'Le chargement de tous les taxons incluant les rangs intercalaires peut augmenter significativement le temps de traitement.',
	'explication_wikipedia_langue' => 'Si vous utilisez plusieurs langues pour traduire vos taxons, choisissez la langue à utiliser pour récupérer le descriptif.',
	'explication_wikipedia_descriptif' => 'Vérifier si ce descriptif est bien celui qui décrit le mieux le taxon. Si non, choisissez une page alternative parmi celle éventuellement proposée dans la liste ci-dessous.',
	'explication_wikipedia_lien' => 'Choisissez la page Wikipedia que vous souhaitez intégrer comme descriptif du taxon.',

	// F
	'filtre_edite_oui' => 'Taxons édités',
	'filtre_edite_non' => 'Taxons non édités',
	'filtre_edite_tout' => 'Tous les taxons',
	'filtre_regnes_tout' => 'Tous les règnes',

	// I
	'info_boite_taxonomie_gestion' => 'Cette page permet aux webmestres de consulter, charger, mettre à jour ou vider les règnes animal, végétal et fongique gérés par le plugin.',
	'info_boite_taxonomie_navigation' => 'Cette page permet aux utilisateurs de consulter la liste des taxons chargés en base de données et de naviguer de taxon en taxon.',
	'info_descriptif_existe' => 'non vide',
	'info_etape' => 'Etape @etape@ / @etapes@',
	'info_indicateur_hybride' => 'Ce taxon est un hydribe',
	'info_regne_charge' => 'déjà chargé',
	'info_regne_compteur_taxons' => '@nb@ taxons chargés du règne au genre (@type_rang@)',
	'info_regne_compteur_traductions' => '@nb@ noms communs en [@langue@]',

	// L
	'label_action_charger_regne' => 'Charger un règne',
	'label_action_regne' => 'Action à exécuter',
	'label_action_vider_regne' => 'Vider un règne',
	'label_ascendance' => 'Ascendance taxonomique',
	'label_colonne_actualisation' => 'Actualisé le',
	'label_colonne_statistiques' => 'Statistiques',
	'label_type_rang' => 'Types de rangs à charger jusqu\'au genre',
	'label_regne' => 'Règne sur lequel appliquer l\'action',
	'label_langue_descriptif' => 'Langue du descriptif',
	'label_langues_regne' => 'Langues des noms communs',
	'label_langues_utilisees' => 'Langues à utiliser',
	'label_type_rang_intercalaire' => 'rangs principaux, secondaires et intercalaires',
	'label_type_rang_principal' => 'rangs principaux',
	'label_type_rang_secondaire' => 'rangs principaux et secondaires',
	'label_wikipedia_alternative_defaut' => 'Utiliser le descriptif proposé par défaut',
	'label_wikipedia_alternative' => 'Utiliser la page « @alternative@ »',
	'label_wikipedia_descriptif' => 'Descriptif Wikipedia fourni par défaut',
	'label_wikipedia_langue' => 'Langue à utiliser par Wikipedia',
	'label_wikipedia_lien' => 'Page Wikipedia à utiliser',

	// N
	'notice_vider_regne_inexistant' => 'Le règne @regne@ n\'a pas été trouvé en base de données.',
	'notice_liste_aucun_regne' => 'Aucun règne n\'a encore été chargé en base de données. Utiliser le formulaire ci-dessous pour y remédier.',

	// R
	'rang_kingdom' => 'règne',
	'rang_subkingdom' => 'sous-règne',
	'rang_infrakingdom' => 'infra-règne',
	'rang_superdivision' => 'super-division',
	'rang_division' => 'division',
	'rang_subdivision' => 'sous-division',
	'rang_infradivision' => 'infra-division',
	'rang_superphylum' => 'super-phylum',
	'rang_phylum' => 'phylum',
	'rang_subphylum' => 'sous-phylum',
	'rang_infraphylum' => 'infra-phylum',
	'rang_superclass' => 'super-classe',
	'rang_class' => 'classe',
	'rang_subclass' => 'sous-classe',
	'rang_infraclass' => 'infra-classe',
	'rang_superorder' => 'super-ordre',
	'rang_order' => 'ordre',
	'rang_suborder' => 'sous-ordre',
	'rang_infraorder' => 'infra-ordre',
	'rang_section' => 'section',
	'rang_subsection' => 'sous-section',
	'rang_superfamily' => 'super-famille',
	'rang_family' => 'famille',
	'rang_subfamily' => 'sous-famille',
	'rang_tribe' => 'tribu',
	'rang_subtribe' => 'sous-tribu',
	'rang_genus' => 'genre',
	'regne_animalia' => 'règne animal',
	'regne_fungi' => 'règne fongique',
	'regne_plantae' => 'règne végétal',

	// O
	'onglet_gestion' => 'Gestion des règnes',
	'onglet_configuration' => 'Configuration du plugin',
	'onglet_navigation' => 'Navigation du règne au genre',

	// S
	'succes_vider_regne' => 'Le règne @regne@ a bien été supprimé de la base de données.',
	'succes_charger_regne' => 'Le règne @regne@ a bien été chargé en base de données.',

	// T
	'titre_form_configuration' => 'Configuration du plugin',
	'titre_form_gestion_regne' => 'Gestion des règnes',
	'titre_liste_regnes' => 'Liste des règnes chargés en base de données',
	'titre_liste_fils_taxon' => 'Liste des descendants directs du taxon',
	'titre_page_decrire_wikipedia' => 'Descriptif Wikipedia du taxon <span class="nom_scientifique">@taxon@</span>',
	'titre_page_taxonomie' => 'Taxonomie',
);

?>
