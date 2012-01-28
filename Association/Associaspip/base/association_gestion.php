<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/association');
include_spip('base/abstract_sql');

// A chaque modif de la base SQL ou ses conventions (raccourcis etc)
// le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
// http://zone.spip.org/trac/spip-zone/timeline
// Ce numero est fourni automatiquement par la fonction spip_plugin_install
// lors de l'appel des fonctions de ce fichier.

// desinstatllation
function association_vider_tables($nom_meta, $table)
{
	$tables_a_supprimer=array(
		'spip_asso_activites',
		'spip_asso_categories',
		'spip_asso_comptes',
		'spip_asso_destination',
		'spip_asso_destination_op',
		'spip_asso_dons',
		'spip_asso_exercices',
		'spip_asso_plan',
		'spip_asso_prets',
		'spip_asso_ressources',
		'spip_asso_ventes',
		'spip_association_metas',
		'spip_asso_groupes',
		'spip_asso_groupes_liaisons',
		'spip_asso_membres'
	);
	foreach($tables_a_supprimer as $table)
		{
		sql_drop_table($table);
		spip_log("$table $nom_meta desinstalle");
		}
	effacer_meta($nom_meta_base_version);
	spip_log("$table $nom_meta desinstalle");
}

// MAJ des tables de la base SQL
// Retourne 0 si ok, le dernier numero de MAJ ok sinon
function association_upgrade($meta, $courante, $table='meta')
{
	// Compatibilite: le nom de la meta donnant le numero de version
	// n'etait pas std puis est parti dans une autre table puis encore une autre
	if (!isset($GLOBALS['association_metas']['base_version'])) {
		lire_metas('asso_metas');
		if (isset($GLOBALS['asso_metas']['base_version'])) {
			$n = $GLOBALS['asso_metas']['base_version'];
		} elseif (isset($GLOBALS['meta']['association_base_version'])) {
			$n = $GLOBALS['meta']['association_base_version'];
		} else $n = 0;
		$GLOBALS['association_metas']['base_version'] = $n;
	} else $n = $GLOBALS['association_metas']['base_version'];
	effacer_meta('association_base_version');
	spip_log("association upgrade: $table $meta = $n =>> $courante");
	if (!$n) {
		include_spip('base/create');
		alterer_base($GLOBALS['tables_principales'],
			     $GLOBALS['tables_auxiliaires']);
		ecrire_meta($meta, $courante, NULL, $table);
		return 0; // Reussite (supposee !)
	} else {
	// compatibilite avec les numeros de version non entiers
		$installee = ($n>1) ? $n : ($n*100);
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante>$installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur'];
			// signaler que les dernieres MAJ sont a refaire
			if ($n) ecrire_meta($meta, $n-1, '', $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

$GLOBALS['association_maj'][21] = array(array('sql_alter',"TABLE spip_asso_membres ADD publication text NOT NULL AFTER secteur"));

$GLOBALS['association_maj'][30] = array(
	array('sql_drop_table', "spip_asso_bienfaiteurs"),
	array('sql_drop_table', "spip_asso_financiers")
);

$GLOBALS['association_maj'][40] = array(
	array('sql_alter',"TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL AFTER `id_journal` ")
);

$GLOBALS['association_maj'][50] = array(
	array('sql_alter',"TABLE spip_asso_activites ADD membres TEXT NOT NULL AFTER accompagne, ADD non_membres TEXT NOT NULL AFTER membres ")
);

$GLOBALS['association_maj'][60] = array(
	array('sql_drop_table', "spip_asso_profil")
);

$GLOBALS['association_maj'][61] = array(
	array('spip_query',"RENAME TABLE spip_asso_banques TO spip_asso_plan"),
	array('sql_drop_table',"spip_asso_livres")
);

$GLOBALS['association_maj'][62] = array(
	array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL AFTER commentaires")
);

$GLOBALS['association_maj'][63] = array(
	array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT(20) NOT NULL AFTER acheteur")
);

function association_maj_64()
{
	if (_ASSOCIATION_AUTEURS_ELARGIS=='spip_auteurs_elargis') {
		sql_alter("TABLE spip_auteurs_elargis ADD validite date NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant float NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD date date NOT NULL default '0000-00-00' ");
	} else {
		if (_ASSOCIATION_INSCRIPTION2) {
			if (!$GLOBALS['association_maj_erreur']) $GLOBALS['association_maj_erreur'] = 64;
			return;
		}
		// Simulation provisoire
		@sql_alter("TABLE spip_asso_membres ADD commentaire text NOT NULL default ''");
		@sql_alter("TABLE spip_asso_membres ADD statut_interne text NOT NULL default '' ");
		@sql_alter("TABLE spip_asso_membres CHANGE COLUMN nom nom_famille text DEFAULT '' NOT NULL");
	}
}
$GLOBALS['association_maj'][64] = array(
	array('association_maj_64')
);

// Recopie des metas geree par CFG dans la table asso_meta
// Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus
function association_maj_38192()
{
	if (sql_create('spip_asso_metas',
		$GLOBALS['tables_auxiliaires']['spip_asso_metas']['field'],
		$GLOBALS['tables_auxiliaires']['spip_asso_metas']['key'],
		false, false)) {
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
	} else spip_log("maj_38190: echec de  la creation de spip_asso_metas");
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
	array('spip_query', 'rename table spip_asso_metas TO spip_association_metas')
);

function association_maj_42024()
{
	sql_alter("TABLE spip_asso_comptes ADD vu BOOLEAN default 0");
	sql_update('spip_asso_comptes', array('vu' => 1), "valide='oui'");
	sql_alter("TABLE spip_asso_comptes DROP valide");
}
$GLOBALS['association_maj'][42024] = array(
	array('association_maj_42024')
);

/* cette mise a jour comporte une erreur: sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'"); le champ doit etre nomme direction et non destination */
function association_maj_43909()
{
	sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'");
	sql_create('spip_asso_destination',
		$GLOBALS['tables_principales']['spip_asso_destination']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination']['key']);
	sql_create('spip_asso_destination_op',
		$GLOBALS['tables_principales']['spip_asso_destination_op']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination_op']['key']);
}
$GLOBALS['association_maj'][43909] = array(
	array('association_maj_43909')
);
unset($GLOBALS['association_maj'][43909]); /* pour empecher l'execution de code fautif tout en gardant trace */

/* repare l'erreur commise sur la maj 43909 */
function association_maj_46392()
{
	/* on elimine le champ mal nomme */
	sql_alter("TABLE spip_asso_plan DROP destination");
	/* et on refait la modif correctement: ca risque d'entrainer des erreurs SQL mais c'est pas grave */
	sql_alter("TABLE spip_asso_plan ADD direction ENUM('credit','debit') NOT NULL default 'credit'");
	sql_create('spip_asso_destination',
		$GLOBALS['tables_principales']['spip_asso_destination']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination']['key']);
	sql_create('spip_asso_destination_op',
		$GLOBALS['tables_principales']['spip_asso_destination_op']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination_op']['key']);
}
$GLOBALS['association_maj'][46392] = array(
	array('association_maj_46392')
);

function association_maj_46779()
{
	/* avant d'eliminer reference de la table spip_asso_plan, on recopie sa valeur(si non null) dans le champ commentaires */
	$rows = sql_select("id_plan, reference, commentaire", 'spip_asso_plan', "reference <> ''");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['reference']:$row['reference'];
		sql_updateq('spip_asso_plan',
			array('commentaire' => $commentaire),
			"id_plan=".$row['id_plan']);
	}
	sql_alter("TABLE spip_asso_plan DROP reference");

	/* modification du type de direction, on ajoute une troisieme valeur a l'enumeration, on renomme direction en type_op essentiellement
	pour des raisons de compatibilite avec les differentes bases de donnees supportees par SPIP (impossible d'utiliser ALTER COLUMN ou MODIFY)*/
	sql_alter("TABLE spip_asso_plan ADD type_op ENUM('credit','debit','multi') NOT NULL default 'multi'");
	sql_update('spip_asso_plan', array('type_op' => 'direction'));
	sql_alter("TABLE spip_asso_plan DROP direction");

	/* transforme actif en booleen plutot que texte oui/non, et renomme pour la meme raison en active */
	sql_alter("TABLE spip_asso_plan ADD active BOOLEAN default 1");
	sql_update('spip_asso_plan', array('active' => 0), "actif='non'");
	sql_alter("TABLE spip_asso_plan DROP actif");

	/* avant d'eliminer don de la table spip_asso_ventes, on recopie sa valeur(si non null) dans le champ commentaires */
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

function association_maj_47144()
{
	/* avant d'eliminer id_asso de la table spip_asso_membres, on recopie sa valeur(si non null et non egal a 0) dans le champ commentaires */
	$rows = sql_select("id_auteur, id_asso, commentaire", 'spip_asso_membres', "id_asso <> '' AND id_asso <> 0");
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
unset($GLOBALS['association_maj'][47144]); /* finalement on garde le champ id_asso, on n'effectue donc pas la maj_47144 */

/* revert de la 47144 pour ceux qui l'aurait effectue avant qu'elle ne soit supprimee */
function association_maj_47501()
{
	/* on verifie si le champ id_asso existe dans la table spip_asso_membre, si oui, rien a faire, la 47144 n'a pas ete effectuee */
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table_membres = $trouver_table('spip_asso_membres');
	if (!$table_membres['field']['id_asso']) { /* pas de champ id_asso, il faut le restaurer */
		sql_alter("TABLE spip_asso_membres ADD id_asso TEXT NOT NULL AFTER id_auteur");

		/* on va voir dans commentaire si on trouve un champ qui ressemble a ce que la 47144 a sauvegarde */
		$rows = sql_select("id_auteur, commentaire", 'spip_asso_membres', "commentaire LIKE '% - Ref. Int. %' OR commentaire LIKE 'Ref. Int. %'");
		while ($row = sql_fetch($rows)) {
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
$GLOBALS['association_maj'][47501] = array(array('association_maj_47501'));

/* eliminer le champ id_achat de la table ressources car il est inutile et non utilise, rien a sauvegarder */
function association_maj_47731()
{
	sql_alter("TABLE spip_asso_ressources DROP id_achat");
}
$GLOBALS['association_maj'][47731] = array(
	array('association_maj_47731')
);

/* mise a jour integrant l'utilisation du plugin Coordonnees */
function association_maj_48001()
{
	$effectuer_maj = false;

	/* cette partie du code s'execute au premier chargement, on n'a pas encore interroge l'utilisateur sur ce qu'il veut faire de ses donnees si il en a  ou il n'a pas voulu faire la maj */
	if (!_request('valider_association_maj_coordonnees')) {
		/* on commence par verifier si des informations de la table spip_asso_membres sont potentiellement transferable vers les tables de coordonnees */
		$adresse = sql_countsel('spip_asso_membres', "adresse <> '' OR code_postal <> '' OR ville <> ''");
		$telephone = sql_countsel('spip_asso_membres', "telephone <> '' OR mobile <> ''");

		/* si on n'a pas de donnees a sauvegarder, on fait la mise a jour sans poser de question */
		if (! ($adresse OR $telephone)) {
			$effectuer_maj = true;
		} else { /* on a des donnees, demander a l'utilisateur ce qu'il veut en faire */
			echo '<form method="post" action="">';
			echo '<fieldset><p>'._T('asso:maj_coordonnees_intro').'</p>';
			/* on commence par determiner si le plugin Coordonnees est installe */
			include_spip('inc/plugin');
			$liste_plugins = liste_plugin_actifs();
			$plugin_coordonnees_actif = isset($liste_plugins['COORDONNEES']);

			if (!$plugin_coordonnees_actif) {/* Le plugin coordonnees n'est pas actif */
				echo '<p>'._T('asso:maj_coordonnees_plugin_inactif').'</p>';
			} else { /* le plugin coordonnees est actif */
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="ignorer">'._T('asso:maj_coordonnees_ignorer').'</input><br/>';
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="merge" checked="checked">'._T('asso:maj_coordonnees_merge').'</input>';
				echo "\n<input type='hidden' name='association_maj_adresses' value='$adresse' />";
				echo "\n<input type='hidden' name='association_maj_telephones' value='$telephone' />";
			}
			echo '<p><input type="submit" name="valider_association_maj_coordonnees" value="'._T('asso:effectuer_la_maj').'"/></p>';
			echo '<p>'._T('asso:maj_coordonnees_notes').'</p></fieldset>';
			echo '</form>';
		}
	} else { /* l'utilisateur veut effectuer la maj, on controle si il y a des precision quand a l'ecrasement de donnees existentes */
			$choix_donnees = _request('association_maj_coordonnees_traitement_data');
			if ($choix_donnees=='merge') { /* on integre les donnees d'association dans Coordonnees */
				include_spip('action/editer_numero');
				include_spip('action/editer_adresse');
				include_spip('inc/modifier');

				/* pre-remplissage pour les fonctions insert_numero et insert_adresse de Coordonnees */
				$liens = array('objet' => 'auteur');
				$telephone = array('titre' => 'telephone');
				$mobile = array('titre' => 'mobile');

				$spip_table_numero = table_objet_sql('numero');
				$id_table_numero = id_table_objet('numero');

				$spip_table_adresse = table_objet_sql('adresse');
				$id_table_adresse = id_table_objet('adresse');

				/* On recupere les coordonnees utiles */
				$coordonnees_membres = sql_select('id_auteur, adresse AS voie, code_postal, ville, telephone, mobile', 'spip_asso_membres', "adresse <> '' OR mobile <> '' OR code_postal <> '' OR ville <> '' OR telephone <> ''");
				while ($data = sql_fetch($coordonnees_membres)) {
					$liens['id_objet'] = $data['id_auteur'];
					unset($data['id_auteur']);

					/* si on a un numero de telephone */
					if ($telephone['numero'] = $data['telephone']) {
						if ($id_numero =  insert_numero($liens)) {
							sql_updateq($spip_table_numero, $telephone, "$id_table_numero=$id_numero");
						}
					}
					unset($data['telephone']);

					/* si on a un numero de mobile */
					if ($mobile['numero'] = $data['mobile']) {
						if ($id_numero = insert_numero($liens)) {
							sql_updateq($spip_table_numero, $mobile, "$id_table_numero=$id_numero");
						}
					}
					unset($data['mobile']);

					/* si on a une adresse, meme partielle */
					if ($data['voie'] OR $data['code_postal'] OR $data['ville']) {
						if ($id_adresse = insert_adresse($liens)) {
							sql_updateq($spip_table_adresse, $data, "$id_table_adresse=$id_adresse");
						}
					}
				}
				echo "\n<fieldset>", intval(_request('association_maj_adresses')), _T('asso:maj_coordonnees_adresses_inserees'),
				  '<br/>', intval(_request('association_maj_telephones')), _T('asso:maj_coordonnees_numeros_inseres'), "\n</fieldset>";
			}

			$effectuer_maj = true;
	}

	/* on effectue si besoin la mise a jour */
	if ($effectuer_maj) {
		/* on supprime les champs de la table spip_asso_membres, ils ont deja ete sauvegarde dans les tables de Coordonnees si besoin */
		sql_alter("TABLE spip_asso_membres DROP telephone");
		sql_alter("TABLE spip_asso_membres DROP mobile");
		sql_alter("TABLE spip_asso_membres DROP adresse");
		sql_alter("TABLE spip_asso_membres DROP code_postal");
		sql_alter("TABLE spip_asso_membres DROP ville");
		sql_alter("TABLE spip_asso_membres DROP email");
	} else { /* la mise a jour n'est pas effectuee : on le signale dans les maj_erreur pour y revenir au prochain chargement de la page de gestion des plugins */
		if (!$GLOBALS['association_maj_erreur']) $GLOBALS['association_maj_erreur'] = 48001;
	}
}
$GLOBALS['association_maj'][48001] = array(
	array('association_maj_48001')
);

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

/* cette mise a jour introduit un controle sur l'activation des modules de gestions des dons, */
/* ventes, prets, activit�s subordonnes a l'activation de la gestion comptable.               */
/* la fonction de mise a jour desactive donc d'eventuels modules actives si la gestion        */
/* comptable n'est pas activee                                                               */
function association_maj_48466()
{
	include_spip('inc/association_comptabilite');
	/* on verifie la validite du plan comptable existant */
	if ($GLOBALS['association_metas']['comptes'] && !association_valider_plan_comptable()) {
		ecrire_meta('comptes', '', 'oui', 'association_metas');
		echo '<p>'._T('asso:maj_desactive_gestion_comptable').'</p>';
	}

	$desactivation = false;
	if (!$GLOBALS['association_metas']['comptes']) {
		if ($GLOBALS['association_metas']['dons']) { ecrire_meta('dons', '', 'oui', 'association_metas'); $desactivation = true; }
		if ($GLOBALS['association_metas']['ventes']) { ecrire_meta('ventes', '', 'oui', 'association_metas'); $desactivation = true; }
		if ($GLOBALS['association_metas']['prets']) { ecrire_meta('prets', '', 'oui', 'association_metas'); $desactivation = true; }
		if ($GLOBALS['association_metas']['activites']) { ecrire_meta('activites', '', 'oui', 'association_metas'); $desactivation = true; }
	}

	/* si on a desactive des modules, on le signale par un message */
	if ($desactivation) echo '<p>'._T('asso:maj_desactive_modules').'</p>';

	/* on en profite pour effacer des metas qui ne servent plus */
	effacer_meta('comptes_stricts', 'association_metas');
	effacer_meta('indexation', 'association_metas');
}
$GLOBALS['association_maj'][48466] = array(
	array('association_maj_48466')
);

function association_maj_51602()
{
	sql_alter("TABLE spip_asso_membres ADD date_adhesion DATE AFTER id_asso");
}
$GLOBALS['association_maj'][51602] = array(
	array('association_maj_51602')
);

/* Ces champs de configuration n'etant plus geres par defaut, les passer en personalises pour ceux qui les utilisent */
$GLOBALS['association_maj'][52476] = array(
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_siret'" ), "nom='siret' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='siret' AND valeur=''" ),
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_tva'" ), "nom='tva' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='tva' AND valeur=''" ),
);

/* mise a jour introduisant les groupes */
function association_maj_53901()
{
	sql_create('spip_asso_groupes',
		$GLOBALS['tables_principales']['spip_asso_groupes']['field'],
		$GLOBALS['tables_principales']['spip_asso_groupes']['key']);
	sql_alter("TABLE spip_asso_groupes AUTO_INCREMENT = 100");
	sql_create('spip_asso_groupes_liaisons',
		$GLOBALS['tables_principales']['spip_asso_groupes_liaisons']['field'],
		$GLOBALS['tables_principales']['spip_asso_groupes_liaisons']['key']);

	/* si on a des membres avec une fonction defini, on recupere tout et on les mets dans un groupe appele bureau */
	$liste_membres_bureau = sql_select("id_auteur, fonction" ,"spip_asso_membres", "fonction <> ''");
	if (sql_count($liste_membres_bureau )) {
		/* on cree un groupe "Bureau" */
		$id_groupe = sql_insertq("spip_asso_groupes", array('nom' => 'Bureau', 'affichage' => '1'));
		/* et on y insere tous les membres qui avaient une fonction */
		while ($membre_bureau = sql_fetch($liste_membres_bureau)) {
			sql_insertq("spip_asso_groupes_liaisons", array(	'id_groupe' => $id_groupe,
															'id_auteur' => $membre_bureau['id_auteur'],
															'fonction'  => $membre_bureau['fonction'],
													));
		}
	}

	/* on supprime le champs fonction de la table spip_asso_membres car il est maintenant gere dans spip_asso_groupes_liaison */
	sql_alter("TABLE spip_asso_membres DROP fonction");
}
$GLOBALS['association_maj'][53901] = array(
	array('association_maj_53901')
);

/* Creation de la table 'exercices' permettant de gerer la comptabilite en exercice comptable */
/* sur une annee civile, une annee'scolaire' ou sur des periodes donnees */
$GLOBALS['association_maj'][55177] = array(
	array('sql_create','spip_asso_exercices',
	$GLOBALS['tables_principales']['spip_asso_exercices']['field'],
	$GLOBALS['tables_principales']['spip_asso_exercices']['key'])
);

/* Changer les champs FLOAT en DECIMAL */
$GLOBALS['association_maj'][57429] = array(
	array ('sql_alter', "TABLE spip_asso_categories CHANGE cotisation cotisation DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE prix_vente prix_vente DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE frais_envoi frais_envoi DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE recette recette DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE depense depense DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_plan CHANGE solde_anterieur solde_anterieur DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE recette recette DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE depense depense DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE pu pu DECIMAL(19,4) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_activites CHANGE montant montant DECIMAL(19,4) NOT NULL"),
);

?>