<?php



	// Calcule le modele et retourne la mini-page ainsi calculee
	function Modeles_inclure_modele($squelette, $type, $id) {
		static $compteur;

		if (++$compteur>4) return ''; # ne pas boucler indefiniment

		$fond = 'modele_'.$type;
		if ($squelette)
			$fond .= "_$squelette";

		if (!find_in_path($fond.'.html'))
			return '<div><b>'.htmlentities($fond).'</b></div>';

		include_spip('public/assembler');
		$contexte = array('id_'.$type => $id);
		$page = recuperer_fond($fond, $contexte);

		$compteur--;

		return $page;
	}

	/* static public */ 
	function Modeles_traiter_modeles($texte) {

		$regexp = ',<(breve|article|doc|img)([0-9]+)([|]([a-z_0-9]+))?'.'>,';
		if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
			foreach ($matches as $regs) {
				$modele = Modeles_inclure_modele($regs[4], $regs[1], $regs[2]);
				$texte = str_replace($regs[0], code_echappement($modele), $texte);
			}

		return $texte;
	}



?>