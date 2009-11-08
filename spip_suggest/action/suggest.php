<?php
define('_MYSQL_DIR', '/var/lib/mysql/');
$services = array('mu_courskry83de' => array('spip_articles' => array('num' => 7)));
if (!isset($services[$s = @$_GET['s']]) OR !isset($services[$s][$t = @$_GET['t']])) {
	die ('Usage: ?s=service&t=table');
}
$num = $services[$s][$t]['num'];
# query a nettoyer (charset...)
$q = @$_GET['q'];

exec($c = 'myisam_suggest '._MYSQL_DIR.$s.'/'.$t.' '.$num.' '.escapeshellarg($q), $lines);
$res = array();
print_r($lines);
foreach (array_map('strtolower',$lines) as $line)
	$res[trim($line)]++;
echo json_encode($res);
?>