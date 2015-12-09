<?php

header('Content-Type: text/plain');

echo time()."\n";

echo "lock::you=".var_export(cache_get('lock::test'), 1).";\n";
echo "test=".var_export(cache_get('test'), 1).";\n";


$a = null;

switch ($_GET['o']) {
	case 'unknown':
		$a = cache_get(uniqid(''));
		break;

	case 'unset':
		$a = cache_unset('test');
		break;

	case 'unsetlock':
		$a = cache_unset('lock::test');
		break;

	case 'lock':
		flush();
		$a = cache_lock('test');
		flush();
		echo "sleep: ".time()."\n";
		sleep($_GET['sleep']);
		break;

	case 'inc':
		$a = cache_inc('lock::test');
		break;

	case 'dec':
		$a = cache_dec('lock::test');
		break;

	case 'unlock':
		$a = cache_unlock('test');
		break;


}

echo "\$a = ".var_export($a, true).";\n";

echo "lock::test=".var_export(cache_get('lock::test'), 1).";\n";
echo "test=".var_export(cache_get('test'), 1).";\n";


echo time();

exit;