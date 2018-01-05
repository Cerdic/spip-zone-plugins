<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

function action_ranger_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$verifier_ordre = charger_fonction('verifier_ordre', 'inc');
	include_spip("inc/autoriser");
	include_spip("inc/config");

	list($action, $lang, $id_liaison_objet, $ordre, $objet_dest, $id_objet_dest, $load) = explode('-', $arg);
	$load = _request('load');
	$id = _request('id');

	switch ($action) {
		case 'supprimer_ordre' :
			include_spip('formulaires/bouton_article');
			spip_log('eliminer 1', 'liaison');
			$where = array(
				'id_liaison_objet=' . $id_liaison_objet
			);

			sql_delete("spip_liaison_objets", $where);

			// on vérifie l'ordre des objets déjà enregistrés et on corrige si beliaison_objetin

			$where = array(
				'id_objet_dest=' . $id_objet_dest,
				'objet_dest=' . sql_quote($objet_dest),
				'lang=' . sql_quote($lang)
			);

			$ordre = $verifier_ordre($where);

			break;

		case 'remonter_ordre' :
			$where = array(
				'lang="' . $lang . '"',
				'objet_dest="' . $objet_dest . '"',
				'id_objet_dest="' . $id_objet_dest . '"',
				'ordre<="' . $ordre . '"'
			);

			$result = sql_select("*", "spip_liaison_objets", $where, '', "ordre DESC", 2);

			while ($row = sql_fetch($result)) {
				if ($id_liaison_objet == $row["id_liaison_objet"]) {
					$o = $ordre - 1;
				}
				else {
					$o = $ordre;
				}
				sql_updateq("spip_liaison_objets", array(
					"ordre" => $o
				), 'id_liaison_objet=' . $row['id_liaison_objet']);
			}
			$where = array(
				'id_objet_dest=' . $id_objet_dest,
				'objet_dest=' . sql_quote($objet_dest),
				'lang=' . sql_quote($lang)
			);
			$ordre = $verifier_ordre($where);
			break;

		case 'descendre_ordre' :
			$where = array(
				'lang="' . $lang . '"',
				'objet_dest="' . $objet_dest . '"',
				'id_objet_dest="' . $id_objet_dest . '"',
				'ordre>="' . $ordre . '"'
			);

			$result = sql_select("*", "spip_liaison_objets", $where, '', "ordre", 2);

			while ($row = sql_fetch($result)) {
				if ($id_liaison_objet == $row["id_liaison_objet"]) {
					$o = $ordre + 1;
				}
				else {
					$o = $ordre;
				}
				sql_updateq("spip_liaison_objets", array(
					"ordre" => $o
				), 'id_liaison_objet=' . $row['id_liaison_objet']);
			}
			$where = array(
				'id_objet_dest=' . $id_objet_dest,
				'objet_dest=' . sql_quote($objet_dest),
				'lang=' . sql_quote($lang)
			);
			$ordre = $verifier_ordre($where);
			break;
		// ranger avec drag and drop - liste des objets séléctionné sur la page de l'objet cible
		case 'nouvel_ordre' :
			$nouvel_ordre = explode(',', _request('nouvel_ordre'));
			$ordre = 0;
			foreach ($nouvel_ordre as $id_liaison_objet) {
				$ordre++;
				sql_updateq("spip_liaison_objets", array(
					"ordre" => $ordre
				), 'id_liaison_objet=' . $id_liaison_objet);
				include_spip('inc/invalideur');
				suivre_invalideur("id='liaison_objet/$id_liaison_objet'");
			}
			break;

		// ranger avec drag and drop - liste des objets séléctionné sur la page de l'objet
		case 'nouvel_ordre_objet' :
			$nouvel_ordre = explode(',', _request('nouvel_ordre'));
			$ordre = 0;
			foreach ($nouvel_ordre as $id_liaison_objet) {
				$ordre++;
				sql_updateq("spip_liaison_objets", array(
					"ordre_objet" => $ordre
				), 'id_liaison_objet=' . $id_liaison_objet);
				include_spip('inc/invalideur');
				suivre_invalideur("id='liaison_objet/$id_liaison_objet'");
			}
			break;
	}

	if ($load) {
		include_spip('public/assembler');
		$cont = calculer_contexte();
		$contexte = array(
			'id_objet_dest' => $id_objet_dest,
			'objet_dest' => $objet_dest,
			'l' => $lang,
			'id' => $id
		);
		$contexte = array_merge($cont, $contexte);
		echo recuperer_fond($load, $contexte);
	}
	return $return;
}
