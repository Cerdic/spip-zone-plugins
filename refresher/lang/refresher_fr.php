<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'account_number' => 'Num&eacute;ro de compte',
	'add_cron_explanation' => 'Veuillez utiliser des URLs complets (en incluant \'http://...\'). Si l\'URL est dej&agrave; dans la liste, nous allons seulement mettre &agrave; jour sa fr&eacute;quence de rafraichissement. Toute fr&eacute;quence inf&eacute;rieure &agrave; 5 minutes sera trait&eacute;e toutes les 5 minutes (valeur minimum).',
	'add_cron_job' => 'Ajouter un URL',
	'add_url' => 'Ajouter URL',
	'akamai' => 'Akamai',
	'akamai_info' => '-Veuillez fournir les informations du compte Akamai',
	'article_modification' => 'Modification d\'article (mode \'Push\')',
	'article_modification_explication' => 'Ces actions ne prendront effet que si l\'article est d&eacute;ja publi&eacute;',
	'article_publication' => 'Publication d\'article (mode \'Push\')',
	'article_publication_explication' => 'Sont inclus la publication, retrait de publication que ce soit en modifiant le statut ou bien la date de l\'article',
	'authors_only' => 'auteurs seulement',
	// B
	
	// C
	'cloudflare' => 'Cloudflare',
	'config_title' => 'Configuration du plugin refresher',
	'count_cron_jobs' => 'URL(s) dans la file d\'attente',
	'count_rows1' => 'Il y a ',
	'count_rows2' => ' lignes dans la table.',
	// D
	'delete_on_cdn' => 'Supprimer sur CDN',
	'delete_spip_cache_by_name' => 'Supprimer des fichiers en cache SPIP par nom',
	'delete_spip_cache_by_name_explanation' => 'Vous pouvez supprimer des fichiers sp&eacute;cifiques du cache. Si vous sp&eacute;cifiez un r&eacute;pertoire, la fonction recherchera les fichiers correspondants dans ce r&eacute;pertoire uniquement.',
	'delete_spip_cache_by_date' => 'Supprimer des fichiers en cache SPIP par date',
	'delete_spip_cache_by_date_explanation' => 'Vous pouvez supprimer tous les fichiers en cache ayant &eacute;t&eacute; cr&eacute;&eacute;s entre deux dates sp&eacute;cifiques.',
	'document_article' => 'Ajout d\'un document &agrave; un article (mode \'Push\')',
	'document_article_explication' => 'Les m&ecirc;mes mesures seront prises si on enl&egrave;ve un document d\'un article. Ces actions ne prendront effet que si l\'article est d&eacute;ja publi&eacute;.',
	'document_modification' => 'Modification de document (mode \'Push\')',
	// E
	'edgecast' => 'Edgecast',
	'edgecast_info' => '-Veuillez fournir les informations du compte Edgecast',
	'empty_queue' => 'Vider la file d\'attente d\'URLs &agrave; rafraichir',
	'empty_queue_explanation' => 'Cette action va vider les URLs en attente dans le job queue. Toute requ&ecirc;te actuellement en attente ne sera donc pas trait&eacute;e.',
	'empty_table' => 'Vider la table',
	'every' => 'toutes les',
	'everyone' => 'tout le monde',
	'examples' => 'Exemples:
	<br><br>- <strong>3/nom_fichier</strong> -> recherche nom_fichier dans le r&eacute;pertoire 3
		<br>- <strong>a/prefixe*suffixe</strong> -> va supprimer tous les fichiers du r&eacute;pertoire "a" corespondants &agrave; prefixe+nimporte_quoi+suffixe
		<br>- <strong>5/*</strong> -> va supprimer tous les fichiers du r&eacute;pertoire 5
		<br>- <strong>nom_fichier</strong> -> va rechercher les fichiers nomm&eacute;s nom_fichier dans tous les r&eacute;pertoires
		<br>- <strong>nom_partiel*</strong> -> va supprimer tous les fichiers dont le nom commence par "nom_partiel", ceci dans tous les r&eacute;pertoires
		<br>- <strong>*</strong> -> ATTENTION! supprime tous les fichiers du cache',
	// F
	'file' => 'fichier',
	'files_removed' => 'fichier(s) ont &eacute;t&eacute; supprim&eacute;s',
	'forum_post' => 'Participation &agrave; un forum (mode \'Push\')',
	'frequence' => 'frequence',
	'from' => 'de',
	'full_url' => 'URL complet',
	// G
	'groupes_mots_liste' => '-Quels mots cl&eacute;s ont leur propre page sur le site qui n&eacute;cessitera un recalcul quand on mettra &agrave; jour ces mots cl&eacute;s ou les associera &agrave; un objet?',
	// I
	'invalideur_cdn' => '-Voulez-vous utiliser l\'invalidation sur CDN (purge)?',
	'invalideur_spip' => '-Voulez-vous utiliser le rafraichissement SPIP (recalcul)?',
	// L
	// M
	'main_configuration' => 'Configuration',
	'manage_cache' => 'Gestion du cache',
	'minutes' => 'minutes',
	'mot_article' => 'Ajout d\'un mot &agrave; un article (mode \'Push\')',
	'mot_article_explication' => 'Les m&ecirc;mes mesures seront prises si on enl&egrave;ve un mot d\'un article. Ces actions ne prendront effet que si l\'article est d&eacute;ja publi&eacute;.',
	'mot_modification' => 'Modification d\'un mot (mode \'Push\')',
	// N
	'no' => 'non',
	'no_cdn' => 'pas de CDN',
	'no_one' => 'personne',
	// P
	'password' => 'Mot de passe',
	'pause_explanation' => 'Ce temps de pause ne s\'appliquera qu\'entre deux rafraichissements issus d\'une m&ecirc;me action &eacute;ditoriale. Si deux actions &eacute;ditoriales diff&eacute;rentes sur le site sont effectives au m&ecirc;me moment leurs rafraichissements vont s\'effectuer parall&egrave;lement.',
	'pause_question' => '-Combien de temps de pause voulez-vous entre deux rafraichissements d\'URLs de la part du plugin? Ceci peut &eacute;viter une surcharge du serveur pendant les p&eacute;riodes de rafraichissement.',
	'please_select_groups' => 'Veuillez s&eacute;lectionner les groupes de mots concern&eacute;s.',
	// Q
	'queue_is_empty' => 'La file d\'attente est vide.',
	// R
	'rafraichir_url_manuellement' => 'Rafraichir manuellement un URL',
	'rafraichir_url_manuellement_explication' => 'Cette fonction va instantan&eacute;ment rafraichir un URL du site, c\'est &agrave; dire rafraichir le cache SPIP (si l\'option est activ&eacute;e dans la configuration) et/ou purger l\'URL dans le cache du CDN (si l\'option est activ&eacute;e dans la configuration). 
		Notez qu\'il faut quelques minutes au CDN pour nettoyer un cache sur ses servers. Veuillez utiliser des URLs complets.<br><br>
		Ex: http://www.monsite.com/monchemin/mapage.html',
	'recalcul_article' => 'Recalculer la page de l\'article',
	'recalcul_article_instant' => 'Recalculer la page de l\'article instantan&eacute;ment',
	'recalcul_auteurs' => 'Recalculer les pages des auteurs de l\'article',
	'recalcul_documents' => 'Recalculer les pages de tous les documents de l\'article',
	'recalcul_home' => 'Recalculer la homepage',
	'recalcul_mot' => 'Recalculer la page du mot',
	'recalcul_mots' => 'Recalculer les pages de mots associ&eacute;s (appartenant aux groupes selectionn&eacute;s)',
	'recalcul_mots2' => 'Recalculer les pages de mots de l\'article (appartenant aux groupes selectionn&eacute;s)',
	'recalcul_rubrique' => 'Recalculer la page de la rubrique',
	'refresher_cron' => 'Rafraichissements r&eacute;guliers',
	'refresher_cron_explanation' => 'Vous pouvez programmer des rafraichissements d\'URLs &agrave; une certaine fr&eacute;quence. Ces rafraichissements sont pris en charge par le job_queue, et seront effectu&eacute;s en t&acirc;che de fond. La pr&eacute;cision des rafraichissments est de 5 minutes. Si par exemple vous d&eacute;finissez une fr&eacute;quence de rafraichissement de 30 minutes pour un URL, l\'intervalle effectif sera entre 30 et 35 minutes.',
	'refresher_cron_is_empty' => 'La liste d\'URLs est vide.',
	'refresher_cron_list_title' => 'Liste des URLs &agrave; rafraichir &agrave; intervalles r&eacute;guliers',
	'remove_files' => 'Supprimer fichiers',
	'remove_files2' => 'Supprimer fichier(s)',
	'remove_selected_cron_jobs' => 'Supprimer les URLs s&eacute;lectionn&eacute;s',
	'rubrique_hierarchie' => 'Recalculer la hi&eacute;rarchie de la rubrique',
	'rubrique_modification' => 'Modification d\'une rubrique (mode \'Push\')',
	'rubrique_parent' => 'recalculer uniquement la rubrique parente',
	'rubrique_rien' => 'aucune action sur les rubriques',
	// S
	'seconds' => 'secondes',
	// T
	'to' => 'a',
	'token' => 'Token',
	// U
	'user' => 'Utilisateur',
	// V
	// W
	'warning_curl' => 'Attention! Vous devez installer l\'extension CURL pour PHP afin de pouvoir utiliser cette fonctionnalit&eacute; (CURL non d&eacute;tect&eacute;).',
	'warning_soap' => 'Attention! Vous devez installer l\'extension SOAP pour PHP afin de pouvoir utiliser cette fonctionnalit&eacute; (SOAP non d&eacute;tect&eacute;).',
	'webmasters_only' => 'webmestres seulement',
	'who_recalcul' => '-Qui peut utiliser var_mode=calcul/recalcul dans les URLs?',
	// Y
	'yes' => 'oui'
);

?>
