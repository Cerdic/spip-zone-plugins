<?php

// module inclu dans la description de l'outil en page de configuration

//include_spip('inc/actions');
//include_spip('inc/actions_compat');

// verifie les entrees mortes
function glossaire_verifie(&$c) {
	include_spip('public/parametrer'); // pour mes_fonctions
	$res = $res2 = array();
	$c = count($gloss = glossaire_query_tab());
	for($i=0; $i<$c; $i++) {
		$gi = &$gloss[$i]; glossaire_verifie_init($gi, $res);
		for($j=$i+1; $j<$c; $j++) {
			$gj = &$gloss[$j]; glossaire_verifie_init($gj, $res);
			$u = false;
			$titre = $gi['mots']?glossaire_gogogo($gj['titre2'], $gi['mots'], -1, $u):'';
			if(count($gi['regs']))
				$titre .= preg_replace_callback($gi['regs'], "glossaire_echappe_mot_callback", $gj['titre'], -1);
			if(strpos($titre,'@@GLOSS')!==false) {	
				$a = '['.$gi['titre'].'->mot'.$gi['id_mot'].']';
				$b = '['.$gj['titre'].'->mot'.$gj['id_mot'].']';
				$res2[] = "&bull; ".couteauprive_T('glossaire_erreur', array('mot1'=>$a, 'mot2'=>$b));
			}
		}
	}
	if(count($res)) $res[] = couteauprive_T('glossaire_verifier');
	if(count($res2)) $res2[] = couteauprive_T('glossaire_inverser');
	return propre(join("\n_ ", array_merge($res, $res2)));
}

// function d'initialisation utilisee par la precedente
function glossaire_verifie_init(&$g, &$res) {
	static $gu;
	if(!isset($g['mots'])) {
		if(!isset($gu)) $gu = function_exists('glossaire_generer_url')?'glossaire_generer_url':'glossaire_generer_url_dist';
		list($g['mots'], $g['regs'], $g['titre2'], $ok_regexp) = glossaire_parse(extraire_multi($g['titre']));
		if(!$ok_regexp) $res[] = "&bull; <html>"._L('Erreur : ') . cs_lien(glossaire_generer_url_dist($g['id_mot']), htmlentities(extraire_multi($g['titre'])))."</html>";
	}
}

function glossaire_action_rapide($actif) {
	if(_request('test_bd')) {
		$info = glossaire_verifie($count);
		$info = $info
			?('<div style="color:red">'.$info.'</div>')
			:('<div style="color:green">'._T('couteauprive:glossaire_ok', array('nb'=>$count)).'</div>');
	} else $info = '';
	return ajax_action_rapide_simple('test', $info, 'couteau:lancer_test', 'couteau:test_base');
}

// fonction {$outil}_{$arg}_action() appelee par action/action_rapide.php
function glossaire_test_action() {
	// lancer la verification des mots du glossaire
	redirige_vers_exec(array('test_bd' => 1));
}

?>