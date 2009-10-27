<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_ab_testing() {

	pipeline('exec_init',array('args'=>array('exec'=>_request('exec')),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('A/B Testing'), "accueil", "accueil");


	echo gros_titre(_L('A/B Testing'), '', false);

	echo debut_gauche("",true);
	echo creer_colonne_droite("", true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>_request('exec')),'data'=>''));
	echo debut_droite("", true);


	if (!function_exists('xcache_inc'))
		echo debut_cadre_trait_couleur('', true)
			."A/B testing necessite un serveur avec l'extension XCache"
			.fin_cadre_trait_couleur(true);
	else
		bloc_ab_testing(_request('reset') == 'oui');


	echo "<br /><br /><br /><br /><br />\n"; # goret
	echo debut_cadre_trait_couleur('', true);
	echo "<a href='".generer_url_ecrire('cfg', 'cfg=ab')."'>Configuration</a>";
	echo " - ";
	echo "<a href='".parametre_url(self(), 'reset', 'oui')."'
		onclick='return confirm(&quot;on efface ?&quot;);'
		>Reset</a>";
	echo fin_cadre_trait_couleur(true);


	echo pipeline('affiche_milieu',array('args'=>array('exec'=>_request('exec')),'data'=>''));
	echo fin_gauche(), fin_page();


}


function bloc_ab_testing($reset = false) {

	# lire la config
	$cfg = @unserialize($GLOBALS['meta']['ab']);

	if (!$cfg['urls']) {
		echo ('Aucune URL suivie !');
		return;
	}

	if (!$n = intval($cfg['n'])) $n = 2;

	for ($i=0; $i<$n; $i++) {
		echo "<h4>Cohorte $i</h4>\n";
		echo "<table class='spip'>\n";
		echo "<tr><th>URI</th><th>hits</th><th>%</th></tr>\n";
		foreach (array_filter(preg_split(",[\r\n]+,", $cfg['urls'])) as $page) {
			$mem = ab_silo($page, $i);
			if ($reset) xcache_set($mem, 0);
			$m[$i][$page] = xcache_get($mem);
		}
		if (!$max[$i] = @max($m[$i])) $max[$i] = 1;

		foreach (array_filter(preg_split(",[\r\n]+,", $cfg['urls'])) as $page) {
			$pc = ceil(1000*$m[$i][$page]/$max[$i])/10;
			echo "<tr><td>$page</td><td>".$m[$i][$page]."</td><td>".$pc."%</td></tr>\n";
		}
		echo "</table>\n";
		echo "Squelettes pour cette cohorte: <a href='/AB$i' target='_AB$i'>/AB$i/</a>";
	}



}

?>