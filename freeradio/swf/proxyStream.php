<?php
/* ------------------------------------------ */
/* Franck Ruzzin : 12 mai 2011
/* ------------------------------------------ */

// Récupération de l'URL
// POST ou GET ?
$url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
extractStream($url);

function extractStream($url) 
{
	$url_stuff = parse_url($url);
    $port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;
	$fp = fsockopen($url_stuff['host'], $port);
	if (!$fp) {
        echo $url;
	} else {
		$query  = 'GET ' . $url_stuff['path'];
		if ($url_stuff['query']) $query .= '?' . $url_stuff['query'];
		$query.=" HTTP/1.1\r\n";
		$query .= 'Host: '.$url_stuff['host']."\r\n";
		$query .= 'Connection: Close'."\r\n";
		$query .= "\r\n";
		fwrite($fp, $query);
		
		$buffer="";
		while (!feof($fp)&&strlen($buffer)<10240) {
			$read = fgets($fp, 4096);
			$buffer .= $read;
		}
		fclose($fp);
		// on récupère des données de description
		if (strlen($buffer)<10240)
		{
			//Redirection ?
			if (isRedir($buffer))
				extractStream(getLocation($buffer));
			else
				echo $buffer;
		}
		// on récupère un flux (directement le streaming)
		else
			echo $url;
	}
}

function isRedir($buf)
{
	$result=false;
	$lignes = explode("\r\n", $buf);
	foreach ($lignes as $ligne) {
		$pos = strpos(strtolower($ligne), "301 moved permanently");
		if ($pos !== false) {
			$result=true;
			break;
		}
	}
	return $result;
}

function getLocation($buf)
{
	$result="";
	$lignes = explode("\r\n", $buf);
	foreach ($lignes as $ligne) {
		$pos = strpos(strtolower($ligne), "location:");
		if ($pos === 0) {
			$result = substr($ligne,10);
			break;
		}
	}
	return $result;
}


?> 

