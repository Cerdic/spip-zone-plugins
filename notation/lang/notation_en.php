<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) / b_b
*
* Copyright (c) 2008-2010
* Logiciel libre distribue sous licence GNU/GPL.
*
* Contextualisation des messages
*
**/
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'notation' => 'Ratings',

	// Installation
	'creation_des_tables_mysql' => 'Creating tables',
	'creer_tables' => 'Create the tables',
	'effacer_tables' => 'Erase the tables',
	'afficher_tables' => 'Show ratings',

	// Creation / destruction
	'creation' => 'Creating tables',
	'cree' => 'Tables created',
	'destruction' => 'Destruction of tables',
	'detruit' => 'Tables destroyed...',
	'detruire' => '<strong style="color:red">Warning, this command will destroy the tables of the plugin !</strong><br />You should use it only if you want to uninstall the plugin...',

	// Affichage
	'vos_notes' => 'Your top 5 scores',
	'topten' => 'The top 10 scores',
	'toptenp' => 'The 10 highest scores (weighted)',
	'topnb' => 'The 10 items which are the most rated',
	'objets' => 'Items',
	'articles' => 'Articles',
	'notes' => 'Ratings',
	'moyenne' => 'Average',
	'votre_note' => 'Your rating',
	'notesp' => 'Weighted scores',
	'moyennep' => 'Weighted average',
	'nbvotes' => 'Ratings&nbsp;nb',
	'nbvotes_moyen' => 'Number of average votes by item : ',
	'nbvotes_total' => 'Total number of votes on the site : ',
	'nbobjets_note' => 'Number of rated items : ',

  // Parametrage
	'configuration_notation' => 'Configure ratings',
	'param' => 'Setting',
	'ponderation' => 'Weighting of the rating',
	'info_ponderation' => 'The weighting factor lends more value to items that have received enough votes.<br />Enter below the number of votes beyond which you think the score is reliable.',
	'valeur_ponderation' => 'Weighting factor',
	'acces' => 'Accessibility',
	'info_acces' => 'Open voting : ',
	'exemple' => 'Score distribution (score = 5, weighting factor = @ponderation@) : ',
	'item_adm' => 'to the administrators',
	'item_aut' => 'to the editors',
	'item_ide' => 'to registered people',
	'item_all' => 'to all',
	'titre_ip' => 'Mode:',
	'info_ip' => 'To be the easiest possible to use, the rating is attached to the IP address of the voter, which avoids two successive votes in the database, with a few drawbacks ...  especially if you manage author\'s votes.<br />
				In this case, we set the note on the user identifier (when he is registered, of course).<br />
                If you want to guarantee the uniqueness of the note, limit the voting <b>only</b> to registered users (above).',
	'item_ip' => 'one vote by IP',
	'item_id' => 'One vote by user',
	'nb_etoiles' => 'Ratings value',
	'info_etoiles' => 'This setting allows you to change the maximum value of the note (the number of stars between 1 and 10, and 5 by default).<br />
                    <strong style="color:red">/!\ Warning</strong> : you should not modify this setting after the scoring to be initiated because the ratings will not be calculated again and this can cause inconsistencies in the scoring ...<br />
                    This parameter must be fixed once and for all when creating ratings.',
	'valeur_nb_etoiles' => 'Rating from 1 to ',

	// Les erreurs
	'err_balise' => '[ NOTATION_ERR : tag outside an article loop ]',
	'err_db_notation' => '[ RATING ERROR : only one rating by article ]',
	// Aide
	'aide' => 'Help',

  // Affichage des notes
	'note_1' => 'Rating : 1',
	'note_2' => 'Rating : 2',
	'note_3' => 'Rating : 3',
	'note_4' => 'Rating : 4',
	'note_5' => 'Rating : 5',
	'note_6' => 'Rating : 6',
	'note_7' => 'Rating : 7',
	'note_8' => 'Rating : 8',
	'note_9' => 'Rating : 9',
	'note_10' => 'Rating : 10',

	// Formulaires
	'voter' => 'Vote : ',
	'note' => 'Rating : ',
	'votes' => 'Votes',
	'vote' => 'vote',
	'bouton_voter' => 'Vote',
	'auteur' => 'Author',
	'ip' => 'IP',

	'change_note_label' => "Allow voters to change their rating",
	'info_modifications' => "Changing of the ratings",

	'jaime' => 'I like',
	'jaimepas' => 'I don\'t like',
	'jaimeplus' => 'I no longer like',
	'jechangedavis' => 'I withdraw my opinion',
	'jaidonnemonavis' => 'I gave my opinion !',
	
);

?>
