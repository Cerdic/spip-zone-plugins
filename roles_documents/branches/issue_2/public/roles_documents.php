<?php
/**
 * Balises et critères utiles au plugin Rôles de documents
 *
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Roles_documents\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Surcharge du critère `logo`
 *
 * Tout comme le critère {logo} par défaut, on permet de sélectionner tous les
 * objets qui ont un logo, quel qu'il soit, au format historique ou au format
 * document.
 *
 * Un unique paramètre optionnel permet de se restreindre à un rôle
 * particulier. Par exemple, {logo accueil} permet de sélectionner les logos
 * dont le rôle est 'logo_accueil'.
 *
 * {!logo} permet d'inverser la sélection, pour avoir les objets qui n'ont PAS
 * de logo.
 *
 * @uses lister_objets_avec_logos()
 *     Pour obtenir les éléments qui ont un logo enregistrés avec la méthode
 *     "historique".
 *
 * @param string $idb Identifiant de la boucle
 * @param array $boucles AST du squelette
 * @param Critere $crit Paramètres du critère dans cette boucle
 * @return void
 */
function critere_logo($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];

	// On interprète le premier paramètre du critère, qui nous donne le type de
	// logo
	if (count($crit->param)) {
		$type_logo = calculer_liste(
			array_shift($crit->param),
			array(),
			$boucles,
			$boucle->id_parent
		);
		$type_logo = trim($type_logo, "'");
	}

	// Pour ajouter la jointure qu'il nous faut à la boucle, on lui donne le
	// premier alias L* qui n'est pas utilisé.
	$i = 1;
	while (isset($boucle->from["L$i"])) {
		$i++;
	}
	$alias_jointure = "L$i";

	$alias_table = $boucle->id_table;
	$id_table_objet = $boucle->primary;

	// On fait un LEFT JOIN avec les liens de documents qui correspondent au(x)
	// rôle(s) cherchés. Cela permet de sélectionner aussi les objets qui n'ont
	// pas de logo, dont le rôle sera alors NULL. C'est nécessaire pour pouvoir
	// gérer les logos enregistrés avec l'ancienne méthode, et pour {!logo}.
	$boucle->from[$alias_jointure] = 'spip_documents_liens';
	$boucle->from_type[$alias_jointure] = 'LEFT';
	$boucle->join[$alias_jointure] = array(
		"'$alias_table'",
		"'id_objet'",
		"'$id_table_objet'",
		"'$alias_jointure.objet='.sql_quote('" . objet_type($alias_table) . "')." .
		"' AND $alias_jointure.role LIKE \'logo\_" . ($type_logo ?: '%') . "\''",
	);
	$boucle->group[] = "$alias_table.$id_table_objet";

	// On calcule alors le where qui va bien.
	if ($crit->not) {
		$where = "$alias_jointure.role IS NULL";
	} else {
		$where = array(
			"'LIKE'",
			"'$alias_jointure.role'",
			"'\'logo\_" . ($type_logo ?: '%') . "\''",
		);
	}

	// Rétro-compatibilité : Si l'on ne cherche pas un type de logo particulier,
	// on retourne aussi les logos enregistrés avec la méthode "historique".
	if (! $type_logo) {
		$where_historique =
			'sql_in('
			. "'$alias_table.$id_table_objet', "
			. "lister_objets_avec_logos('$id_table_objet'), "
			. "'')";

		if ($crit->not) {
			$where_historique = array("'NOT'", $where_historique);
		}

		$where = array(
			"'OR'",
			$where,
			$where_historique
		);
	}

	// On ajoute le where à la boucle
	$boucle->where[] = $where;
}


/**
 * Compile la balise `#IMAGE_ROLE` qui retourne le code HTML
 * pour afficher un document image d'après son rôle.
 *
 * Il faut préciser le rôle souhaité en le passant en majuscule à la fin de la balise.
 * Par exemple pour le role `couverture` : #IMAGEROLE_COUVERTURE
 *
 * On peut passer objet et id_objet en paramètre,
 * à défaut on prend l'objet depuis la boucle englobante.
 *
 * `#IMAGEROLE_X*` retourne le nom du fichier.
 *
 * @balise
 * @example
 *     ```
 *     #IMAGEROLE_COUVERTURE
 *     #IMAGEROLE_COUVERTURE{livre,10}
 *     ```
 *
 * @param object $p
 *     Pile au niveau de la balise
 * @return object
 *     Pile complétée par le code à générer
 */

function balise_IMAGEROLE__dist($p) {

	$index        = index_boucle($p); // <BOUCLE_truc> → _truc
	$boucle       = ($index ? $p->boucles[$index] : null);
	$objet_boucle = ($boucle ? $boucle->type_requete : ''); // articles
	$cle_objet    = ($boucle ? $p->boucles[$index]->primary : ''); // id_article
	$serveur      = ($boucle ? $p->boucles[$index]->sql_serveur : '');
	$etoile       = $p->etoile;

	// Retrouver le rôle depuis le nom la balise
	// #ROLEIMAGE_COUVERTURE → couverture
	if (preg_match('/^IMAGEROLE_([A-Z_]+)$/i', $p->nom_champ, $matches)) {
		$role = strtolower($matches[1]);
	} else {
		$role = '';
		$msg = array(
			'roles_documents:zbug_balise_role_inconnu',
			array('balise' => "#$index" . 'IMAGEROLE')
		);
		erreur_squelette($msg, $p);
	}

	// Retrouver l'objet éditorial :
	// soit celui passé en paramètre, soit depuis la boucle englobante
	if (
		!$_objet = interprete_argument_balise(1, $p)
		or !$_id_objet = interprete_argument_balise(2, $p)
	) {
		if ($boucle) {
			$_objet    = _q($objet_boucle);
			$_id_objet = champ_sql($cle_objet, $p);
		} else {
			$_objet    = _q('');
			$_id_objet = _q('');
			$msg = array(
				'roles_documents:zbug_balise_hors_boucle',
				array('balise' => '#IMAGEROLE')
			);
			erreur_squelette($msg, $p);
		}
	}

	$p->code = "calculer_balise_image_role($_objet, intval($_id_objet), '$role', '$etoile', '$serveur')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Produit le code HTML pour la balise `#IMAGEROLE`
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $role
 * @param string $etoile
 * @param string $serveur
 * @return string
 */
function calculer_balise_image_role($objet, $id_objet, $role, $etoile = '', $serveur = '') {

	$code_html = '';
	if ($objet and $id_objet and $role) {
		include_spip('base/objets');
		$objet = objet_type($objet);

		if ($doc = sql_fetsel(
			'd.id_document, fichier, titre, extension',
			'spip_documents AS D INNER JOIN spip_documents_liens as L on L.id_document=D.id_document',
			array(
				'd.media = ' . sql_quote('image'),
				'l.objet = ' . sql_quote($objet),
				'l.id_objet = ' . intval($id_objet),
				'l.role = ' . sql_quote($role),
			),
			'',
			'',
			'',
			'',
			$serveur
		)) {

			include_spip('inc/utils');
			$url = generer_url_entite($doc['id_document'], 'document');

			// Si étoile on retourne l'URL
			if ($etoile) {
				$code_html = $url;

			// Sinon une balise HTML
			} else {
				include_spip('inc/texte');
				if (
					$doc['extension'] === 'svg'
					and (charger_fonction('balise_svg', 'filtre', true) !== false) // SPIP >= 3.3
				) {
					$balise_img = charger_fonction('balise_svg', 'filtre');
				} else {
					$balise_img = charger_fonction('balise_img', 'filtre');
				}
				// Comme texte alternatif on prend le vrai titre saisi à la main, pas celui calculé
				// (qui renvoie le nom du fichier si le titre est vide).
				$alt = typo($doc['titre']);
				$code_html = $balise_img($url, $alt);
			}
		}
	}

	return $code_html;
}
