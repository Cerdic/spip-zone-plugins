<?php
include_spip('base/noisetier');

//Définition des pages gérées par le noisetier
global $noisetier_pages;
if (!isset($noisetier_pages)) $noisetier_pages = array();
$noisetier_pages[]='article';
$noisetier_pages[]='rubrique';
$noisetier_pages[]='breve';
$noisetier_pages[]='sommaire';

function balise_INCLURE_NOISETTE($p) {
	$champ = phraser_arguments_inclure($p, true);
	$_contexte = argumenter_inclure($champ, $p->descr, $p->boucles, $p->id_boucle, false);

	if (isset($_contexte['fond'])) {
		// Critere d'inclusion {env} (et {self} pour compatibilite ascendante)
		if (isset($_contexte['env'])
		|| isset($_contexte['self'])
		) {
			$flag_env = true;
			unset($_contexte['env']);
		}
		$l = 'array(' . join(",\n\t", $_contexte) .')';
		if ($flag_env) {
			$l = "array_merge(\$Pile[0],$l)";
		}
		//Le fond sera récupérer par recuperer_noisette au lieu de recuperer fond. On passe la pile en même temps
		$p->code = "recuperer_noisette('',".$l.",true, false, \$Pile[0])";
	} else {
		$n = interprete_argument_balise(1,$p);
		$p->code = '(($c = find_in_path(' . $n . ')) ? spip_file_get_contents($c) : "")';
	}

	$p->interdire_scripts = false; // la securite est assuree par recuperer_fond
	return $p;
}

function recuperer_noisette($fond, $contexte=array(),$protect_xml=false, $trim=true, $pile) {
	// on est peut etre dans l'espace prive au moment de l'appel
	define ('_INC_PUBLIC', 1);
	if (($fond=='')&&isset($contexte['fond']))
		$fond = $contexte['fond'];

	// Si une id_noisette a été passée
	if(isset($contexte['id_noisette'])) {
		$id_noisette = $contexte['id_noisette'];
		$query = "SELECT * FROM spip_params_noisettes WHERE id_noisette=$id_noisette";
		$result = spip_query($query);
		while ($row = spip_fetch_array($result)){
			//Si variable d'environnement à passer
			if ($row['type']=='env') {
				$nom_env = $row['titre'];
				if (isset($pile[$nom_env])) $contexte[$nom_env] = $pile[$nom_env];
			}
			//On passe les paramètres
			if ($row['type']=='param') {
				$nom_param = $row['titre'];
				$contexte[$nom_param] = $row['valeur'];
			}
		}
	}

	$fonds = array($fond);
	if (is_array($fond)) $fonds=$fond;
	$texte = "";
	foreach($fonds as $fond){
		$page = inclure_page($fond, $contexte);
		if ($GLOBALS['flag_ob'] AND ($page['process_ins'] != 'html')) {
			ob_start();
			eval('?' . '>' . $page['texte']);
			$page['texte'] = ob_get_contents();
			ob_end_clean();
		}
		if (!$protect_xml && isset($page['entetes']['X-Xml-Hack']))
			$page['texte'] = str_replace("<\1?xml", '<'.'?xml', $page['texte']);
	
		$texte .= $page['texte']; // pas de trim, pour etre homogene avec <INCLURE>
		if ($trim) $texte = trim($texte);
	}
	return $texte;
}

?>