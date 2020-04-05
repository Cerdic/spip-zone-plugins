<?php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

define('_SQUELETTES_MOTS_META','SquelettesMots:fond_pour_groupe');

/**
 * Selection du squelette par mot cle
 *
 * @param array $flux
 * @return array
 */
function squelettesmots_styliser($flux) {
	// quand le selecteur doit il s'activer ?
	// pas de fond=rep/nom (inclusions)
	if (!test_espace_prive()
	  AND $fond = $flux['args']['fond']
	  AND strpos($fond, '/')===false) {
	  // on cherche si le squelette a deja trouve un fond particulier specifique
		// c'est a dire different de $fond.html.
		// Si c'est le cas, on ne cherche pas de squelette specifique par mot.
		$skel = basename($flux['data']);
		if ($skel == $fond) {
			$fonds = unserialize($GLOBALS['meta'][_SQUELETTES_MOTS_META]);
			// array ($base_fond = array($id_groupe, $table, $_id_table))
			// on teste qu'on a bien un fond
			if (is_array($fonds) and isset($fonds[$fond])) {
				// si un identifiant du meme type est bien passe
				list($id_groupe, $table, $_id_table) = $fonds[$fond];
				$ext = $flux['args']['ext'];
				$id_rub = $flux['args']['id_rubrique'];
				if ($id = intval($flux['args']['contexte'][$_id_table])) {
					// premier cas : mot sur l'article
					if ($mot = quete_mot_squelette($id, $id_groupe, $table, $_id_table)
					and   ($squelette = test_squelette_motcle($fond, $ext, $mot, '=')
						or $squelette = test_squelette_motcle($fond, $ext, $mot))) {
							$flux['data'] = $squelette;
					}
					// second cas : mot sur une rubrique parente
					elseif ($mot = quete_mot_squelette($id_rub, $id_groupe, 'rubriques', 'id_rubrique', true)
						and $squelette = test_squelette_motcle($fond, $ext, $mot)) {
							$flux['data'] = $squelette;
					}
				}
			}
		}
	}	
	return $flux;
}

/**
 * Tester l'existence d'un squelette
 * @param string $fond
 * @param string $ext
 * @param string $mot
 * @param string $separateur
 * @return string
 */
function test_squelette_motcle($fond, $ext, $mot, $separateur = '-') {
	if ($squelette = find_in_path($fond . $separateur . $mot . '.' . $ext)) {
		return substr($squelette, 0, -strlen(".$ext"));
	}
	return '';
}

/**
 * on ajoute la fonction qui va chercher les mots
 * associes aux items dans le groupe qui va bien
 * recursion : seulement si $id de rubrique
 *
 * @param int $id
 * @param int $id_groupe
 * @param string $table
 * @param string $_id_table
 * @param bool $recurse
 * @return string
 */
function quete_mot_squelette($id ,$id_groupe, $table, $_id_table, $recurse=false) {
	$objet = objet_type($table);

  while($id > 0) {
		$where = array(
			"lien.id_objet=".intval($id),
			"lien.objet=".sql_quote($objet),
			"id_groupe=".intval($id_groupe)
		);

		// attention getfetsel ne renvoie qu'un mot !
		// si quelqu'un a mis plusieurs mots sur l'objet, tant pis pour lui,
		// on ne peut pas predire lequel sera retourne
		if ($titre = sql_getfetsel('titre',"spip_mots AS mots JOIN spip_mots_liens AS lien ON mots.id_mot=lien.id_mot",$where)) {
			include_spip("inc/charsets");
			include_spip("inc/filtres");
			return translitteration(preg_replace('/["\'.\s]/', '_', extraire_multi($titre)));
		}
		if(!$recurse) return '';
		// attention identifiant de rubrique ici uniquement
		$id = quete_parent($id);
  }
  return '';
}
?>
