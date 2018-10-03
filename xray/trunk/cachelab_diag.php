<p><b>Action ciblée sur le cache</b> : Les arguments supplémentaires de l'url spécifient quelle action doit être appliquée sur quels caches.
<small><ul>
<li>action : del, mark, pass, <b>list</b></li>
<li>chemin : liste de morceaux de chemins séparés par | , ou expression régulière si methode=regexp</li>
<li>methode : fonction de détection du chemin spécifié : <b>strpos</b> ou regexp</li>
<li>objet : un type d'objet (article, breve, etc) ou XRAY_OBJET_SPECIAL si non spécifié</li>
<li>cle_objet : clé primaire (si différente de 'id_'+objet)</li>
<li>id_article, id_breve, etc selon objet</li>
</ul></small>
</p>

<?php
if (isset ($_GET['session']))
	$session = $_GET['session'];
else
	$session = '';

if (isset($_GET['methode']) and $_GET['methode'])
	$cachelab_methode_chemin = $_GET['methode'];
else 
	$cachelab_methode_chemin = 'strpos';

if (isset ($_GET['chemin']))
	$chemin = $_GET['chemin'];


if (isset ($_GET['objet']))
	$objet = $_GET['objet'];
elseif (defined ('XRAY_OBJET_SPECIAL') and XRAY_OBJET_SPECIAL)
	$objet = XRAY_OBJET_SPECIAL;
else 
	$objet = null;

if (isset ($_GET['cle_objet']))
	$cle_objet = $_GET['cle_objet'];
elseif ($objet)
	$cle_objet = 'id_'.$objet;	// TODO appeler API spip
else 
	$cle_objet = '';

if ($cle_objet and isset ($_GET[$cle_objet]))
	$id_objet = $_GET[$cle_objet];
else
	$id_objet = 0;

$id_objet=intval($id_objet);
if ($id_objet and $objet)
	$url_objet = "?page=$objet&$cle_objet=$id_objet";
else 
	$url_objet = '';

if (isset ($_GET['action']))
	$action = $_GET['action'];
else
	$action = 'list';

$contexte_test=array('id_article' => 1 , 'id_rubrique' => 48 );
$contexte = ((isset ($_GET['contexte'])) ? $contexte_test : '');
	
if ($cle_objet and !$id_objet)
	$cle_objet='';

$conditions = array('session'=>$session, 'chemin'=>$chemin, 'cle_objet'=>$cle_objet, 'id_objet'=>$id_objet, 'contexte'=>$contexte);
$options = array('chrono'=>true, 'list'=>true, 'methode_chemin'=>$cachelab_methode_chemin);

echo "<pre>"
	.preg_replace(
		'/^Array/', 'cachelab_fitre',
		print_r(array(
			'action'=>$action, 
			'conditions'=>$conditions, 
			'options'=>$options), 1))
	."</pre>";

$stats = cachelab_filtre(
	$action, 
	$conditions,
	$options
);

$l_cible = $stats['l_cible'];
unset($stats['l_cible']);
$l_not_array = $stats['l_not_array'];
unset($stats['l_not_array']);
$l_no_data = $stats['l_no_data'];
unset($stats['l_no_data']);

echo   "<h3>Bilan du filtrage</h3><br>
		<br><b>Stats :</b><pre>    ".trim(str_replace('Array', '', print_r($stats, 1)), "() \n")."</pre>";

function xray_lien_cache ($cle='') {
	$joliecle = substr($cle, strpos($cle,':cache:')+7);
	return "<a href ='/ecrire/index.php?exec=xray&SCOPE=A&COUNT=20&TYPECACHE=ALL&ZOOM=TEXTECOURT&EXTRA=&WHERE=&OB=2&S_KEY=H&SORT=D&SEARCH=$joliecle&SH=".md5($cle)."'>
		$joliecle
	</a>";
}

if (count($l_not_array)) {
	echo "<h3>Erreurs d'accés (pas un tableau)</h3>
		<ul>";
	foreach ($l_not_array as $cle)
		echo "<li>".xray_lien_cache($cle)."</li>";
	echo "</ul>";
}

if (count($l_cible)) {
	echo "<h3>Caches ciblés : ".count($l_cible)."</h3>
		<ul>";
	foreach ($l_cible as $cle)
		echo "<li>".xray_lien_cache($cle)."</li>";
	echo "</ul>";
}
else
	echo "Pas de cache cible<br>";
