<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Enable management by keywords',
	'admin_complets' => 'Full Administrators',
	'admin_restreints' => 'Restricted Administrators?',
	'admin_tous' => 'All administrators (including restricted)',
	'administrateur' => 'administrator',
	'admins' => 'Administrators',
	'admins_redacs' => 'Administrators and Editors',
	'admins_rubriques' => 'The administrators associated to sections have:',
	'attention_crayons' => '<small><strong>Alert.</strong> The settings below can only work if you use a plugin providing an editing interface (like done by <a href="http://www.spip-contrib.net/Crayons,2698">Crayons</a>).</small>',
	'attention_version' => 'Note the following choices can not work with your version of SPIP:',
	'auteur_message_advitam' => 'The author of the message ad vitam',
	'auteur_message_heure' => 'The author of the message, during one hour',
	'auteur_modifie_article' => '<strong>Author modify article</strong> : Each editor can edit the articles he has written (and, consequently, moderate the forum and the associated petition).
	<br />
	<i>N.B. : This option also applies to registered visitors, whether they are authors and a specific interface is provided.</i>',
	'auteur_modifie_email' => '<strong>Editor modify email</strong> : Each editor can edit his email on his record of personal information.',
	'auteur_modifie_forum' => '<strong>Author moderate forum</strong> : Each editor may moderate the forum articles he has authored.',
	'auteur_modifie_petition' => '<strong>Author moderae petition</strong> : Each editor can moderate the petition of the articles he has authored.',

	// C
	'config_auteurs' => 'Configuring authors',
	'config_auteurs_rubriques' => 'What types of authors could be associated <b>to sections</b>?',
	'config_auteurs_statut' => 'When creating an author, what is <b>the default status</b>?',
	'config_plugin_qui' => 'Who can <strong>modify the configuration</strong> of the plugins (activation...)?',
	'config_site' => 'Configuring site',
	'config_site_qui' => 'Who can edit the <strong>site configuration</strong>?',
	'crayons' => 'Pencils',

	// D
	'deja_defini' => 'The following permissions are already defined elsewhere:',
	'deja_defini_suite' => 'The plugin « Authority » can not change certain settings below therefore it may not work.
	<br />To resolve this problem, you should check if your <tt>mes_options.php</tt> (or another active plugin) has defined these functions',
	'descriptif_1' => 'This setup page is reserved for the webmaster of the site:',
	'descriptif_2' => '<hr />
<p><small>If you want to edit this list, please edit the file <tt>config/mes_options.php</tt> (create it needed) and indicate the list of identifiers of webmasters, as follows:</small></p>
<html><pre>&lt;?php
  define (\'_ID_WEBMESTRES\',
  \'1:5:8\');
?&gt;</pre></html>
<p><small>Note: Webmasters defined in this way do not need to make the FTP authentication for sensitive operations (upgrading the database, for example).</small></p>

<a href=\'http://www.spip-contrib.net/-Autorite-\' class=\'spip_out\'>Cf. documentation</a>
',
	'details_option_auteur' => '<small><br />For now, the option "author" works only for registered authors (forums by subscription, for example). And if it is enabled, the site administrators also have the ability to edit the forums.
	</small>',
	'droits_des_auteurs' => 'Authors rights',
	'droits_des_redacteurs' => 'Editors rights',
	'droits_idem_admins' => 'the same rights as all administrators',
	'droits_limites' => 'limited rights to these sections',

	// E
	'effacer_base_option' => '<small><br />The recommended option is "not any one", the standard option of SPIP is "administrators" (but always with a check by FTP).</small>',
	'effacer_base_qui' => 'Who can <strong>erase</strong> the database site?',
	'espace_publieur' => 'Open publishing space',
	'espace_publieur_detail' => 'Choose below a sector to be treated as a open publishing space for visitors and / or editors (you have provided an interface, such as pencils and a form to submit article):',
	'espace_publieur_qui' => 'Would you open this section - beyond the administrators:',
	'espace_wiki' => 'Wiki Space',
	'espace_wiki_detail' => 'Choose below a sector to be treated as a wiki, being editable by everyone from the public space (you have provided an interface, such as pencils):',
	'espace_wiki_mots_cles' => 'Wiki space by keywords',
	'espace_wiki_mots_cles_detail' => '	Choose below the keywords that activate the wiki, being editable by everyone from the public space (you have provided an interface, such as pencils)',
	'espace_wiki_mots_cles_qui' => 'Would you open the wiki beyond administrators:',
	'espace_wiki_qui' => 'Would you open this wiki - beyond the administrators:',

	// F
	'forums_qui' => '<strong>Forums :</strong>  who may modify the contents of the Forums:',

	// I
	'icone_menu_config' => 'Authority',
	'infos_selection' => '(you can select multiple sectors with the shift key)',
	'interdire_admin' => 'Check the boxes below to prohibit administrators to create',

	// M
	'mots_cles_qui' => '<strong>Keywords :</strong> who can create and edit keywords :',

	// N
	'non_webmestres' => 'This setting is not applicable to the Webmasters.',
	'note_rubriques' => '<small><br />(Note that only administrators can create sections, and restricted administrators, can done this in their topics.)</small>',
	'nouvelles_rubriques' => 'new sections to the site root',
	'nouvelles_sous_rubriques' => 'new sub-sections in the tree.',

	// O
	'ouvrir_redacs' => 'Open to the editors of the site:',
	'ouvrir_visiteurs_enregistres' => 'Open to registered visitors:',
	'ouvrir_visiteurs_tous' => 'Open to all visitors site:',

	// P
	'pas_acces_espace_prive' => '<strong>No access to the private area:</ strong> editors don\'t have access to the private area.',
	'personne' => 'Not any one',
	'petitions_qui' => '<strong>Signatures :</strong> who can change the signatures on petitions:',
	'publication' => 'Publication',
	'publication_qui' => 'Who can publish on the website:',

	// R
	'redac_tous' => 'All editors',
	'redacs' => 'to site editors',
	'redacteur' => 'editor',
	'redacteur_lire_stats' => '<strong>Editor could see Stats</strong> : editors can view statistics.',
	'redacteur_modifie_article' => '<strong>Editor changes proposed</strong> : 	Each editor can edit an article proposed for publication, even if he is not the author.',
	'refus_1' => '<p>Only webmasters of the site',
	'refus_2' => 'are allowed to change these settings.</p>
<p>For more information, see <a href="http://www.spip-contrib.net/-Autorite-">the documentation</a>.</p>',
	'reglage_autorisations' => 'Setting Permissions',

	// S
	'sauvegarde_qui' => 'Who can perform <strong>backups</strong>?',

	// T
	'tous' => 'All',
	'tout_deselectionner' => ' deselect all',

	// V
	'valeur_defaut' => '(default value)',
	'visiteur' => 'visitor',
	'visiteurs_anonymes' => 'anonymous visitors can create new pages.',
	'visiteurs_enregistres' => 'to registred visitors',
	'visiteurs_tous' => 'to all site visitors.',

	// W
	'webmestre' => 'Webmaster',
	'webmestres' => 'Webmasters'
);

?>
