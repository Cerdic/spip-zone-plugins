<?php

class Modeles {

	// Calcule le modele et retourne la mini-page ainsi calculee
	function inclure_modele($squelette, $type, $id) {
		static $compteur;

		if (++$compteur>4) return ''; # ne pas boucler indefiniment

		if ($squelette)
			$fond = 'modele_'.$squelette;
		else
			$fond = 'modele_'.$type;


		### pour tester directement :
		if (!find_in_path($fond.'.html'))
			$fond = 'plugins/modeles/'.$fond;

		if (!find_in_path($fond.'.html')
		OR !_DIR_RESTREINT)
			return '<div><b>'.htmlentities($fond).'</b></div>';

		$contexte_inclus = array('id_'.$type => $id);

		ob_start();
		$page = inclure_page($fond, $contexte_inclus);
		if ($page['process_ins'] == 'html')
			echo $page['texte'];
		else
			eval('?' . '>' . $page['texte']);

		if ($page['lang_select'] === true)
			lang_dselect();

		$page = ob_get_contents(); 
		ob_end_clean();

		$compteur--;

		return $page;
	}

	/* static public */ 
	function traiter_modeles($texte) {

		$regexp = ',<(breve|article)([0-9]+)([|]([a-z_0-9]+))?'.'>,';
		if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
		foreach ($matches as $regs) {
			$modele = Modeles::inclure_modele($regs[4], $regs[1], $regs[2]);
			$texte = str_replace($regs[0], code_echappement($modele), $texte);
		}

		return $texte;
	}

}

?>