<?php
if (!isset($_GET['ajax'])) {
?>
<html>
<head>

<!-- adapter le chemin pour charger jquery et la css du menu -->

<link rel="stylesheet" type="text/css" href="plugins/_plugins_/_ze_laboratoire_/spiip/dist_back/gadget-rubriques.css" >
	<script src="plugins/_plugins_/-jQuery/jquery.js" type="text/javascript"></script>

<script type="text/javascript"><!--
$.fn.activer_nav = function() {
	$("div.nav>ul li").click(
		function(e) {
			e.stopPropagation();
			var id = this.id.replace('rub-', '');
			$("#id_rubrique").set('value', id);
			$("div.nav").load("parcours.php?ajax=1&id_rubrique="+id, null, $.fn.activer_nav);
			return false;
		}
	);
};
$(document).load($.fn.activer_nav);
// --></script>
</head>
<body>
<pre>
ordre de parcours des rubriques :

distance (A à B) = profondeur(A) + profondeur(B) - 2*profondeur(ancetre commun)


N = (A)


while(!limite)

	B = A
	A = parent(A)
	N += enfants(N) triés par date inverse ou limite atteinte
	N[] = A


	N[r] = (string titre, array enfants(r))

</pre>

<form><input id="id_rubrique" type="text" value="<?php echo intval($_GET['id_rubrique']); ?>" /></form>


<?php } // if ajax

include('ecrire/inc_version.php');

#$mysql_profile = true;

include_spip('base/abstract_sql');
include_spip('inc/texte');


function arbo($id_rubrique, $limite=100, $req_suite) {
	$src = $id_rubrique;
	$n = array($id_rubrique => array());
	$r = array($id_rubrique);

	while ($limite > 0) {

		$b = $id_rubrique;

		if ($id_rubrique !== NULL) {
			$s = spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
			$p = spip_fetch_array($s);
			$r[] = intval($id_rubrique = $p['id_parent']);
		} else
			$id_rubrique = NULL;

		if ($limite > 1) {
			$s = spip_query("SELECT id_rubrique, id_parent FROM spip_rubriques WHERE " . calcul_mysql_in('id_parent', join(',',$r))
. $req_suite
#." LIMIT 0,".max(0, $limite-1)
);
			while ($p = spip_fetch_array($s)) {
				$n[$p['id_parent']]['enfants'][$p['id_rubrique']]++;
				$r[] = $p['id_rubrique'];
				$limite --;
			}
			$num = spip_num_rows($s);
		}
		else
			$num = 0;

		if ($b AND ($id_rubrique !== NULL)) {
			$n[$id_rubrique]['enfants'][$b]++;
			$r[] = $b;
			$limite --;
		}

		if ($id_rubrique === NULL AND !$num)
			break;

	}

	// Si on n'a pas touche la racine, remonter
	while ($id_rubrique) {
		$p = spip_fetch_array(spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
		$n[$p['id_parent']]['enfants'][$id_rubrique]++;
		$r[] = $id_rubrique;
		$id_rubrique = $p['id_parent'];
	}
	$r[] = 0;

	// recuperer les parents pas totalement vus (pour montrer "++")
	$s = spip_query("SELECT DISTINCT(id_parent) FROM spip_rubriques WHERE ".calcul_mysql_in('id_rubrique', join(',', $r), 'NOT'));
	while ($t = spip_fetch_array($s)) {
		if (in_array($t['id_parent'], $r))
			$n[$t['id_parent']]['plus'] = true;
	}

	return array($n, $r);
}


function afficher_soupe($src, $id, $n) {
	static $profondeur;

	$profondeur++;

	$elem = "($profondeur) -$id: ".typo(supprimer_numero($n[$id]['titre']));

	$v = 'id="rub-'.$id.'"';

	if ($n[$id]['plus']) {
		$elem .= ' ++';
		$ret .= "<li $v class='plus'>\n";
	}
	else
		$ret .= "<li $v>\n";

	if ($id == $src)
		$elem = "<b>$elem</b>";
	else
		$elem = "<a href='".parametre_url(self(), 'id_rubrique', $id)."'>$elem</a>";
	
	$ret .= $elem;

	if (is_array($n[$id]['enfants'])) {
		$ret .= "<ul>\n";
		foreach ($n[$id]['enfants'] as $e => $rien) {
			$ret .= afficher_soupe($src, $e, $n);
		}
		$ret .= "</ul>\n";
	}

	$ret .= "</li>\n";
	$profondeur--;

	return $ret;
}

list($n, $r) = arbo(
	intval($_GET['id_rubrique']),
	sinon($_GET['num'],10),
	" ORDER BY 0+titre, titre"
);

// Recuperer les titres des rubriques
$s = spip_query("SELECT id_rubrique,titre FROM spip_rubriques WHERE ".calcul_mysql_in('id_rubrique', join(',', $r)));
while ($t = spip_fetch_array($s))
	$n[$t['id_rubrique']]['titre'] = $t['titre'];

$n[0]['titre'] = "Racine du site";


if (!isset($_GET['ajax']))
	echo "<div class='nav' id='bandeautoutsite'>";

echo "<ul>";
echo afficher_soupe(intval($_GET['id_rubrique']), 0, $n);
echo "</ul>";

if (!isset($_GET['ajax']))
	echo "</div>";
