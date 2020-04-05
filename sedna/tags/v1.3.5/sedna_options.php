<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (preg_match(',&age=([0-9]+)&age=([0-9]+),',$_SERVER['REQUEST_URI'],$regs)){
	url_de_base();
	$url = parametre_url(self(),'age',$regs[1],'&');
	include_spip('inc/headers');
	redirige_par_entete($url);
}

if($var_color=_request('var_color')) {
	include_spip('inc/cookie');
	spip_setcookie('sedna_color', $var_color, time()+365*24*3600);
	$_COOKIE['sedna_color'] = $var_color;
}

$GLOBALS['marqueur'] = (isset($GLOBALS['marqueur'])?$GLOBALS['marqueur']:'').isset($_COOKIE['sedna_color'])?(":".$_COOKIE['sedna_color']):"";

function sedna_utils(){
	$GLOBALS['forcer_lang']= true;

	// Descriptifs : affiches ou masques ?
	// l'accessibilite sans javascript => affiches par defaut
	if ($_COOKIE['sedna_style'] == 'masquer')
		$class_desc = "desc_masquer";
	else
		$class_desc = "desc_afficher";
	
	// Si synchro active il faut comparer le contenu du cookie et ce
	// qu'on a stocke dans le champ spip_auteurs.sedna (a creer au besoin)
	$synchro = '';
	if ($_COOKIE['sedna_synchro'] == 'oui'
	AND $id = $GLOBALS['visiteur_session']['id_auteur']) {
		// Recuperer ce qu'on a stocke
		$champ = $champ['sedna'];
		// mixer avec le cookie en conservant un ordre chronologique
		if ($_COOKIE['sedna_lu'] <> $champ) {
			$lus_cookie = preg_split(',[- +],',$_COOKIE['sedna_lu']);
			$lus_champ = preg_split(',[- +],',$champ);
			$lus = array();
			while (count($lus_cookie) OR count($lus_champ)) {
				if ($a = array_shift($lus_cookie))
					$lus[$a] = true;
				if ($a = array_shift($lus_champ))
					$lus[$a] = true;
			}
			$lus = substr(join('-', array_keys($lus)),0,3000); # 3ko maximum
			// Mettre la base a jour
			sql_updateq("spip_auteurs",array('sedna',$lus),"id_auteur=".intval($id));
			$synchro = ' *';

			// Si le cookie n'est pas a jour, on l'update sur le brouteur
			if ($lus <> $_COOKIE['sedna_lu']) {
				include_spip('inc/cookie');
				spip_setcookie('sedna_lu', $lus,
					time()+365*24*3600);
					$_COOKIE['sedna_lu'] = $lus;
				// Signaler que la synchro a eu lieu
				$synchro = ' &lt;&lt;';
			}
		}
	}
	// forcer le refresh ?
	if ($id = intval(_request('refresh'))) {
		include_spip('genie/syndic');
		syndic_a_jour($id);
	}
}

?>