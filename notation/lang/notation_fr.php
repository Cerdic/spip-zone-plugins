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

	// A
	'acces' => 'Accessibilit&eacute;',
	'afficher_tables' => 'Afficher les notes',
	'aide' => 'Aide',
	'articles' => 'Articles',
	'auteur' => 'Auteur',

	// B
	'bouton_radio_fermee' => 'Ferm&eacute;e',
	'bouton_radio_ouvert' => 'Ouverte',
	'bouton_voter' => 'Voter',

	// C
	'change_note_label' => "Autoriser les votants &agrave; modifier leur note",
	'configuration_notation' => 'Configurer les notations',
	'creation' => 'Cr&eacute;ation des tables',
	'creation_des_tables_mysql' => 'Cr&eacute;ation des tables',
	'cree' => 'Tables cr&eacute;es',
	'creer_tables' => 'Cr&eacute;er les tables',

	// D
	'destruction' => 'Destruction des tables',
	'detruit' => 'Tables d&eacute;truites...',
	'detruire' => '<strong style="color:red">Attention, cette commande va d&eacute;truire les tables du plugin !</strong><br />Vous ne devez l\'utiliser que si vous voulez d&eacute;activer le plugin...',

	// E
	'effacer_tables' => '&Eacute;ffacer les tables',
	'err_balise' => '[ NOTATION_ERR : balise en dehors d\'un article ]',
	'err_db_notation' => '[ NOTATION ERREUR : une seule notation par article ]',
	'exemple' => 'Distribution des notes (note = 5, facteur de pond&eacute;ration = @ponderation@) : ',
	'explication_accepter_note' => 'Si "ferm&eacute;e", la notation sera activable au cas par cas sur les objets ayant cette fonctionnalit&eacute;.',

	// I
	'info_acces' => 'Ouvrir le vote : ',
	'info_etoiles' => 'Ce param&egrave;tre vous permet de modifier la valeure maximale de la note (le nombre d\'&eacute;toiles, entre 1 et 10, et 5 par d&eacute;faut).<br />
                    <strong style="color:red">/!\ Attention</strong> : vous ne devez pas toucher &agrave; ce param&egrave;tre une fois la notation engag&eacute;e car les notes ne seront pas recalcul&eacute;es et cela peut provoquer des incoh&eacute;rences dans la notation...<br />
                    Ce param&egrave;tres doit &ecirc;tre fix&eacute; une fois pour toute &agrave; la cr&eacute;ation des notes.',
	'info_fonctionnement_note' => 'Fonctionnement de la notation',
	'info_ip' => 'Pour &ecirc;tre le plus facile possible d\'utilisation, la note est fix&eacute;e sur l\'adresse IP du votant, ce qui &eacute;vite deux votes successifs dans la base, avec quelques inconv&eacute;nients... en particulier si vous g&eacute;rez des votes d\'auteurs.<br />
                Dans ce cas, on fixe la note sur l\'identifiant de l\'utilisateur (quand celui-ci est enregistr&eacute;, bien s&ucirc;r).<br />
                Si vous voulez garantir l\'unicit&eacute; de la note, limitez le vote aux <b>seules</b> personnes enregistr&eacute;es (ci-dessus).',
	'info_modifications' => "Modifications des notes",
	'info_ponderation' => 'Le facteur de pond&eacute;ration permet d\'accorder plus de valeur aux articles ayant re&ccedil;u suffisament de votes. <br /> Entrez ci-dessous la nombre de votes au del&agrave; duquel vous pensez que la note est fiable.',
	'ip' => 'IP',
	'item_adm' => 'aux administrateurs ',
	'item_all' => '&agrave; tous ',
	'item_aut' => 'aux auteurs ',
	'item_ide' => 'aux personnes enregistr&eacute;es ',
	'item_id' => 'un vote par utilisateur ',
	'item_ip' => 'un votepar IP',

	// J
	'jaidonnemonavis' => 'J\'ai donn&eacute; mon avis !',
	'jaime' => 'J\'aime',
	'jaimepas' => 'Je n\'aime pas',
	'jaimeplus' => 'Je n\'aime plus',
	'jechangedavis' => 'Je retire mon avis',

	// L
	'label_accepter_note' => 'Statut de la notation sur tous les objets',

	// M
	'moyenne' => 'Moyenne',
	'moyennep' => 'Moyenne ponder&eacute;e',

	// N
	'nb_etoiles' => 'Valeur des notes',
	'nbobjets_note' => 'Nombre d\'objets ayant une note : ',
	'nbvotes' => 'Nb&nbsp;votes',
	'nbvotes_moyen' => 'Nombre de votes moyens par objet : ',
	'nbvotes_total' => 'Nombre de votes total sur le site : ',
	'notation' => 'Notations',
	'note' => 'Note : ',
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
	'notes' => 'Notes',
	'notesp' => 'Notes ponder&eacute;es',

	// O
	'objets' => 'Objets',

	// P
	'param' => 'Param&eacute;trage',
	'ponderation' => 'Pond&eacute;ration de la note',

	// T
	'titre_ip' => 'Mode de fonctionnement :',
	'topnb' => 'Les 10 objets les plus not&eacute;s',
	'topten' => 'Les 10 meilleures notes',
	'toptenp' => 'Les 10 meilleures notes (pond&eacute;r&eacute;es)',

	// V
	'valeur_nb_etoiles' => 'Notation de 1 &agrave; ',
	'valeur_ponderation' => 'Facteur de pond&eacute;ration',
	'vos_notes' => 'Vos 5 meilleurs notes',
	'vote' => 'vote',
	'voter' => 'Voter : ',
	'votes' => 'votes',
	'votre_note' => 'Votre note',
);

?>
