<?php

function telepathe($contenu=null, $type=null) {
	static $t;

	// id = initialisation
	if ($type == 'id')
		$t = array();

	// autre, remplir
	if (!is_null($type)
	AND is_array($t)) {

		# un pipeline qui permet de traiter tout le contenu a envoyer,
		# par exemple pour recoder les URLs des liens
		$contenu = pipeline('telepathe', $contenu);

		// si type finit par *, c'est un array
		// si type finit par +, c'est une concatenation
		preg_match(',^(.*?)([\*\+])?$,S', $type, $r);
		switch($r[2]) {
			case '':
				$t[$r[1]] = $contenu;
				break;
			case '*':
				$t[$r[1]][] = $contenu;
				break;
			case '+':
				$t[$r[1]] .= $contenu;
				break;
		}
		return "<dt>$type</dt>\n<dd>".safehtml(filtre_print($contenu))."</dd>";
	}

	// rien, renvoyer le tableau
	if (is_null($type))
		return $t;
}


function telepathe_formater($html) {

	$v = telepathe();
	$r = array("a".$v['id'] => $v);

	switch (_request('format')) {
		case 'json':
			return pretty_json_encode($r, _request('callback'));
		case 'yaml':
			if (include_spip('inc/yaml'))
				return yaml_encode($r);
		#case 'xml':
		#	return xml($r);
		default:
			return $html;
	}


}

## essai (ratŽ) de faire du joli json affichable dans le nav ; mais
## avec yaml c'est plus propre
function pretty_json_encode($x, $callback=null) {
	$x = json_encode($x);

	if ($callback)
		$x = htmlspecialchars($callback)."(\n"
			. $x . "\n);\n";

	return $x;

/*
	if (is_array($x)) {
		$a = array();
		foreach ($x as $k=>$v)
			$a[] = " ".json_encode($k).":\n\t".pretty_json_encode($v);
		return "{\n".join(",\n", $a)."\n}\n";
	}

	return json_encode($x);
*/
}