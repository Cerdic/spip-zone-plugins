<?php

	//
	//balise #TABLE_MATIERE
	//
	function balise_TABLE_MATIERE_dist($p) {
		$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
		if ($b === '') {
			erreur_squelette(
				_T('zbug_champ_hors_boucle',
					array('champ' => '#TABLE_MATIERE')
				), $p->id_boucle);
			$p->code = "''";
		}
		elseif (!$p->param || $p->param[0][0]) {
			$avant = "'- '";
			$apres = "'<br />'";
		}
		else {
			$avant =  calculer_liste($p->param[0][1],
				$p->descr,
				$p->boucles,
				$p->id_boucle);
			$apres =  calculer_liste($p->param[0][2],
				$p->descr,
				$p->boucles,
				$p->id_boucle);
		}
		$p->code = "
		AncresIntertitres_compose_table_matiere(
			AncresIntertitres_table_matiere(\"retour\"),
			$avant,
			$apres
		)";
		$p->interdire_script = true;
		return $p;
	}

?>
