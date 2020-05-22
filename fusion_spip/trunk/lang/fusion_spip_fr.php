<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/fusion_spip.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'base' => 'Site source',
	'base_desc' => 'La base de données du site source doit être <a href="/ecrire/?exec=admin_tech">déclarée comme base externe</a>.<br/>
	La base du site source doit être dans la même version que celle du site hôte',
	'bouton_importer' => 'Démarrer la fusion',
	'bouton_supprimer' => 'Supprimer la fusion',

	// C
	'confirme_traduire_documents_doublons' => 'Traduire les documents en doublons en utilisant les champs multis',
	'confirme_warning' => 'Confirmer la fusion des bases ?',

	// D
	'dossier_existe_pas' => 'Le répertoire @dossier@ n’existe pas',
	'dossier_pas_lisible' => 'Le répertoire @dossier@ n’est pas accessible en lecture',

	// E
	'erreur_img_accessible' => 'Votre répertoire IMG n’est pas accessible. Il est impossible soit d’y écrire un fichier à la racine, soit d’y créer un sous-répertoire.',
	'erreur_source_inaccessible' => 'Erreur de lecture du répertoire IMG de la source',
	'erreur_traduction_document' => 'Vos bases sont dans des langues différentes, en cochant la case suivante, vous pouvez traduire le contenu des documents en doublon en utilisant les champs multis.',
	'erreur_version_indeterminee' => 'indéterminée (clé version_installee de la table spip_meta introuvable)',
	'erreur_versions' => 'Le site hôte et le site source ne sont pas dans la même version de base de données :
		<br/>- hôte est en version : @vhote@
		<br/>- source est en version : @vsource@',
	'erreur_versions_impossible' => 'Impossible de vérifier la version de la base de données importée (table spip_meta introuvable)',

	// I
	'img_dir' => 'Chemin physique des documents',
	'img_dir_desc' => 'Pour copier les documents du site source dans le site hôte, indiquez leur chemin physique (chemin absolu sur le disque dur, par exemple <code>/home/edgard/www/edgard_spip/IMG</code>). Si le champ est vide, aucun document ne sera importé, vous devrez les copier manuellemnent.',

	// M
	'maj_base' => 'Mise à jour de la base de données',
	'manque_champs_source' => 'Les champs @diff@ manquent dans la table "@table@" de la base source',
	'manque_champs_hote' => 'Les champs @diff@ manquent dans la table "@table@" de la base hote',
	'manque_table_source' => 'La table "@table@" est absente dans la base source',
	'message_img_dir_nok' => 'Merci de préciser le chemin',
	'message_import_nok' => 'Erreur lors de la fusion',
	'message_import_ok' => 'Fusion terminée<br>Log détaillé : <code>tmp/log/fusion_spip_fusion_spip*.log</code><br><br>Voici un résumé des objets importés :<br>',
	'message_suppression_ok' => 'Objets supprimés',

	// R
	'referers' => 'Ne pas traiter les referers (liens entrants)',

	// S
	'secteur' => 'Secteur',
	'secteur_desc' => 'Pour importer le site source dans un secteur, sinon il sera importé à la racine',
	'stats' => 'Ne pas traiter les statistiques',

	// T
	'titre_fusion_spip' => 'Fusion de sites Spip',
	'titre_fusion_spip_suppression' => 'Suppression'
);
