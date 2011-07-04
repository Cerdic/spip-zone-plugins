<?php

function propre_latex($t) {
	static $wheel, $notes;


	if (!isset($wheel)) {
		$ruleset = SPIPTextWheelRuleset::loader(
			array('latex/latex.yaml'),'personnaliser_raccourcis'
		);
		
		$wheel = new TextWheel($ruleset);

		if (_request('var_mode') == 'wheel'
		AND autoriser('debug')) {
			$f = $wheel->compile();
			echo "<pre>\n".htmlspecialchars($f)."</pre>\n";
			exit;
		}
	}



	$t = $wheel->text($t);



	return $t;
}

?>