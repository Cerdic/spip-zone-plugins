<h3>Essais "manuels" de cachelab</h3>
<p><b>Action ciblée sur le cache</b> : cette page permet d'essayer cachelab "à la main". Ç'a été utile pendant la mise au point de cachelab.<br>
Les arguments supplémentaires de l'url spécifient quelle action doit être appliquée sur quels caches.<br>
Ci après, les valeurs en gras sont les valeurs par défaut.
<small><ul>
<li>action : del, mark, pass, <b>list</b></li>
<li>chemin : liste de morceaux de chemins séparés par | , ou expression régulière si methode=regexp</li>
<li>methode : fonction de détection du chemin spécifié : <b>strpos</b> ou regexp ou equal (paramètre de cachelab_cibler : <i>methode_chemin</i>)</li>
<li>partie_chemin : <b>tout</b> ou dossier ou fichier : la partie du chemin qui est testée</li>
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

$chemin = (isset ($_GET['chemin']) ?$_GET['chemin'] : '');

if (isset($_GET['partie_chemin']) and $_GET['partie_chemin'])
	$cachelab_partie_chemin = $_GET['partie_chemin'];
else
	$cachelab_partie_chemin = 'tout';


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
$options = array('list'=>true, 'methode_chemin'=>$cachelab_methode_chemin, 'partie_chemin'=>$cachelab_partie_chemin);

echo "<pre>"
	.preg_replace(
		'/^Array/', 'cachelab_cibler',
		print_r(array(
			'action'=>$action,
			'conditions'=>$conditions,
			'options'=>$options), 1))
	."</pre>";

$stats = cachelab_cibler(
	$action,
	$conditions,
	$options
);

$l_cible = $stats['l_cible'];
unset($stats['l_cible']);

echo   "<h3>Bilan du filtrage</h3><br>
		<br><b>Stats :</b><pre>    ".trim(str_replace('Array', '', print_r($stats, 1)), "() \n")."</pre>";

function xray_lien_cache ($cle='') {
	$joliecle = substr($cle, strpos($cle,':cache:')+7);
	return "<a href ='/ecrire/index.php?exec=xray&SCOPE=A&COUNT=20&TYPECACHE=ALL&ZOOM=TEXTECOURT&EXTRA=&WHERE=&OB=2&S_KEY=H&SORT=D&SEARCH=$joliecle&SH=".md5($cle)."'>
		$joliecle
	</a>";
}

if (count($l_cible)) {
	echo "<h3>Caches ciblés : ".count($l_cible)."</h3>
		<ul>";
	global $Memoization;
	echo '_CACHE_NAMESPACE : '._CACHE_NAMESPACE;
	foreach ($l_cible as $cle)  {
		echo "<li>".xray_lien_cache($cle);
		$cle_sans_ = rtrim($cle, '_');
		if ($cle_sans_ != $cle) {
			$clememo_ = substr($cle,strlen(_CACHE_NAMESPACE));
			$clememo = rtrim($clememo_,'_');
			echo " (sans _ : ".xray_lien_cache($cle_sans_).") ";
			$v_ = $Memoization->get($clememo_);
			if (!$v_)
				echo " (v_ vide) ";
			$v = $Memoization->get($clememo);
			if (!$v)
				echo " (v vide) ";
		}
		echo "</li>";
	}
	echo "</ul>";
}
else
	echo "Pas de cache cible<br>";
))
