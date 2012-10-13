<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/association');
include_spip('base/abstract_sql');

// desinstallation
function association_vider_tables($nom_meta, $table) {
	// on efface la meta [association_meta][base_version] pour que la fonction qui gere la desinstallation ne voie plus le plugin et confirme sa desinstallation
	effacer_meta($nom_meta, $table);
	spip_log("Plugin Associaspip (vb:$nom_meta_base_version) dereference",'associaspip');

	// On liste les tables du plugin
	$tables_a_supprimer = array(
		'spip_asso_activites',
		'spip_asso_categories',
		'spip_asso_comptes',
		'spip_asso_destination',
		'spip_asso_destination_op',
		'spip_asso_dons',
		'spip_asso_exercices',
		'spip_asso_groupes',
		'spip_asso_groupes_liaisons',
		'spip_asso_membres',
		'spip_asso_plan',
		'spip_asso_prets',
		'spip_asso_ressources',
		'spip_asso_ventes',
		'spip_association_metas',
	);
	// On efface les tables du plugin en consignant le resultat dans /tmp/spip_prive.log
	foreach($tables_a_supprimer as $table ) {
		if (sql_drop_table($table))
			spip_log("Associaspip : echec de la desinstallation de la table '$table' ",'associaspip');
		else {
			spip_log("Associaspip : echec de la desinstallation de la table '$table' ",'associaspip');
		}
	}

}

// fonction qui va remplir ou mettre a jour la table spip_asso_groupes
// pour y a definir les groupes gerant les autorisations (id<100)
function association_gestion_autorisations_upgrade() {
	// definir tous les groupes qui doivent exister
	$groupes_autorisations = array(1,2,3,20,21,30,31);

	// recuperer tous ceux existants
	$groupes_existants = array();
	$query = sql_select('id_groupe', 'spip_asso_groupes', 'id_groupe<100');
	while ($data = sql_fetch($query)) {
		$groupes_existants[$data['id_groupe']] = TRUE;
	}

	// inserer toutes les entrees necessitant une mise à jour
	$groupes_a_inserer = array();
	foreach ($groupes_autorisations as $id_groupe) {
		if (!$groupes_existants[$id_groupe]) { // il manque un entree
			$groupes_a_inserer[]=array('id_groupe'=>$id_groupe);
		}
	}

	if (count($groupes_a_inserer)) {
		sql_insertq_multi('spip_asso_groupes', $groupes_a_inserer);
	}
}

// MAJ des tables de la base SQL
// Retourne 0 si ok, le dernier numero de MAJ ok sinon
function association_upgrade($meta, $courante, $table='meta') {
	if (!isset($GLOBALS['association_metas']['base_version'])) { // Compatibilite : le nom de la meta donnant le numero de version n'etait pas std puis est parti dans une autre table puis encore une autre
		lire_metas('asso_metas');
		if (isset($GLOBALS['asso_metas']['base_version'])) {
			$n = $GLOBALS['asso_metas']['base_version'];
		} elseif (isset($GLOBALS['meta']['association_base_version'])) {
			$n = $GLOBALS['meta']['association_base_version'];
		} else
			$n = 0;
		$GLOBALS['association_metas']['base_version'] = $n;
	} else
		$n = $GLOBALS['association_metas']['base_version'];
	effacer_meta('association_base_version');
	spip_log("association upgrade: $table $meta = $n =>> $courante",'associaspip');
	if (!$n) {
		include_spip('base/create');
		alterer_base($GLOBALS['tables_principales'],
			     $GLOBALS['tables_auxiliaires']);
		sql_alter("TABLE spip_asso_groupes AUTO_INCREMENT = 100"); //!\ l'index de depart de l'autoincrement de la table doit etre a 100 car les premiers groupes sont reserves aux autorisations
		association_gestion_autorisations_upgrade();
		ecrire_meta($meta, $courante, NULL, $table);
		return 0; // Reussite (supposee !)
	} else { // compatibilite avec les numeros de version non entiers
		$installee = ($n>1) ? $n : ($n*100);
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante>$installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur'];
			if ($n) // signaler que les dernieres MAJ sont a refaire
				ecrire_meta($meta, $n-1, '', $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

// A chaque modif de la base SQL ou ses conventions (raccourcis etc)
// le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
// http://zone.spip.org/trac/spip-zone/timeline
// Ce numero est fourni automatiquement par la fonction spip_plugin_install
// lors de l'appel des fonctions de ce fichier.

$GLOBALS['association_maj'][21] = array(
	array('sql_alter', "TABLE spip_asso_membres ADD publication TEXT NOT NULL "),
);

$GLOBALS['association_maj'][30] = array(
	array('sql_drop_table', "spip_asso_bienfaiteurs"),
	array('sql_drop_table', "spip_asso_financiers"),
);

$GLOBALS['association_maj'][40] = array(
	array('sql_alter',"TABLE spip_asso_comptes ADD valide TEXT NOT NULL "),
);

$GLOBALS['association_maj'][50] = array(
	array('sql_alter',"TABLE spip_asso_activites ADD membres TEXT NOT NULL, ADD non_membres TEXT NOT NULL "),
);

$GLOBALS['association_maj'][60] = array(
	array('sql_drop_table', "spip_asso_profil"),
);

$GLOBALS['association_maj'][61] = array(
	array('spip_query',"RENAME TABLE spip_asso_banques TO spip_asso_plan"),
	array('sql_drop_table',"spip_asso_livres"),
);

$GLOBALS['association_maj'][62] = array(
	array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL "),
);

$GLOBALS['association_maj'][63] = array(
	array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT NOT NULL "),
);

function association_maj_64() {
	if (_ASSOCIATION_AUTEURS_ELARGIS=='spip_auteurs_elargis') {
		sql_alter("TABLE spip_auteurs_elargis ADD validite DATE NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant FLOAT NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD date DATE NOT NULL default '0000-00-00' ");
	} else {
		if (_ASSOCIATION_INSCRIPTION2) {
			if (!$GLOBALS['association_maj_erreur'])
				$GLOBALS['association_maj_erreur'] = 64;
			return;
		}
		// Simulation provisoire
		sql_alter("TABLE spip_asso_membres ADD commentaire TEXT NOT NULL default ''");
		sql_alter("TABLE spip_asso_membres ADD statut_interne TEXT NOT NULL default '' ");
		sql_alter("TABLE spip_asso_membres ADD nom_famille TEXT DEFAULT '' NOT NULL ");
		sql_update('spip_asso_membres', array('nom'=>'nom_famille'), "nom<>''" );
		sql_alter("TABLE spip_asso_membres DROP nom");
	}
}
$GLOBALS['association_maj'][64] = array(
	array('association_maj_64')
);

// Recopie des metas geree par CFG dans la table asso_meta
function association_maj_38192() {
	if (sql_create('spip_asso_metas',
		$GLOBALS['tables_auxiliaires']['spip_asso_metas']['field'], $GLOBALS['tables_auxiliaires']['spip_asso_metas']['key'], FALSE, FALSE)) {
		// Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus
		include _DIR_PLUGINS . 'cfg/inc/cfg.php';
		if (is_array($c = lire_config('association'))) {
			foreach($c as $k => $v) {
				ecrire_meta($k, $v, 'oui', 'association_metas');
			}
			// effacer les vieilles meta
			effacer_meta('association');
			effacer_meta('asso_base_version');
			effacer_meta('association_base_version');
		}
	} else
		spip_log("maj_38190: echec de  la creation de spip_asso_metas",'associaspip');
}
$GLOBALS['association_maj'][38192] = array(
	array('association_maj_38192')
);

$GLOBALS['association_maj'][38258] = array(
	array('sql_create','spip_asso_membres',
		$GLOBALS['tables_principales']['spip_asso_membres']['field'],
	    $GLOBALS['tables_principales']['spip_asso_membres']['key']
	)
);

$GLOBALS['association_maj'][38578] = array(
/* eviter les syntaxes proprietaires
#	array('spip_query', "RENAME table spip_asso_metas TO spip_association_metas"), // syntaxe DB2 et MySQL
#	array('spip_query', "SP_RENAME table spip_asso_metas , spip_association_metas"), // syntaxe SQL-Server / Transact-SQL
* au profit de la syntaxe commune, gage de portabilite
*/
	array('sql_alter', "TABLE spip_asso_metas RENAME TO spip_association_metas"), // syntaxe ANSI-SQL92 reconnue par MySQL Oracle PosgreSQL SQLite etc
);

$GLOBALS['association_maj'][42024] = array(
	array('sql_alter', "TABLE spip_asso_comptes ADD vu BOOLEAN default 0"),
	array('sql_update', 'spip_asso_comptes', array('vu' => 1), "valide='oui'"),
	array('sql_alter', "TABLE spip_asso_comptes DROP valide"),
);

// cette mise a jour comporte une erreur :
// sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'");
// le champ doit etre nomme direction et non destination
$GLOBALS['association_maj'][43909] = array(
	array('sql_alter', "TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'"),
	array('sql_create', 'spip_asso_destination', $GLOBALS['tables_principales']['spip_asso_destination']['field'], $GLOBALS['tables_principales']['spip_asso_destination']['key']),
	array('sql_create', 'spip_asso_destination_op', $GLOBALS['tables_principales']['spip_asso_destination_op']['field'], $GLOBALS['tables_principales']['spip_asso_destination_op']['key']),
);
unset($GLOBALS['association_maj'][43909]); // pour empecher l'execution de code fautif tout en gardant trace

// repare l'erreur commise sur la maj 43909
$GLOBALS['association_maj'][46392] = array(
	/* on elimine le champ mal nomme */
	array('sql_alter', "TABLE spip_asso_plan DROP destination"),
	/* et on refait la modif correctement: ca risque d'entrainer des erreurs SQL mais c'est pas grave */
	array('sql_alter', "TABLE spip_asso_plan ADD direction ENUM('credit','debit') NOT NULL default 'credit'"),
	array('sql_create', 'spip_asso_destination', $GLOBALS['tables_principales']['spip_asso_destination']['field'], $GLOBALS['tables_principales']['spip_asso_destination']['key']),
	array('sql_create', 'spip_asso_destination_op', $GLOBALS['tables_principales']['spip_asso_destination_op']['field'], $GLOBALS['tables_principales']['spip_asso_destination_op']['key']),
);

function association_maj_46779() {
	$rows = sql_select("id_plan, reference, commentaire", 'spip_asso_plan', "reference <> ''");
	while ($row = sql_fetch($rows)) { // avant d'eliminer reference de la table spip_asso_plan, on recopie sa valeur (si non vide) dans le champ commentaires
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['reference']:$row['reference'];
		sql_updateq('spip_asso_plan',
			array('commentaire' => $commentaire),
			"id_plan=".$row['id_plan']);
	}
	sql_alter("TABLE spip_asso_plan DROP reference");

	// modification du type de direction, on ajoute une troisieme valeur a l'enumeration, on renomme direction en type_op essentiellement
	// pour des raisons de compatibilite avec les differentes bases de donnees supportees par SPIP (impossible d'utiliser ALTER COLUMN ou MODIFY)
	sql_alter("TABLE spip_asso_plan ADD type_op ENUM('credit','debit','multi') NOT NULL default 'multi'");
	sql_update('spip_asso_plan', array('type_op' => 'direction'));
	sql_alter("TABLE spip_asso_plan DROP direction");

	// transforme actif en booleen plutot que texte oui/non, et renomme pour la meme raison en active
	sql_alter("TABLE spip_asso_plan ADD active BOOLEAN default 1");
	sql_update('spip_asso_plan', array('active' => 0), "actif='non'");
	sql_alter("TABLE spip_asso_plan DROP actif");

	// avant d'eliminer don de la table spip_asso_ventes, on recopie sa valeur(si non nul) dans le champ commentaires
	$rows = sql_select("id_vente, don, commentaire", 'spip_asso_ventes', "don <> ''");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['don']:$row['don'];
		sql_updateq('spip_asso_ventes',
			array('commentaire' => $commentaire),
			"id_vente=".$row['id_vente']);
	}
	sql_alter("TABLE spip_asso_ventes DROP don");

}
$GLOBALS['association_maj'][46779] = array(
	array('association_maj_46779')
);

// suppression id_asso de la table spip_asso_membres
function association_maj_47144() {
	$rows = sql_select("id_auteur, id_asso, commentaire", 'spip_asso_membres', "id_asso <> '' AND id_asso <> 0"); // avant d'eliminer id_asso de la table spip_asso_membres, on recopie sa valeur (si non vide et non egal a 0) dans le champ commentaires
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - Ref. Int. ".$row['id_asso']:"Ref. Int. ".$row['id_asso'];
		sql_updateq('spip_asso_membres',
			array('commentaire' => $commentaire),
			"id_auteur=".$row['id_auteur']);
	}
	sql_alter("TABLE spip_asso_membres DROP id_asso");
}
$GLOBALS['association_maj'][47144] = array(
	array('association_maj_47144')
);
unset($GLOBALS['association_maj'][47144]); // finalement on garde le champ id_asso, on n'effectue donc pas la maj_47144

// revert de la 47144 pour ceux qui l'aurait effectue avant qu'elle ne soit supprimee
function association_maj_47501() {
	// on verifie si le champ id_asso existe dans la table spip_asso_membre, si oui, rien a faire, la 47144 n'a pas ete effectuee
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table_membres = $trouver_table('spip_asso_membres');
	if (!$table_membres['field']['id_asso']) { // pas de champ id_asso, il faut le restaurer
		sql_alter("TABLE spip_asso_membres ADD id_asso TEXT NOT NULL ");

		$rows = sql_select("id_auteur, commentaire", 'spip_asso_membres', "commentaire LIKE '% - Ref. Int. %' OR commentaire LIKE 'Ref. Int. %'"); // on va voir dans commentaire si on trouve un champ qui ressemble a ce que la 47144 a sauvegarde
		while ($row = sql_fetch($rows)) { // restaurer le contenu du champ id_asso
			if (preg_match('/^(.*?)( - )?Ref\. Int\. (.*)$/', $row['commentaire'], $matches)) {
				$commentaire = $matches[1];
				$id_asso = $matches[3];
				sql_updateq('spip_asso_membres',
					array('commentaire' => $commentaire, 'id_asso' => $id_asso),
					"id_auteur=".$row['id_auteur']);
			}
		}
	}
}
$GLOBALS['association_maj'][47501] = array(
	array('association_maj_47501')
);

// eliminer le champ id_achat de la table ressources car il est inutile et non utilise : rien a sauvegarder
$GLOBALS['association_maj'][47731] = array(
	array('sql_alter', "TABLE spip_asso_ressources DROP id_achat"),
);

// mise a jour integrant l'utilisation du plugin Coordonnees
function association_maj_48001() {
	$effectuer_maj = FALSE;

	// cette partie du code s'execute au premier chargement, on n'a pas encore interroge l'utilisateur sur ce qu'il veut faire de ses donnees si il en a ou il n'a pas voulu faire la maj
	if (!_request('valider_association_maj_coordonnees')) {
		// on commence par verifier si des informations de la table spip_asso_membres sont potentiellement transferable vers les tables de coordonnees
		$adresse = sql_countsel('spip_asso_membres', "adresse <> '' OR code_postal <> '' OR ville <> ''");
		$telephone = sql_countsel('spip_asso_membres', "telephone <> '' OR mobile <> ''");

		if (! ($adresse OR $telephone)) { // si on n'a pas de donnees a sauvegarder, on fait la mise a jour sans poser de question
			$effectuer_maj = TRUE;
		} else { // on a des donnees, demander a l'utilisateur ce qu'il veut en faire
			echo '<form method="post" action="">';
			echo '<fieldset><p>'._T('asso:maj_coordonnees_intro').'</p>';
			// on commence par determiner si le plugin Coordonnees est installe
			include_spip('inc/plugin');
			$liste_plugins = liste_plugin_actifs();
			$plugin_coordonnees_actif = isset($liste_plugins['COORDONNEES']);
			if (!$plugin_coordonnees_actif) {// Le plugin coordonnees n'est pas actif
				echo '<p>'._T('asso:maj_coordonnees_plugin_inactif').'</p>';
			} else { // le plugin coordonnees est actif
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="ignorer">'._T('asso:maj_coordonnees_ignorer').'</input><br/>';
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="merge" checked="checked">'._T('asso:maj_coordonnees_merge').'</input>';
				echo "\n<input type='hidden' name='association_maj_adresses' value='$adresse' />";
				echo "\n<input type='hidden' name='association_maj_telephones' value='$telephone' />";
			}
			echo '<p><input type="submit" name="valider_association_maj_coordonnees" value="'._T('asso:effectuer_la_maj').'"/></p>';
			echo '<p>'._T('asso:maj_coordonnees_notes').'</p></fieldset>';
			echo '</form>';
		}
	} else { // l'utilisateur veut effectuer la maj, on controle si il y a des precision quand a l'ecrasement de donnees existentes
			$choix_donnees = _request('association_maj_coordonnees_traitement_data');
			if ($choix_donnees=='merge') { // on integre les donnees d'association dans Coordonnees
				include_spip('action/editer_numero');
				include_spip('action/editer_adresse');
				include_spip('inc/modifier');

				// pre-remplissage pour les fonctions insert_numero et insert_adresse de Coordonnees
				$liens = array('objet' => 'auteur');
				$telephone = array('titre' => 'telephone');
				$mobile = array('titre' => 'mobile');
				$spip_table_numero = table_objet_sql('numero');
				$id_table_numero = id_table_objet('numero');
				$spip_table_adresse = table_objet_sql('adresse');
				$id_table_adresse = id_table_objet('adresse');

				// On recupere les coordonnees utiles
				$coordonnees_membres = sql_select('id_auteur, adresse AS voie, code_postal, ville, telephone, mobile', 'spip_asso_membres', "adresse <> '' OR mobile <> '' OR code_postal <> '' OR ville <> '' OR telephone <> ''");
				while ($data = sql_fetch($coordonnees_membres)) {
					$liens['id_objet'] = $data['id_auteur'];
					unset($data['id_auteur']);
					if ($telephone['numero'] = $data['telephone']) { // si on a un numero de telephone
						if ($id_numero =  insert_numero($liens)) {
							sql_updateq($spip_table_numero, $telephone, "$id_table_numero=$id_numero");
						}
					}
					unset($data['telephone']);
					if ($mobile['numero'] = $data['mobile']) { // si on a un numero de moblie
						if ($id_numero = insert_numero($liens)) {
							sql_updateq($spip_table_numero, $mobile, "$id_table_numero=$id_numero");
						}
					}
					unset($data['mobile']);
					if ($data['voie'] OR $data['code_postal'] OR $data['ville']) { // si on a une adresse, meme partielle
						if ($id_adresse = insert_adresse($liens)) {
							sql_updateq($spip_table_adresse, $data, "$id_table_adresse=$id_adresse");
						}
					}
				}
				echo "\n<fieldset>", intval(_request('association_maj_adresses')), _T('asso:maj_coordonnees_adresses_inserees'),
				  '<br/>', intval(_request('association_maj_telephones')), _T('asso:maj_coordonnees_numeros_inseres'), "\n</fieldset>";
			}

			$effectuer_maj = TRUE;
	}

	// on effectue si besoin la mise a jour
	if ($effectuer_maj) { // on supprime les champs de la table spip_asso_membres, ils ont deja ete sauvegarde dans les tables de Coordonnees si besoin
		sql_alter("TABLE spip_asso_membres DROP telephone");
		sql_alter("TABLE spip_asso_membres DROP mobile");
		sql_alter("TABLE spip_asso_membres DROP adresse");
		sql_alter("TABLE spip_asso_membres DROP code_postal");
		sql_alter("TABLE spip_asso_membres DROP ville");
		sql_alter("TABLE spip_asso_membres DROP email");
	} else { // la mise a jour n'est pas effectuee : on le signale dans les maj_erreur pour y revenir au prochain chargement de la page de gestion des plugins
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 48001;
	}
}
$GLOBALS['association_maj'][48001] = array(
	array('association_maj_48001')
);

// passer l'horodatage des modifications de ligne en TIMESTAMP automatique
$GLOBALS['association_maj'][48225] = array(
	array ('sql_alter', "TABLE spip_asso_categories CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_plan CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_prets CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_activites CHANGE maj maj TIMESTAMP"),
);

// cette mise a jour introduit un controle sur l'activation des modules de gestions des dons,
// ventes, prets, activites subordonnes a l'activation de la gestion comptable.
// la fonction de mise a jour desactive donc d'eventuels modules actifs si la gestion
// comptable n'est pas activee
function association_maj_48466() {
	include_spip('inc/association_comptabilite');
	/* on verifie la validite du plan comptable existant */
	if ($GLOBALS['association_metas']['comptes'] && !association_valider_plan_comptable()) {
		ecrire_meta('comptes', '', 'oui', 'association_metas');
		echo '<p>'._T('asso:maj_desactive_gestion_comptable').'</p>';
	}

	$desactivation = FALSE;
	if (!$GLOBALS['association_metas']['comptes']) {
		if ($GLOBALS['association_metas']['dons']) { ecrire_meta('dons', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['ventes']) { ecrire_meta('ventes', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['prets']) { ecrire_meta('prets', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['activites']) { ecrire_meta('activites', '', 'oui', 'association_metas'); $desactivation = TRUE; }
	}

	// si on a desactive des modules, on le signale par un message
	if ($desactivation) echo '<p>'._T('asso:maj_desactive_modules').'</p>';

	// on en profite pour effacer des metas qui ne servent plus
	effacer_meta('comptes_stricts', 'association_metas');
	effacer_meta('indexation', 'association_metas');
}
$GLOBALS['association_maj'][48466] = array(
	array('association_maj_48466')
);

// ajout de la date d'adhesion du membre
$GLOBALS['association_maj'][51602] = array(
	array('sql_alter', "TABLE spip_asso_membres ADD date_adhesion DATE "),
);
unset($GLOBALS['association_maj'][51682]); // finalement non...

// Ces champs de configuration n'etant plus geres par defaut,
// les passer en personalises pour ceux qui les utilisent
$GLOBALS['association_maj'][52476] = array(
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_siret'" ), "nom='siret' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='siret' AND valeur=''" ),
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_tva'" ), "nom='tva' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='tva' AND valeur=''" ),
);

// mise a jour introduisant les groupes
function association_maj_53901() {
	sql_create('spip_asso_groupes',
		$GLOBALS['tables_principales']['spip_asso_groupes']['field'],
		$GLOBALS['tables_principales']['spip_asso_groupes']['key']);
	sql_alter("TABLE spip_asso_groupes AUTO_INCREMENT = 100");
	sql_create('spip_asso_groupes_liaisons',
		$GLOBALS['tables_principales']['spip_asso_groupes_liaisons']['field'],
		$GLOBALS['tables_principales']['spip_asso_groupes_liaisons']['key']);

	/* si on a des membres avec une fonction defini, on recupere tout et on les mets dans un groupe appele bureau */
	$liste_membres_bureau = sql_select("id_auteur, fonction" ,"spip_asso_membres", "fonction <> ''"); // si on a des membres avec une fonction definie, on recupere tout...
	if (sql_count($liste_membres_bureau )) { // ...et on les met dans un groupe appele "Bureau"
		$id_groupe = sql_insertq("spip_asso_groupes", array('nom' => 'Bureau', 'affichage' => '1')); // on cree un groupe "Bureau"...
		while ($membre_bureau = sql_fetch($liste_membres_bureau)) { // ...et on y insere tous les membres qui avaient une fonction
			sql_insertq("spip_asso_groupes_liaisons", array(
				'id_groupe' => $id_groupe,
				'id_auteur' => $membre_bureau['id_auteur'],
				'fonction'  => $membre_bureau['fonction'],
			));
		}
	}

	sql_alter("TABLE spip_asso_membres DROP fonction"); // on supprime le champ fonction de la table spip_asso_membres car cela est maintenat gere dans spip_asso_groupes_liaisons
}
$GLOBALS['association_maj'][53901] = array(
	array('association_maj_53901')
);

// Creation de la table 'exercices' permettant de gerer la comptabilite en exercice comptable
// sur une 'annee civile', une 'annee scolaire', ou sur des periodes donnees
$GLOBALS['association_maj'][55177] = array(
	array('sql_create','spip_asso_exercices',
	$GLOBALS['tables_principales']['spip_asso_exercices']['field'],
	$GLOBALS['tables_principales']['spip_asso_exercices']['key']),
);

// Changer les champs FLOAT (ou parfois TEXT...) en DECIMAL
// correction de r57429 (etourderie: 2 decimales et non 4), et rajout de deux champs confirmes (spip_asso_dons)
$GLOBALS['association_maj'][57896] = array(
	array ('sql_alter', "TABLE spip_asso_categories CHANGE cotisation cotisation DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE prix_vente prix_vente DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE frais_envoi frais_envoi DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE recette recette DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE depense depense DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_plan CHANGE solde_anterieur solde_anterieur DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE recette recette DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE depense depense DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE pu pu DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_activites CHANGE montant montant DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE argent argent DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE valeur valeur DECIMAL(19,2) NOT NULL"),
);

// Revue de la gestion des ressources et prets (debut)
$GLOBALS['association_maj'][58825] = array(
	array ('sql_alter', "TABLE spip_asso_prets DROP statut"), // ce champ ne sert pas, donc...
	array ('sql_alter', "TABLE spip_asso_prets CHANGE date_sortie date_sortie DATETIME NOT NULL"), // permettre une gestion plus fine (duree inferieure a la journee)
	array ('sql_alter', "TABLE spip_asso_prets CHANGE date_retour date_retour DATETIME NOT NULL"), // permettre une gestion plus fine (duree inferieure a la journee)
	array('sql_update', 'spip_asso_ressources', array('statut' => 1), "statut='ok'"), // nouveau statut numerique gerant simultanement les quantites
	array('sql_update', 'spip_asso_ressources', array('statut' => 0), "statut='reserve'"), // nouveau statut numerique gerant simultanement les quantites
	array('sql_update', 'spip_asso_ressources', array('statut' => -1), "statut='suspendu'"), // nouveau statut numerique gerant simultanement les quantites
/* Ne pas convertir le champ si on a des statuts personnalises... le code prevoit la compatibilite ascendante (sauf ajout de fonctionnalite incompatible) */
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE statut statut TINYTEXT NULL"), // changement temporaire pour rendre le champ nullable
	array('sql_update', 'spip_asso_ressources', array('statut' => NULL), "statut='sorti'"), // nouveau statut numerique gerant simultanement les quantites
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE statut statut TINYINT NULL DEFAULT 1"), // nouvelle gestion numerique
);

// Revue de la gestion des ressources et prets (suite)
$GLOBALS['association_maj'][58824] = array(
	array ('sql_alter', "TABLE spip_asso_prets ADD prix_unitaire DECIMAL(19,2) NOT NULL DEFAULT 0 "), // comme pour les ventes (asso_ventes.prix_vente) et les activites (asso_activites.montant) on garde le cout de base facture car celui-ci (asso_ressources.pu) peut changer par la suite
);

// Revue de la gestion des ressources et prets (fin)
$GLOBALS['association_maj'][58825] = array(
// on reprend ici les requetes erronnees de maj-57780 ("bienfaiteur" y est malencontreusement+logiquement nomme "donateur")
	/* En liant le nom du bienfaiteur avec l'ID membre avant d'enregistrer, il faut penser a defaire cela a chaque edition pour eviter de se retrouver avec [un nom->membreXX] qui devient [[un nom->mebreXX]->membreXX] au moment de reediter. Il semble plus simple de ne pas transformer la saisie a stocker mais seulement l'affichage avec la nouvelle fonction association_formater_idnom($id,$nom) Du coup il faut quand meme retablir les champs pour ne pas reproduire a l'affichage le souci qu'on avait a l'edition... */
	array('sql_update', 'spip_asso_dons', array('bienfaiteur' => "SUBSTR(bienfaiteur,2, INSTR(bienfaiteur,'->membre')-1)"), "bienfaiteur LIKE '[%->membre%]'"), // SUBSTR est compris par la plupart meme s'il y a d'autres appelations comme SUBSTRING (SQL Server et mySQL). INSTR (pour lequel Oracle accepte deux parametres optionnels de plus que mySQL) ou POSITION ou PARTINDEX ou CHARINDEX ou LOCATE ... pfff. peut-etre vaut-il mieux le faire en PHP pour etre certain d'etre independant de l'implementation SQL ?!? ou tenter l'approche par REPLACE("dans quelle chaine","sous-chaine a trouver","sous-chaine de remplacement") qui est commun a beaucoup de SGBD_SQL (mais pas dans la norme de 92 non plus si j'ai bonne memoire) ? faut voir...
// on reprend ici les requetes erronnees de maj-58798
	array ('sql_alter', "TABLE spip_asso_ressources ADD ud CHAR(1) NOT NULL DEFAULT 'D' "), // unite des durees de location
// on reprend ici les requetes erronnees de maj-58824
	array ('sql_alter', "TABLE spip_asso_ressources ADD prix_acquisition DECIMAL(19,2) NOT NULL DEFAULT 0 "), // garder trace du cout d'acquisition pour mieux evaluer l'amortissement et la rentabilite
	array('sql_update', 'spip_asso_prets AS a_p INNER JOIN spip_asso_ressources AS a_r ON a_p.id_ressource=a_r.id_ressource', array('prix_unitaire'=>'pu' ), "prix_unitaire=0"), // mettre a jour avec les tarifs actuels...
);

$GLOBALS['association_maj'][58894] = array(
// renommer le champ "date" en "date_inscription" qui est plus parlant et n'est pas un mot reserve
	array('sql_alter', "TABLE spip_asso_activites ADD date_inscription DATE NOT NULL DEFAULT '0000-00-00' "),
	array('sql_update', 'spip_asso_activites', array('date'=>'date_inscription') ),
	array('sql_alter', "TABLE spip_asso_activites DROP date "),
);

// introduction des groupes dans la table spip_asso_groupes
$GLOBALS['association_maj'][59886] = array(
	array('association_gestion_autorisations_upgrade')
);

// Correction de l'erreur aux niveau de l'auto-increment des id_groupe presente pour les nouvelles installations effectuees entre la r53901  et r60035
function association_maj_60038() {
	sql_alter("TABLE spip_asso_groupes AUTO_INCREMENT = 100"); // reset de l'auto-increment meme s'il y a deja des groupes d'ID >100 car ca ne pose pas de probleme

	$query = sql_select('id_groupe', 'spip_asso_groupes', "id_groupe<100 AND nom<>''", '', 'id_groupe'); // on verifie qu'on a des groupes d'ID <100 avec un nom <>''
	if (sql_count($query)) {
		$max_id = sql_getfetsel('MAX(id_groupe)', 'spip_asso_groupes'); // on recupere l'ID du dernier groupe cree
		$max_id = ($max_id<100)?100:$max_id;
		while ($data=sql_fetch($query)) {
			sql_updateq('spip_asso_groupes', array('id_groupe'=>($data['id_groupe']+$max_id)), 'id_groupe='.$data['id_groupe']); // on ajoute $max_id_ a l'ID des groupes selectionnes
			sql_updateq('spip_asso_groupes_liaisons', array('id_groupe'=>($data['id_groupe']+$max_id)), 'id_groupe='.$data['id_groupe']); // on fait pareul dans la table des liaisons
		}
	}

	// on verifie qu'il ne faille pas creer des groupes pour les autorisations apres avoir bouge ceux existants
	association_gestion_autorisations_upgrade();
}
$GLOBALS['association_maj'][60038] = array(
	array('association_maj_60038')
);

// ajout de la caution a la gestion des ressources
$GLOBALS['association_maj'][62712] = array(
	array('sql_alter', "TABLE spip_asso_ressources ADD prix_caution DECIMAL(19,2) NOT NULL DEFAULT 0 "),
	array('sql_alter', "TABLE spip_asso_prets ADD prix_caution DECIMAL(19,2) NOT NULL DEFAULT 0 "),
	array('sql_alter', "TABLE spip_asso_prets ADD date_caution1 DATE NOT NULL DEFAULT '0000-00-00' "),
	array('sql_alter', "TABLE spip_asso_prets ADD date_caution0 DATE NOT NULL DEFAULT '0000-00-00' "),
	array('sql_alter', "TABLE spip_asso_prets ADD date_reservation DATETIME DEFAULT NULL "),
);

// ajout de nouvelles autorisations
$GLOBALS['association_maj'][66289] = array(
	array('sql_insertq_multi', 'spip_asso_groupes', array( array('id_groupe'=>10), array('id_groupe'=>32), array('id_groupe'=>33), ), ),
);

// normalisation de la base
$GLOBALS['association_maj'][66345] = array(
// renommer le champ "commentaires" en "commentaire" pour homogeniser les traitements et l'affichage
	array('sql_alter', "TABLE spip_asso_categories ADD commentaire TEXT NOT NULL"),
	array('sql_update', 'spip_asso_categories', array('commentaire'=>'commentaires') ),
	array('sql_alter', "TABLE spip_asso_categories DROP commentaires"),
	array('sql_alter', "TABLE spip_asso_groupes ADD commentaire TEXT NOT NULL"),
	array('sql_update', 'spip_asso_groupes', array('commentaire'=>'commentaires') ),
	array('sql_alter', "TABLE spip_asso_groupes DROP commentaires"),
// homogeniser le nommage de cle secondaire sur les membres/auteurs pour ne plus s'arracher les cheveux
	array('sql_alter', "TABLE spip_asso_dons ADD id_auteur BIGINT UNSIGNED NOT NULL"),
	array('sql_update', 'spip_asso_dons', array('id_auteur'=>'id_adherent') ),
	array('sql_alter', "TABLE spip_asso_dons DROP id_adherent"),
	array('sql_alter', "TABLE spip_asso_activites ADD id_auteur BIGINT UNSIGNED NOT NULL"),
	array('sql_update', 'spip_asso_activites', array('id_auteur'=>'id_adherent') ),
	array('sql_alter', "TABLE spip_asso_activites DROP id_adherent"),
	array('sql_alter', "TABLE spip_asso_prets ADD id_auteur BIGINT UNSIGNED NOT NULL"),
	array('sql_update', 'spip_asso_prets', array('id_emprunteur'=>'id_auteur') ),
	array('sql_alter', "TABLE spip_asso_dons DROP id_emprunteur"),
);

?>