<?php
define ('CACHELAB_DEFAULT_ACTION', 'list');

echo <<<EOT
<pre>Arguments d'url possibles pour changer les valeurs par défaut :
- methode : <b>strpos</b> ou regexp
- action : <b>list</b>, del, show, mark (avec 'cachelab_mark'= 1 ou 'invalide (explication)')
- objet : <b>article</b>, breve, etc
- cle_objet : si différent de 'id_'+objet
- id_article, id_breve, etc selon objet
- chemin : liste séparée par | ou fullregexp si methode=regexp
</pre>
EOT;

if ($_GET['methode'])
	define ('CACHELAB_METHODE_CHEMIN', $_GET['methode']);
else 
	define ('CACHELAB_METHODE_CHEMIN', 'strpos');

if (isset ($_GET['objet']))
	$objet = $_GET['objet'];
else
	$objet = XRAY_OBJET_SPECIAL;

if (isset ($_GET['cle_objet']))
	$cle_objet = $_GET['cle_objet'];
elseif ($objet)
	$cle_objet = 'id_'.$objet;	// TODO spiper
else 
	$cle_objet = '';

if ($cle_objet and isset ($_GET[$cle_objet]))
	$id_objet = $_GET[$cle_objet];
else
	$id_objet = XRAY_ID_OBJET_SPECIAL;
$id_objet=intval($id_objet);
$url_objet = "?page=$objet&$cle_objet=$id_objet";

if (isset ($_GET['action']))
	$action = $_GET['action'];
else
	$action = CACHELAB_DEFAULT_ACTION;

if ($cle_objet and !$id_objet)
	die ("$cle_objet est inconnu : passez le en argument d'url ou définissez XRAY_ID_OBJET_SPECIAL en php");

if (isset ($_GET['chemin']))
	$chemin = $_GET['chemin'];
else 
	$chemin = 'admin|prive|liste';


echo "<b>Action : $action<br>
		Méthode pour la recherche des chemins : ".CACHELAB_METHODE_CHEMIN."<br>
		Recherchés : <br>
		- objet=$objet cle_objet=$cle_objet id_objet=$id_objet <br>
		- chemins : $chemin<br><br>";


function cachelab_applique ($action, $cle, $val=null, $opt='') {
global $Memoization;
static $len_prefix;
	if (!$len_prefix)
		$len_prefix = strlen(_CACHE_NAMESPACE);

	$cle = substr($cle, $len_prefix);

	switch ($action) {
	case 'del' :
		$del = $Memoization->del ($cle);
		if (!$del) echo "echec del $cle<br>";
		break;
	case 'mark' :
		if ($val === null)
			$data = $Memoization->get ($cle);
		else
			$data = $val;
		if (is_array($data)) {
			$data['cachelab_mark'] = ($opt ? $opt : 1);
			$data = $Memoization->set ($cle, $data);
		}
		else
			echo "Pour $action sur $cle (avec val=".print_r($val,1).") et opt='$opt', data n'est pas un tableau : (".print_r($data, 1).')<br>';
		break;
	case 'list' :
		break;
	case 'show' :
	default : 
		$data = $Memoization->get ($cle);
		if (isset($data['texte']))
			$data['texte'] = strip_tags(substr($data['texte'], 0, 1000).'...');
		echo "<b>Trouvé $cle :</b><pre class='cachelab_applique_show'>".print_r($data,1)."</pre>";
		break;
	}
}

// $chemin : liste de chaines à tester dans le chemin du squelette, séparées par |
// 	OU une regexp (hors délimiteurs et modificateurs) si la méthode est 'regexp'
function cachelab_filtre ($action, $cle_objet, $id_objet, $chemin='', $opt='invalide') {
	include_spip ('lib/microtime.inc');
	microtime_do ('begin');

	$listechemin = $listeobjet = array();
	$nb_valides=0;
	$nb_echecaccesdata=0;
	$nb_absentducontexte=0;
	$nb_accesdata=0;
	$nb_datanotarray=0;
	
	$chemins = explode('|', $chemin);
	$cache = apcu_cache_info();
	foreach($cache['cache_list'] as $i => $d) {
		if ($d and apcu_exists($d['info'])
			//and ($meta_derniere_modif <= $d['creation_time'])
			) 
		{
			$nb_valides++;
			$danslechemin = false;
			switch (CACHELAB_METHODE_CHEMIN) {
			case 'strpos' :
				foreach ($chemins as $unchemin) {
					if (strpos ($d['info'], $unchemin) !== false) {
						$listechemin[]=$d;
						$danslechemin = true;
						cachelab_applique ($action, $d['info'], null, $opt.' (chemin)');
						break;
					};
				}
				break;
			case 'regexp' :
				if ($danslechemin = preg_match(",$chemin,i", $d['info'])) {
					$listechemin[]=$d;
					cachelab_applique ($action, $d['info'], null, $opt.' (chemin)');
				}
				break;
			};

			if (!$danslechemin and $cle_objet) {
				if ($data = get_apc_data($d['info'], $success)) {
					$nb_accesdata++;

					if (is_array($data)) {
						if (isset($data['contexte'])
							and isset ($data['contexte'][$cle_objet])
							and ($data['contexte'][$cle_objet]==$id_objet)) {
								$listeobjet[] = $d;
								cachelab_applique ($action, $d['info'], $data, $opt.' (objet)');
							}
						else
							$nb_absentducontexte++;
					}
					else 
						$nb_datanotarray++;
				}
				else 
					$nb_echecaccesdata++;
			}
		}
	}
	$stats = array('nb filtres examinés'=>$nb_valides, 
				'nb echec acces data' => $nb_echecaccesdata,
				'nb acces data' => $nb_accesdata,
				'nb data not array' => $nb_datanotarray,
				'nb objet absent du contexte' => $nb_absentducontexte,
				'microtime' => microtime_do ('end', 'ms')
			);
	return array($listeobjet, $listechemin, $stats);
}

list ($listeobjet, $listechemin, $stats) = cachelab_filtre($action, $cle_objet, $id_objet, $chemin);
// list ($listeobjet, $listechemin) = filtre_cache('', 1, $chemin);

echo "<h3>Bilan du filtrage</h3><br>
		Caches trouvés avec le chemin $chemin : ".count($listechemin)."<br>
		Caches trouvés avec <a href='$url_objet'>$objet $id_objet</a> : ".count($listeobjet)."</b><br>
		<br><b>Stats :</b><pre>    ".trim(str_replace('Array', '', print_r($stats, 1)), "() \n")."</pre>";

echo "<h3>Caches trouvés avec le chemin $chemin : ".count($listechemin)."</h3>
	<ul>";
foreach ($listechemin as $d)
	echo "	<li>{$d['info']}</li>";
if ($cle_objet) {
	echo "</ul>
		<h3>Caches trouvés avec <a href='$url_objet'>$objet $id_objet</a> : ".count($listeobjet)."</h3>
		<ul>";
	foreach ($listeobjet as $d)
		echo "	<li>{$d['info']}</li>";
	echo "</ul>";
};
