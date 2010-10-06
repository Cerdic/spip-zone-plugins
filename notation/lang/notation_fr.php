<?php
/**
* Plugin Notation v.0.5
* par JEM (jean-marc.viglino@ign.fr) / b_b
*
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*
* Contextualisation des messages
*
**/
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'notation' => 'Notations',

	// Installation
	'creation_des_tables_mysql' => 'Cr&eacute;ation des tables',
	'creer_tables' => 'Cr&eacute;er les tables',
	'effacer_tables' => '&Eacute;ffacer les tables',
	'afficher_tables' => 'Afficher les notes',

	// Creation / destruction
	'creation' => 'Cr&eacute;ation des tables',
	'cree' => 'Tables cr&eacute;es',
	'destruction' => 'Destruction des tables',
	'detruit' => 'Tables d&eacute;truites...',
	'detruire' => '<strong style="color:red">Attention, cette commande va d&eacute;truire les tables du plugin !</strong><br />Vous ne devez l\'utiliser que si vous voulez d&eacute;activer le plugin...',

	// Affichage
	'vos_notes' => 'Vos 5 meilleurs notes',
	'topten' => 'Les 10 meilleures notes',
	'toptenp' => 'Les 10 meilleures notes (pond&eacute;r&eacute;es)',
	'topnb' => 'Les 10 objets les plus not&eacute;s',
	'objets' => 'Objets',
	'articles' => 'Articles',
	'notes' => 'Notes',
	'moyenne' => 'Moyenne',
	'votre_note' => 'Votre note',
	'notesp' => 'Notes ponder&eacute;es',
	'moyennep' => 'Moyenne ponder&eacute;e',
	'nbvotes' => 'Nb&nbsp;votes',
	'nbvotes_moyen' => 'Nombre de votes moyens par objet : ',
	'nbvotes_total' => 'Nombre de votes total sur le site : ',
	'nbobjets_note' => 'Nombre d\'objets ayant une note : ',

  // Parametrage
	'configuration_notation' => 'Configurer les notations',
	'param' => 'Param&eacute;trage',
	'ponderation' => 'Pond&eacute;ration de la note',
	'info_ponderation' => 'Le facteur de pond&eacute;ration permet d\'accorder plus de valeur aux articles ayant re&ccedil;u suffisament de votes. <br /> Entrez ci-dessous la nombre de votes au del&agrave; duquel vous pensez que la note est fiable.',
	'valeur_ponderation' => 'Facteur de pond&eacute;ration',
	'acces' => 'Accessibilit&eacute;',
	'info_acces' => 'Ouvrir le vote : ',
	'exemple' => 'Distribution des notes (note = 5, facteur de pond&eacute;ration = @ponderation@) : ',
	'item_adm' => 'aux administrateurs ',
	'item_aut' => 'aux auteurs ',
	'item_ide' => 'aux personnes enregistr&eacute;es ',
	'item_all' => '&agrave; tous ',
	'titre_ip' => 'Mode de fonctionnement :',
	'info_ip' => 'Pour &ecirc;tre le plus facile possible d\'utilisation, la note est fix&eacute;e sur l\'adresse IP du votant, ce qui &eacute;vite deux votes successifs dans la base, avec quelques inconv&eacute;nients... en particulier si vous g&eacute;rez des votes d\'auteurs.<br />
                Dans ce cas, on fixe la note sur l\'identifiant de l\'utilisateur (quand celui-ci est enregistr&eacute;, bien s&ucirc;r).<br />
                Si vous voulez garantir l\'unicit&eacute; de la note, limitez le vote aux <b>seules</b> personnes enregistr&eacute;es (ci-dessus).',
	'item_ip' => 'un votepar IP',
	'item_id' => 'un vote par utilisateur ',
	'nb_etoiles' => 'Valeur des notes',
	'info_etoiles' => 'Ce param&egrave;tre vous permet de modifier la valeure maximale de la note (le nombre d\'&eacute;toiles, entre 1 et 10, et 5 par d&eacute;faut).<br />
                    <strong style="color:red">/!\ Attention</strong> : vous ne devez pas toucher &agrave; ce param&egrave;tre une fois la notation engag&eacute;e car les notes ne seront pas recalcul&eacute;es et cela peut provoquer des incoh&eacute;rences dans la notation...<br />
                    Ce param&egrave;tres doit &ecirc;tre fix&eacute; une fois pour toute &agrave; la cr&eacute;ation des notes.',
	'valeur_nb_etoiles' => 'Notation de 1 &agrave; ',

	// Les erreurs
	'err_balise' => '[ NOTATION_ERR : balise en dehors d\'un article ]',
	'err_db_notation' => '[ NOTATION ERREUR : une seule notation par article ]',
	// Aide
	'aide' => 'Aide',

  // Affichage des notes
	'note_1' => 'Note : 1',
	'note_2' => 'Note : 2',
	'note_3' => 'Note : 3',
	'note_4' => 'Note : 4',
	'note_5' => 'Note : 5',
	'note_6' => 'Note : 6',
	'note_7' => 'Note : 7',
	'note_8' => 'Note : 8',
	'note_9' => 'Note : 9',
	'note_10' => 'Note : 10',

	// Formulaires
	'voter' => 'Voter : ',
	'note' => 'Note : ',
	'votes' => 'votes',
	'vote' => 'vote',
	'bouton_voter' => 'Voter',
	'auteur' => 'Auteur',
	'ip' => 'IP',

	'change_note_label' => "Autoriser les votants &agrave; modifier leur note",
	'info_modifications' => "Modifications des notes",

	'jaime' => 'J\'aime',
	'jaimepas' => 'Je n\'aime pas',
	'jaimeplus' => 'Je n\'aime plus',
	'jechangedavis' => 'Je retire mon avis',
	'jaidonnemonavis' => 'J\'ai donn&eacute; mon avis !',
	
);

?>
