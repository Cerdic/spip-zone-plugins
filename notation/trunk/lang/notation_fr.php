<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/notation/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Accessibilité',
	'afficher_tables' => 'Afficher les notes',
	'aide' => 'Aide',
	'articles' => 'Articles',
	'auteur' => 'Auteur',

	// B
	'bouton_radio_fermee' => 'Fermée',
	'bouton_radio_ouvert' => 'Ouverte',
	'bouton_voter' => 'Voter',

	// C
	'change_note_label' => 'Autoriser les votants à modifier leur note',
	'configuration_notation' => 'Configurer les notations',
	'creation' => 'Création des tables',
	'creation_des_tables_mysql' => 'Création des tables',
	'cree' => 'Tables crées',
	'creer_tables' => 'Créer les tables',

	// D
	'date' => 'date',
	'derniers_votes' => 'Derniers votes',
	'destruction' => 'Destruction des tables',
	'detruire' => '<strong style="color:red">Attention, cette commande va détruire les tables du plugin !</strong><br />Vous ne devez l’utiliser que si vous voulez déactiver le plugin...',
	'detruit' => 'Tables détruites...',

	// E
	'effacer_tables' => 'Éffacer les tables',
	'err_balise' => '[ NOTATION_ERR : balise en dehors d’un article ]',
	'err_db_notation' => '[ NOTATION ERREUR : une seule notation par article ]',
	'exemple' => 'Distribution des notes (note = 5, facteur de pondération = @ponderation@) : ',
	'explication_accepter_note' => 'Si "fermée", la notation sera activable au cas par cas sur les objets ayant cette fonctionnalité.',

	// I
	'info_acces' => 'Ouvrir le vote : ',
	'info_etoiles' => 'Ce paramètre vous permet de modifier la valeure maximale de la note (le nombre d’étoiles, entre 1 et 10, et 5 par défaut).<br />
                    <strong style="color:red">/ !\\ Attention</strong> : vous ne devez pas toucher à ce paramètre une fois la notation engagée car les notes ne seront pas recalculées et cela peut provoquer des incohérences dans la notation...<br />
                    Ce paramètres doit être fixé une fois pour toute à la création des notes.',
	'info_fonctionnement_note' => 'Fonctionnement de la notation',
	'info_ip' => 'Pour être le plus facile possible d’utilisation, la note est fixée sur l’adresse IP du votant, ce qui évite deux votes successifs dans la base, avec quelques inconvénients... en particulier si vous gérez des votes d’auteurs.<br />
                Dans ce cas, on fixe la note sur l’identifiant de l’utilisateur (quand celui-ci est enregistré, bien sûr).<br />
                Si vous voulez garantir l’unicité de la note, limitez le vote aux <b>seules</b> personnes enregistrées (ci-dessus).',
	'info_modifications' => 'Modifications des notes',
	'info_ponderation' => 'Le facteur de pondération permet d’accorder plus de valeur aux articles ayant reçu suffisament de votes. <br /> Entrez ci-dessous la nombre de votes au delà duquel vous pensez que la note est fiable.',
	'ip' => 'IP',
	'item_adm' => 'aux administrateurs ',
	'item_all' => 'à tous ',
	'item_aut' => 'aux auteurs ',
	'item_id' => 'un vote par utilisateur ',
	'item_ide' => 'aux personnes enregistrées ',
	'item_ip' => 'un vote par IP',

	// J
	'jaidonnemonavis' => 'J’ai donné mon avis !',
	'jaime' => 'J’aime',
	'jaimepas' => 'Je n’aime pas',
	'jaimeplus' => 'Je n’aime plus',
	'jechangedavis' => 'Je retire mon avis',

	// L
	'label_accepter_note' => 'Statut de la notation sur tous les objets',

	// M
	'moyenne' => 'Moyenne',
	'moyennep' => 'Moyenne ponderée',

	// N
	'nb_etoiles' => 'Valeur des notes',
	'nbobjets_note' => 'Nombre d’objets ayant une note : ',
	'nbvotes' => 'Nombre de votes',
	'nbvotes_moyen' => 'Nombre de votes moyens par objet : ',
	'nbvotes_total' => 'Nombre de votes total sur le site : ',
	'notation' => 'Notations',
	'note' => 'Note : ',
	'note_1' => 'Note : 1',
	'note_10' => 'Note : 10',
	'note_2' => 'Note : 2',
	'note_3' => 'Note : 3',
	'note_4' => 'Note : 4',
	'note_5' => 'Note : 5',
	'note_6' => 'Note : 6',
	'note_7' => 'Note : 7',
	'note_8' => 'Note : 8',
	'note_9' => 'Note : 9',
	'note_pond' => 'Notes ponderées',
	'notes' => 'Notes',

	// O
	'objets' => 'Objets',

	// P
	'param' => 'Paramétrage',
	'ponderation' => 'Pondération de la note',

	// T
	'titre_ip' => 'Mode de fonctionnement :',
	'topnb' => 'Les 10 objets les plus notés',
	'topten' => 'Les 10 meilleures notes',
	'toptenp' => 'Les 10 meilleures notes (pondérées)',
	'totaux' => 'Totaux',

	// V
	'valeur_nb_etoiles' => 'Notation de 1 à ',
	'valeur_ponderation' => 'Facteur de pondération',
	'vos_notes' => 'Vos 5 meilleurs notes',
	'vote' => 'vote',
	'voter' => 'Voter : ',
	'votes' => 'votes',
	'votre_note' => 'Votre note'
);

?>
