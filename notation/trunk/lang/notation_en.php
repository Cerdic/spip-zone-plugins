<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/notation?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Accessibility',
	'afficher_tables' => 'Show ratings',
	'aide' => 'Help',
	'articles' => 'Articles',
	'auteur' => 'Author',

	// B
	'bouton_radio_fermee' => 'Closed',
	'bouton_radio_ouvert' => 'Opened',
	'bouton_voter' => 'Vote',

	// C
	'change_note_label' => 'Allow voters to change their rating',
	'configuration_notation' => 'Configure ratings',
	'creation' => 'Creating tables',
	'creation_des_tables_mysql' => 'Creating tables',
	'cree' => 'Tables created',
	'creer_tables' => 'Create the tables',

	// D
	'date' => 'date',
	'derniers_votes' => 'Last votes',
	'destruction' => 'Destruction of tables',
	'detruire' => '<strong style="color:red">Warning, this command will destroy the tables of the plugin !</strong><br />You should use it only if you want to uninstall the plugin...',
	'detruit' => 'Tables destroyed...',

	// E
	'effacer_tables' => 'Erase the tables',
	'err_balise' => '[ NOTATION_ERR : tag outside an article loop ]',
	'err_db_notation' => '[ RATING ERROR : only one rating by article ]',
	'exemple' => 'Score distribution (score = 5, weighting factor = @ponderation@) : ',
	'explication_accepter_note' => 'If "closed", the rating can be activated individually on objects that have this feature.',

	// I
	'info_acces' => 'Open voting : ',
	'info_etoiles' => 'This setting allows you to change the maximum value of the note (the number of stars between 1 and 10, and 5 by default).<br />
                    <strong style="color:red">/!\\ Warning</strong> : you should not modify this setting after the scoring to be initiated because the ratings will not be calculated again and this can cause inconsistencies in the scoring ...<br />
                    This parameter must be fixed once and for all when creating ratings.',
	'info_fonctionnement_note' => 'How rating works',
	'info_ip' => 'To be the easiest possible to use, the rating is attached to the IP address of the voter, which avoids two successive votes in the database, with a few drawbacks ...  especially if you manage author’s votes.<br />
				In this case, we set the note on the user identifier (when he is registered, of course).<br />
                If you want to guarantee the uniqueness of the note, limit the voting <b>only</b> to registered users (above).',
	'info_modifications' => 'Changing of the ratings',
	'info_ponderation' => 'The weighting factor lends more value to items that have received enough votes.<br />Enter below the number of votes beyond which you think the score is reliable.',
	'ip' => 'IP',
	'item_adm' => 'to the administrators',
	'item_all' => 'to all',
	'item_aut' => 'to the editors',
	'item_id' => 'One vote by user',
	'item_ide' => 'to registered people',
	'item_ip' => 'one vote by IP',

	// J
	'jaidonnemonavis' => 'I gave my opinion !',
	'jaime' => 'I like',
	'jaimepas' => 'I don’t like',
	'jaimeplus' => 'I no longer like',
	'jechangedavis' => 'I withdraw my opinion',

	// L
	'label_accepter_note' => 'Status of the rating on all objects',

	// M
	'moyenne' => 'Average',
	'moyennep' => 'Weighted average',

	// N
	'nb_etoiles' => 'Ratings value',
	'nbobjets_note' => 'Number of rated items : ',
	'nbvotes' => 'Ratings number',
	'nbvotes_moyen' => 'Number of average votes by item : ',
	'nbvotes_total' => 'Total number of votes on the site : ',
	'notation' => 'Ratings',
	'note' => 'Rating : ',
	'note_1' => 'Rating : 1',
	'note_10' => 'Rating : 10',
	'note_2' => 'Rating : 2',
	'note_3' => 'Rating : 3',
	'note_4' => 'Rating : 4',
	'note_5' => 'Rating : 5',
	'note_6' => 'Rating : 6',
	'note_7' => 'Rating : 7',
	'note_8' => 'Rating : 8',
	'note_9' => 'Rating : 9',
	'note_pond' => 'Weighted ratings',
	'notes' => 'Ratings',

	// O
	'objets' => 'Items',

	// P
	'param' => 'Setting',
	'ponderation' => 'Weighting of the rating',

	// T
	'titre_ip' => 'Mode:',
	'topnb' => 'The 10 items which are the most rated',
	'topten' => 'The top 10 scores',
	'toptenp' => 'The 10 highest scores (weighted)',
	'totaux' => 'Totals',

	// V
	'valeur_nb_etoiles' => 'Rating from 1 to ',
	'valeur_ponderation' => 'Weighting factor',
	'vos_notes' => 'Your top 5 scores',
	'vote' => 'vote',
	'voter' => 'Vote : ',
	'votes' => 'Votes',
	'votre_note' => 'Your rating'
);

?>
