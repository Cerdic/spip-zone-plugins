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


//TODO: essayer de se passer de cette insertion unilaterale de css ...
function SquelettesMots_header_prive($texte) {
  $texte.= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_SQUELETTESMOTS.'chercher_squelettes_mots.css" />' . "\n";
  return $texte;
}

// Selection du squelette par mot cle
function SquelettesMots_styliser($flux) {
	// quand le selecteur doit il s'activer ?
	// pas de fond=rep/nom (inclusions)
	if ($fond = $flux['args']['fond']
	and false === strpos($fond, '/')) {
		// on cherche si le squelette a deja trouve un fond particulier specifique
		// c'est a dire different de $fond.html.
		// Si c'est le cas, on ne cherche pas de squelette specifique par mot.
		$skel = basename($flux['data']);
		if ($skel == $fond) {
			$fonds = unserialize($GLOBALS['meta']['SquelettesMots:fond_pour_groupe']);
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

function test_squelette_motcle($fond, $ext, $mot, $separateur = '-') {
	if ($squelette = find_in_path($fond . $separateur . $mot . '.' . $ext)) {
		return substr($squelette, 0, -strlen(".$ext"));
	}
	return false;
}

// smc: on ajoute la fonction qui va chercher les mots
// associes aux items dans le groupe qui va bien
// recursion : seulement si $id de rubrique
function quete_mot_squelette($id ,$id_groupe, $table, $_id_table, $recurse=false) {
  $select1 = 'titre';
  $from1 = array('spip_mots AS mots',
				 "spip_mots_$table AS lien");
  while($id > 0) {
	$where1 = array("$_id_table=$id",
					'mots.id_mot=lien.id_mot',
					"id_groupe=$id_groupe");
	
	// attention getfetsel ne renvoie qu'un mot !
	// si quelqu'un a mis plusieurs mots sur l'objet, tant pis pour lui,
	// on ne peut pas predire lequel sera retourne
	if ($titre = sql_getfetsel($select1,$from1,$where1)) {
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
