<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/xiti/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_xiti' => 'Activer',
	'activer_xiti_par_langue' => 'Configurer Xiti par langue',
	'activer_xiti_par_secteur' => 'Configurer Xiti par secteur',

	// E
	'explication_activer_xiti_par_langue' => 'Configurer un code XITI différent en fonction de chaque langue (compatible avec la configuration par secteur).',
	'explication_activer_xiti_par_secteur' => 'Configurer un code XITI différent en fonction de chaque secteur.',

	// I
	'icone_xiti' => 'Configurer Xiti',
	'item_activer_niveau_objets' => 'Activer les niveaux deux sur les contenus :',
	'item_langue_xiti_non' => 'Désactiver la configuration par langue',
	'item_langue_xiti_oui' => 'Activer la configuration par langue',
	'item_niveaux_deux' => 'Activer les niveaux 2',
	'item_non_utiliser_xiti' => 'Désactiver Xiti',
	'item_secteur_xiti_home' => 'Considérer ces secteurs comme la home',
	'item_secteur_xiti_non' => 'Désactiver la configuration par secteur',
	'item_secteur_xiti_oui' => 'Activer la configuration par secteur',
	'item_titre_chapitre' => 'Titre par chapitre',
	'item_titre_seul' => 'Titre par chapitre',
	'item_utiliser_xiti' => 'Activer Xiti',
	'item_xtdi_explication_xiti' => 'Degré d’implication. La valeur par défaut est 0 ou vide. Cette variable accepte les valeurs 1, 2, 3, 4 et 5 pour les pages ayant une implication non nulle.',
	'item_xtdi_xiti' => 'Variable xtdi',
	'item_xtdmc_explication_xiti' => 'Domaine de pose du cookie - nom de domaine au format « .domaine.com » (sans sous-domaine, commençant par un point).',
	'item_xtdmc_xiti' => 'Variable xtdmc',
	'item_xtn2_explication_xiti' => 'Identifiant numérique du niveau 2 dans lequel il faut ranger la page auditée. Les niveaux 2 sont à créer via votre interface XITI.',
	'item_xtn2_explication_xiti_accueil' => 'Identifiant numérique du niveau 2 uniquement pour l’accueil du site, la page sommaire.',
	'item_xtn2_home_xiti' => 'Variable "xtn2" ou "s2=" de la home de ce secteur',
	'item_xtn2_xiti' => 'Variable "xtn2" ou "s2="',
	'item_xtn2_xiti_accueil' => 'Variable "xtn2" ou "s2=" spécifique à l’accueil',
	'item_xtnv_explication_xiti' => 'Niveau d’arborescence HTML du site.
										Cette variable spécifiant l’emplacement du referrer à récupérer.
										Cette variable est renseignée par défaut à "document", elle doit être changée en "parent.document" dans le cas d’une frame/iframe sur le même nom de domaine
										(dans le cas de noms de domaines différents, contactez le Service Clients qu’il faut !).',
	'item_xtnv_xiti' => 'Variable xtnv',
	'item_xtor_explication_xiti' => 'La variable xtor doit contenir au minimum un préfixe indiquant le type de campagne, ainsi qu’un identifiant de campagne.',
	'item_xtor_xiti' => 'Variable xtor',
	'item_xtpage_explication_xiti' => '<strong>Obligatoire</strong> - création dynamique de chapitres ou simplement le titre des pages',
	'item_xtpage_xiti' => 'Variable "xtpage=" ou "p="',
	'item_xtsd_explication_xiti' => 'Sous-domaine du collecteur AT Internet. À récupérer dans le panneau Marqueurs. Cette information ne doit pas être modifiée.',
	'item_xtsd_xiti' => 'Variable xtsd',
	'item_xtsite_explication_xiti' => 'Identifiant numérique du site. À récupérer dans le panneau Marqueurs. Cette information ne doit pas être modifiée.',
	'item_xtsite_xiti' => 'Variable "xtsite=" ou "s="',

	// L
	'legend_activer_xiti' => 'Choix d’activer Xiti',
	'legend_configuration_generale' => 'Configuration générale',
	'legend_configuration_langue' => 'Configuration par langue',
	'legend_configuration_secteur' => 'Configuration par secteur',
	'legend_explication_obligatoire_xiti' => ' ',
	'legend_informations_xiti_langue' => 'Variables de configuration pour la langue "@lang@"',
	'legend_informations_xiti_secteur' => 'Variables de configuration du secteur "@titre@"',
	'legend_niveau_deux' => 'Niveaux 2',
	'legend_obligatoire_xiti' => 'Variables fixes et obligatoires',
	'legend_recommande_xiti' => 'Variables optionnelles dépendant de chaque page auditée (utilisation fortement recommandée)',
	'legend_recommande_xiti_campagne' => 'Variable optionnelle utilisé pour les campagnes emailings',

	// T
	'texte_xiti' => '<p>Activer Xiti, puis renseigner le formulaire de configuration du plugin</p>
					 <p>Consulter la documentation <a href="http://help.atinternet-solutions.com/FR/implementation/general/abouttagging_fr.htm">en ligne</a></p>',
	'titre_configurer' => 'Configurer Xiti',
	'titre_xiti' => 'Configuration du plugin Xiti'
);
