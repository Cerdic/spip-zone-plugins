<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'account_number' => 'Account number',
	'add_cron_explanation' => 'Please use full URLs (i.e http://...). If the URL is already in the list, it\'s frequency will be updated only. The minimum frequency is set to 5 minutes, anything below will be processed every 5 minutes.',
	'add_cron_job' => 'Add a new URL',
	'add_url' => 'Add URL',
	'akamai' => 'Akamai',
	'akamai_info' => '-Please provide Akamai account information',
	'article_modification' => 'Article modification (\'Push\' mode)',
	'article_modification_explication' => 'Those actions will take effect only if article is already published',
	'article_publication' => 'Article publication (\'Push\' mode)',
	'article_publication_publication' => 'This includes publication, removal from publication wether by modifying the status or by modifying the date of article',
	'authors_only' => 'authors only',
	// B
	
	// C
	'cloudflare' => 'Cloudflare',
	'config_title' => 'Configuration of refresher plugin',
	'count_cron_jobs' => 'URL(s) in the cron job',
	'count_rows1' => 'There are ',
	'count_rows2' => ' rows in the table.',
	// D
	'delete_on_cdn' => 'Delete on CDN',
	'delete_spip_cache_by_name' => 'Delete cache files on SPIP by name',
	'delete_spip_cache_by_name_explanation' => 'You can remove specific files from the cache. If you specify the folder, it will look for matching files in this specific folder only.',
	'delete_spip_cache_by_date' => 'Delete cache files on SPIP by date',
	'delete_spip_cache_by_date_explanation' => 'You can choose to remove all cache files created between two selected dates.',
	'document_article' => 'Add document to article (\'Push\' mode)',
	'document_article_explication' => 'The same actions will be applied when removing document from article. Those actions will take effect only if article is published',
	'document_modification' => 'Document modification (\'Push\' mode)',
	// E
	'edgecast' => 'Edgecast',
	'edgecast_info' => '-Please provide Edgecast account information',
	'empty_queue' => 'Empty refresher queue',
	'empty_queue_explanation' => 'This action will empty the queue of URLs to refresh. Any awaiting page will remain unprocessed.',
	'empty_table' => 'Empty table',
	'everyone' => 'everyone',
	'examples' => 'Examples:
	<br><br>- <strong>3/filename</strong> -> will look for filename in folder 3
		<br>- <strong>a/prefix*suffix</strong> -> will remove all files in folder "a" matching prefix+anything+suffix
		<br>- <strong>5/*</strong> -> will remove all files from folder 5
		<br>- <strong>filename</strong> -> will look for filename in all cache folders
		<br>- <strong>partial_name*</strong> -> will remove all files from all folders starting with "partial_name"
		<br>- <strong>*</strong> -> dangerous! removes all files from cache',
	'every' => 'every',
	// F
	'file' => 'file',
	'files_removed' => 'file(s) have been removed',
	'forum_post' => 'Forum post on article (\'Push\' mode)',
	'frequence' => 'frequency',
	'from' => 'from',
	'full_url' => 'full URL',
	// G
	'groupes_mots_liste' => '-Which keywords have dedicated pages on the website that need to be recalculated when we update an associated object?',
	// I
	'invalideur_cdn' => '-Do you want to use CDN invalidation?',
	'invalideur_spip' => '-Do you want to activate the cache refreshing system with SPIP?',
	// L
	// M
	'main_configuration' => 'Main configuration',
	'manage_cache' => 'Manage cache',
	'minutes' => 'minutes',
	'mot_article' => 'Add keyword to article (\'Push\' mode)',
	'mot_article_explication' => 'The same actions will be applied when removing keyword from article. This will apply only if article already published.',
	'mot_modification' => 'Keyword modification (\'Push\' mode)',
	// N
	'no' => 'no',
	'no_cdn' => 'no CDN',
	'no_one' => 'no one',
	// P
	'password' => 'Password',
	'pause_explanation' => 'This pause time will work only between refresh actions from the same source. If 2 different sources of refreshing pages on the website are effective at the same time they will run independently.',
	'pause_question' => '-How many seconds do you want to wait between 2 pages updates triggered by the refresher? This can prevent server load peaks during invalidation periods',
	'please_select_groups' => 'Please select keyword groups.',
	// Q
	'queue_is_empty' => 'The queue is currently empty.',
	// R
	'rafraichir_url_manuellement' => 'Manually refresh URL',
	'rafraichir_url_manuellement_explication' => 'This action will instantly refresh an URL in the system, meaning refreshing the SPIP cache (if option selected in configuration) and/or purge the URL in CDN cache (if option selected in configuration). 
		Note that it takes a few minutes for the CDN to clear a cache on it\'s servers. Please use the full URL.
		Ex: http://www.mysite.com/mypath/mypage.html',
	'recalcul_article' => 'Refresh article page',
	'recalcul_article_instant' => 'Refresh article page instantly',
	'recalcul_auteurs' => 'Refresh article\'s authors pages',
	'recalcul_documents' => 'Refresh all documents pages from article',
	'recalcul_home' => 'Refresh website homepage',
	'recalcul_mot' => 'Refresh keyword page',
	'recalcul_mots' => 'Refresh associated keywords pages (from selected groups)',
	'recalcul_mots2' => 'Refresh article\'s keywords pages (from selected groups)',
	'recalcul_rubrique' => 'Refresh section page',
	'refresher_cron' => 'Manage cron jobs',
	'refresher_cron_explanation' => 'You can set URL refreshings at a specified frequency. Those URLs will be taken care of in a cron job. The frequency accuracy is up to 5 minutes. If, for instance, you define a refreshing interval of 30 minutes for an URL, the effective period between 2 refreshes will be from 30 to 35 minutes.',
	'refresher_cron_is_empty' => 'The list is empty.',
	'refresher_cron_list_title' => 'List of URLs to refresh',
	'remove_files' => 'Remove files',
	'remove_files2' => 'Remove file(s)',
	'remove_selected_cron_jobs' => 'Remove selected URLs',
	'rubrique_hierarchie' => 'Refresh all section hierarchy',
	'rubrique_modification' => 'Section modification (\'Push\' mode)',
	'rubrique_parent' => 'Refresh parent section only',
	'rubrique_rien' => 'No action on sections',
	// S
	'seconds' => 'seconds',
	// T
	'to' => 'to',
	'token' => 'Token',
	// U
	'user' => 'User',
	// V
	// W
	'warning_curl' => 'Warning! You need to install the CURL extension for PHP to use this feature (not detected).',
	'warning_soap' => 'Warning! You need to install the SOAP extension for PHP to use this feature (not detected).',
	'webmasters_only' => 'webmasters only',
	'who_recalcul' => '-Who can use var_mode=calcul/recalcul in URLs?',
	// Y
	'yes' => 'yes'
);

?>
