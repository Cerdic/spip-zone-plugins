<?php

function exec_vhosts_config() {
	global $vhosts;

	if ($GLOBALS['connect_statut'] != '0minirezo') {
		exit;
	}

	include_spip('inc/presentation');

	$i=1;
	while(_request("url$i")) {
		if(_request("host$i")) {
			$vhosts[_request("url$i")]= _request("host$i");
		} else {
			unset($vhosts[_request("url$i")]);
		}
		$i++;		
	}
	if($i!=1) {
		$vhostsSerial= serialize($vhosts);
		ecrire_fichier(_DIR_SESSIONS."vhosts.txt",$vhostsSerial);
	}

	debut_page("Virtual Hosts", "vhosts", "vhosts_config");

	echo "<form method='post'>\n";
	echo "<table><tr>\n";
	echo "  <th>url du site</th>\n";
	echo "  <th>vhost associ&eacute;</th>\n";
	echo "</tr>\n";

	$i=1;
	foreach($vhosts as $url => $host) {
		echo "<tr>\n";
		echo "  <td><input length='20' name='url$i' value='$url'></td>\n";
		echo "  <td><input length='12' name='host$i' value='$host'></td>\n";
		echo "</tr>\n";
		$i++;
	}
	echo "<b>Ajouter des entr&eacute;es</b><br/>\n";
	for($j=0; $j<5; $j++) {
		echo "<tr>\n";
		echo "  <td><input length='20' name='url$i' value=''></td>\n";
		echo "  <td><input length='12' name='host$i' value=''></td>\n";
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";
	echo "<input type='submit' value='valider'>\n";
	echo "</form>\n";

	fin_page();

	//	ecrire_fichier(_DIR_SESSIONS."phpmvconfig/site_urls.php",$conf);
}
