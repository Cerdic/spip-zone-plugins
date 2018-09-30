<p><b>Action ciblée sur le cache</b> : Les arguments supplémentaires de l'url spécifient quelle action doit être appliquée sur quels types de caches. On peut cibler les caches destinataires en indiquant un objet précis contenu dans leur contexte OU grâce au chemin du squelette. Pour spécifier un objet précis, on indique le type d'objet, le nom de la clé et sa valeur. Pour spécifier un chemin de squelette, on peut utiliser 2 méthodes : strpos (par défaut) ou regexp.<br>Les arguments d'url possibles sont : </p>

<small><ul>
<li>action : del, mark, pass</li>
<li>chemin : liste de morceaux de chemins séparés par | , ou expression régulière si methode=regexp</li>
<li>methode : fonction de détection du chemin spécifié : <b>strpos</b> ou regexp</li>
<li>objet : un type d'objet (article, breve, etc) ou XRAY_OBJET_SPECIAL si non spécifié</li>
<li>cle_objet : clé primaire (si différente de 'id_'+objet)</li>
<li>id_article, id_breve, etc selon objet</li>
</ul></small>

<?php
if (isset($_GET['methode']) and $_GET['methode'])
	$cachelab_methode_chemin = $_GET['methode'];
else 
	$cachelab_methode_chemin = 'strpos';

if (isset ($_GET['objet']))
	$objet = $_GET['objet'];
elseif (defined ('XRAY_OBJET_SPECIAL') and XRAY_OBJET_SPECIAL)
	$objet = XRAY_OBJET_SPECIAL;
else 
	$objet = null;

if (isset ($_GET['cle_objet']))
	$cle_objet = $_GET['cle_objet'];
elseif ($objet)
	$cle_objet = 'id_'.$objet;	// TODO spiper
else 
	$cle_objet = '';

if ($cle_objet and isset ($_GET[$cle_objet]))
	$id_objet = $_GET[$cle_objet];
elseif (defined ('XRAY_ID_OBJET_SPECIAL'))
	$id_objet = XRAY_ID_OBJET_SPECIAL;
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
	$action = 'pass';

if ($cle_objet and !$id_objet)
	die ("$cle_objet est inconnu : passez le en argument d'url ou définissez XRAY_ID_OBJET_SPECIAL en php");

if (isset ($_GET['chemin']))
	$chemin = $_GET['chemin'];

echo "<b>Choix actuels :</b><br>
		Action : $action<br>
		Méthode pour la recherche des chemins : $cachelab_methode_chemin<br>
		chemin recherché : $chemin<br>
		objet recherché = $cle_objet $id_objet<br>
		globale derniere_modif_invalide : {$GLOBALS['derniere_modif_invalide']}";

$stats = cachelab_filtre(
	$action, 
	array('chemin'=>$chemin, 'cle_objet'=>$cle_objet, 'id_objet'=>$id_objet),
	array('chrono'=>true, 'listes'=>true, 'methode_chemin'=>$cachelab_methode_chemin)
	);

$liste_matche_chemin = $stats['liste_matche_chemin'];
unset($stats['liste_matche_chemin']);
$liste_cible = $stats['liste_cible'];
unset($stats['liste_cible']);
$liste_data_not_array = $stats['liste_data_not_array'];
unset($stats['liste_data_not_array']);

// list ($listeobjet, $listechemin) = filtre_cache('', 1, $chemin);

echo "<h3>Bilan du filtrage</h3><br>
		Caches trouvés avec le chemin $chemin : ".count($listechemin)."<br>
		Caches trouvés avec <a href='$url_objet'>$objet $id_objet</a> : ".count($listeobjet)."</b><br>
		<br><b>Stats :</b><pre>    ".trim(str_replace('Array', '', print_r($stats, 1)), "() \n")."</pre>";

if (count($liste_data_not_array)) {
	echo "<h3>Erreurs d'accés (pas un tableau)</h3><ul>";
	foreach ($liste_data_not_array as $cle)
		echo "<li>$cle</li>";
	echo "</ul>";
}

if ($cle_objet) {
	echo "</ul>
		<h3>Cible avec chemin $chemin et contexte avec $objet=$id_objet : ".count($liste_cible)."</h3>
		<ul>";
	foreach ($liste_cible as $d)
		echo "	<li>{$d['info']}</li>";
	echo "</ul>";
};

echo "<h3>Caches trouvés avec le chemin $chemin : ".count($liste_matche_chemin)."</h3>
	<ul>";
foreach ($liste_matche_chemin as $d)
	echo "	<li>{$d['info']}</li>";
