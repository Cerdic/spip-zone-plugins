<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Logs.functions.php,v 1.29 2006/01/04 22:32:46 matthieu_ Exp $



/**
* returns the hostname of the $ip
* 
* @param string $ip
* 
* @return string hostname
*/
function getHost($ip)
{
	return trim(strtolower(@gethostbyaddr($ip)));
}

/**
* returns an ip which can be resolved, if possible
* 
* @return string ip 
*/
function getIp() 
{
	if(isset($_SERVER['HTTP_CLIENT_IP']) 
		&& ($ip = getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']))
		&& strpos($ip, "unknown")===false 
		&& getHost($ip) != $ip)
	{
		return $ip;
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
		&& $ip = getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR'])
		&& isset($ip) && !empty($ip)
		&& strpos($ip, "unknown")===false 
		&& getHost($ip) != $ip)
	{
		return $ip;
	}
	elseif( isset($_SERVER['HTTP_CLIENT_IP'])
		&& strlen( getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']) ) != 0 )
	{
		return getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']);
	}
	else if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
		&& strlen (getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR'])) != 0)
	{
		return getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	else
	{
		return getFirstIpFromList($_SERVER['REMOTE_ADDR']);
	}
}

/**
* returns first element of a comma separated list
* 
* @param string $ip
* 
* @return string first element before ','
*/
function getFirstIpFromList($ip)
{
	$p = strpos($ip, ',');
	if($p!==false)
	{
		return secureVar(substr($ip, 0, $p));
	}
	else
	{
		return secureVar($ip);
	}
}

/**
* get the visitor os
* 
* @param string $userAgent
* @param array $osList
* 
* @return string 
*/
function getOs($userAgent)
{
	for (@reset($GLOBALS['osNameToId']), $ok = false; 
		(list($key, $value) = @each($GLOBALS['osNameToId'])) && !$ok;)
	{
		if ($ok = ereg($key, $userAgent))
		{
			return $value;
		}
	}
	return 'UNK';
}

/**
* returns continent of $country 
* 
* @param string $country isocode
* @param array $coutryList
* 
* @return string continent (3 letters)
*/
function getContinent($country)
{
	if(isset($GLOBALS['countryList'][$country][0]))
	{
		return $GLOBALS['countryList'][$country][0];
	}
	else
	{
		return "unk";
	}
}

/**
* get visitor country with both information : Hostname and BrowserLang
* 
* @param string $host
* @param string $lang browser lang
* 
* @return string 
*/
function getCountry($host, $lang)
{
	// look for an existing domain
	$domain = substr($host, strrpos($host, '.') + 1);
	
	// if domain doesn't exist
	if(strlen($domain) != 2)
	{
		// look for the the last but one extension Ex : "ca" in ".ca.com"
		$host = substr($host, 0, strrpos($host, '.') );
		$domain = substr($host, strrpos($host, '.'));
	}
	
	if(isset($GLOBALS['countryList'][$domain][0]))
	{
		return $domain;
	}
	else
	{
		// try with the browser langage code
		
		// when comma 'fr, en-us', keep first two chars
		
		// replace cs with cz code
		$offcomma = strpos(str_replace('cs', 'cz', $lang), ',');
	
		if($offcomma == 2)
		{
			$domain =  substr( $lang , 0, 2);
			if(isset($GLOBALS['countryList'][$domain][0]))
			{
				return $domain;
			}
		}

		// detect second code Ex: "be" in "fr-be"
		$off = strpos($lang, '-');
		if($off!==false)
		{
			$domain = substr($lang, $off+1, 2);
			
			if(isset($GLOBALS['countryList'][$domain][0]))
			{
				return $domain;
			}
		}
		
		// catch the first extension Ex: "fr" in "fr,ar-ma"
		$domain = substr($lang, 0, 2);
		
		if(isset($GLOBALS['countryList'][$domain][0]))
		{
			return $domain;
		}
	}
	
	// unfortunately... unknown !
	return "xx";
}

/**
* returns valid phpmv hostname extension (site.co.jp in fvae.VARG.ceaga.site.co.jp)
* from the complete hostname
* 
* @param string $hostname
* 
* @return string
*/
function getHostnameExt($hostname)
{
	$extToExclude = array(
		'com', 'net', 'org', 'co', 'co'
	);
	
	$off = strrpos($hostname, '.');
	$ext = substr($hostname, $off);

	if(empty($off) || is_numeric($ext) || strlen($hostname) < 5)
	{
		return 'Ip';
	}
	else
	{
		$e = explode('.', $hostname);
		$s = sizeof($e);
		
		// if extension not correct
		if(isset($e[$s-2]) && in_array($e[$s-2], $extToExclude))
		{
			return $e[$s-3].".".$e[$s-2].".".$e[$s-1];
		}
		else
		{
			return $e[$s-2].".".$e[$s-1];
		}
	}
}

/**
* get visitor browser 
* 
* @param string $userAgent
*/
function getBrowserInfo($userAgent)
{
	$browser = '';
	
	$info = array(
	'shortName' => 'unk',
	'longName' => '',
	'major_number' => '',
	'minor_number' => '',
	'version' => ''
	);
	
	foreach($GLOBALS['browsers'] as $key => $value) 
	{
		if(!empty($browser)) $browser .= "|";
		$browser .= $key;
	}
	
	$results = array();
	
	// added fix for seamonkey detection
	if ((preg_match_all("/(mozilla)[\/\sa-z;.0-9-(]+rv:([0-9]+)([.0-9a-z]+)\) gecko\/[0-9]{8}$/i", $userAgent, $results)) 
	||	(preg_match_all("/($browser)[\/\sa-z(]*([0-9]+)([\.0-9a-z]+)?/i", $userAgent, $results))
		)
	 {
		$count = count($results[0])-1;
		
		// browser code
		$info['shortName'] = $GLOBALS['browsers'][strtolower($results[1][$count])];
		$info['longName'] = $GLOBALS['browsers'][strtolower($results[1][$count])];
		
		// majeur version number (7 in mozilla 1.7
		$info['major_number'] = $results[2][$count];
		
		// is an minor version number ? If not, 0
		$match = array();
		
		preg_match('/([.\0-9]+)?([\.a-z0-9]+)?/i', $results[3][$count], $match);
		
		if(isset($match[1])) 
		{
			// find minor version number (7 in mozilla 1.7, 9 in firefox 0.9.3)
			$info['minor_number'] = substr($match[1], 0, 2);
		} 
		else 
		{
			$info['minor_number'] = '.0';
		}
		
		$info['version'] = $info['major_number'] . $info['minor_number'];
	}	
	return $info;	
}


/**
* records in the DB a category, and recursively records its parent if not saved before
* 
* @param string $categoryName complete name of the category to record. Ex : "g1>g2>g3" which parent is "g1>g2"
* 
* @return int idparent of the category saved
*/
function recordDbCategory($categoryName)
{
	// print("<br>$categoryName</br>");
	$level = substr_count($categoryName, CATEGORY_DELIMITER) + 1;
	printDebug("in category $categoryName := level : $level <br>".CATEGORY_DELIMITER);
	
	// "g3"
	$off = strrpos($categoryName, CATEGORY_DELIMITER);
	
	// adjust offset, if no '>' detected, we must begin at offset 0. 
	// If '>' detected, begin at offset+1
	($off===false) ? $off=0 : $off+=1;
	
	$pageCategory = substr($categoryName, $off );
	// print("pageCategory : $pageCategory <br>");																				      
	
	// "g1>g2"
	$parentName = substr($categoryName, 0,  strrpos($categoryName, CATEGORY_DELIMITER) );
	$parentLevel = $level - 1 ;
	
	// print("parentName : $parentName <br>");
	
	if($parentName === '')
	{
		$idParent = 0;
	}
	else
	{
		// try to select idParent category
		$r = query("SELECT idcategory
		FROM ".T_CATEGORY."
		WHERE complete_name = '$parentName'
		AND level = '$parentLevel'
		LIMIT 1");
		
		if(mysql_num_rows($r)>0)
		{
			$r = mysql_fetch_assoc($r);
			$idParent = $r['idcategory'];
		}
		else if($level == 1)
		{
			// save a new row with category info
			$r = query("INSERT INTO ".T_CATEGORY." 
						(complete_name, name, level, idparent)
						VALUES ('$categoryName', '$pageCategory', '1', '0')
					");
			
			return mysql_insert_id();		
		}
		else 
		{
			// saves the parent category name
			$idParent = recordDbCategory($parentName);
		}
	}	
	// save a new row with category info and idparent
	$r = query("INSERT INTO ".T_CATEGORY." 
				(complete_name, name, level, idparent)
				VALUES ('$categoryName', '$pageCategory', '$level', '$idParent')
		");
	
	return mysql_insert_id();
}

/**
* records the table 'page' if necessary and
* create links to the page URL, links to the page category
* 
* @param string $pageName 
* @param string $pageUrl complete page Url
* @param string $pageCategory "g1>g2>g3>g4". No category is string '' and has idCategory = 0
* 
* @return array (idpage of the page saved, idpageurl of the url of the page saved)
*/
function recordDbPage($pageName, $pageUrl, $pageCategory)
{
	if(strcmp($pageCategory,'')===0 || empty($pageCategory) || $pageCategory == '')
	{
		$categoryId = 0;
	}
	else
	{
		// select the category
		$r = query("SELECT idcategory
		FROM ".T_CATEGORY."
		WHERE complete_name = '$pageCategory'
		LIMIT 1
		");
		
		if(mysql_num_rows($r)==0)
		{
			// save category
			$categoryId = recordDbCategory($pageCategory);
		}
		else
		{
			// category id
			$r = mysql_fetch_assoc($r);
			$categoryId = $r['idcategory'];
		}
	}
	// try to select page
	$r = query("SELECT idpage
				FROM ".T_PAGE."
				WHERE idcategory = '$categoryId'
				AND name = '$pageName'
				LIMIT 1
			");
	
	if(mysql_num_rows($r)==0)
	{
		// saves page name & idcategory
		$r = query("INSERT INTO ".T_PAGE." 
					(idcategory, name)
					VALUES ('$categoryId', '$pageName')
					");
		$idPage = mysql_insert_id();
	}
	else
	{
		$r = mysql_fetch_assoc($r);
		$idPage = $r['idpage'];
	}
	
	$idPageUrl = recordDbPageUrl($pageUrl, $idPage);
	
	return array($idPage, $idPageUrl);
}

/**
* saves in the DB the pageUrl of the page $idPage, if necessary
* 
* @param string $pageUrl
* @param int $idPage
* 
* @return bool
*/
function recordDbPageUrl($pageUrl, $idPage)
{
	$r = query("SELECT idpage_url as id
				FROM ".T_PAGE_MD5URL."
				WHERE idpage = '$idPage'
				AND md5url = '".md5($pageUrl)."'
				LIMIT 1
	");
	
	if(mysql_num_rows($r)==0)
	{
	/*	// search for the whole URL if already exists
			$r2 = query("SELECT idpage_url as id
				FROM ".T_PAGE_URL."
				WHERE url = '$pageUrl'
				LIMIT 1
		");
		if(mysql_num_rows($r2)==0)
		{
		*/	
		// saves text URL
		$r3 = query("INSERT INTO ".T_PAGE_URL." (url)
					VALUES ('$pageUrl')");
		$idInserted = mysql_insert_id();
		
		/*}
		else
		{
			$l= mysql_fetch_assoc($r2);
			$idInserted = $l['id'];
		}
		*/
		
		// saves URL and links to table 'page'
		$r = query("INSERT INTO ".T_PAGE_MD5URL." (idpage, md5url, idpage_url)
					VALUES ('$idPage', '".md5($pageUrl)."', '$idInserted')
					");
		return $idInserted;
	}
	
	$l = mysql_fetch_assoc($r);
	return $l['id'];
}

/**
* saves a variable value & name if not saved before
* and create the link in table 'link_vpv' 
* 
* @param string $varName
* @param string|int $varValue
* @param int $idLink_vp
* 
* @return bool
*/
function recordDbVars($varName, $varValue, $idLink_vp)
{
	// detect type of the variable, which determines where to store the value :
	// because there are 2 fields : int_value for int values and varchar_value for string values
	if(is_numeric($varValue))
	{
		$field = 'int_value';
		$field2 = 'varchar_value';
	}
	else
	{
		$field = 'varchar_value';
		$field2 = 'int_value';
	}
	
	// try to select vars id
	$r = query("SELECT idvars
				FROM ".T_VARS."
				WHERE name = '$varName'
				AND $field = '$varValue'
				LIMIT 1
	");
	
	if(mysql_num_rows($r)==0)
	{
		// create var name + value
		$r = query("INSERT INTO ".T_VARS." (name, $field)
					VALUES ('$varName', '$varValue')
					");
		$idVars = mysql_insert_id();
		
	}
	else
	{
		$r = mysql_fetch_assoc($r);
		$idVars = $r['idvars'];
	}
	
	$r = query("INSERT INTO ".T_LINK_VPV." (idlink_vp, idvars)" .
	" VALUES ('$idLink_vp', '$idVars')");
	
	return true;
}

/**
* update the table link_vp
* 
* create the field if necessary with idvisit, idpage, ...
* or update it if previously saved
* 
* @param int $idVisit
* @param int $idPage
* @param int $idPageRef
* @param int $totalTime
* 
* @return int $idLink_vp
*/
function recordDbLink_vp($idVisit, $idPage, $idPageRef, $totalTime) 
{
	$r = query("INSERT INTO ".T_LINK_VP." (idvisit, idpage, idpage_ref, total_time_page_ref)" .
	" VALUES ('$idVisit', '$idPage', '$idPageRef', '$totalTime')");
	$idLink_vp = mysql_insert_id();
	
	return $idLink_vp;	
}

/**
* saves all info concerning page & variables 
* 
* @param int $idVisit
* @param int $idPage
* @param int $idPageRef idpageref or 0 if page referer not found
* @param int $timeDiffRef
* @param array $a_vars contains variables names and values array( 'name' => 'value', ...)
* 
* @return array contains $idLink_vp created and $idPage used (and maybe created if page was not recorded before)
*/
function recordDbInfoPage($idVisit, $idPage, $idPageRef, $timeDiffRef, $a_vars)
{
	$idLink_vp = recordDbLink_vp($idVisit, $idPage, $idPageRef, $timeDiffRef);
	
	printDebug("===> Page info saved (idPage = ".$idPage.", idLink_vp = ".$idLink_vp.") <br>");
	
	// if there are variables to record
	if(is_array($a_vars))
	{
		foreach ($a_vars as $name => $value) 
		{
			recordDbVars($name, $value, $idLink_vp);
		}
	}
	
	return $idLink_vp;
}

/**
* saves path in database
* 
* @param int $idVisit
* @param int $idPageRef
* @param int $idPage
* 
* @return bool true
*/
function recordDbPath($idVisit, $idPageRef, $idPage)
{
	// set id numbers to a correct size... 5 characters (idPage max is 99999)
	$idPage = setToLength($idPage, 5);
	$idPageRef = setToLength($idPageRef, 5);
	
	printDebug("===> Update of the path, adding $idPage after $idPageRef for visit $idVisit...<br>");
	
	// TODO enlever le substring!
	$r = query("UPDATE ".T_PATH.
	" SET sequence = CONCAT(sequence, ';$idPage')" .
	" WHERE idvisit = '$idVisit'" .
	" AND SUBSTRING_INDEX(sequence, ';', -1) = '$idPageRef'" .
	" LIMIT 1");
	
	if(mysql_affected_rows() > 0)
	{
		printDebug("=====> Direct path update, fantastic<br>");
	}
	else
	{
		$r = query("INSERT INTO ".T_PATH." (idvisit, sequence)" .
		" VALUES ('$idVisit', '$idPageRef;$idPage')");
		printDebug("=====> Path updated with an INSERT of the idpageref, because it was not found in an existing path<br>");
	}
	
	return true;
}

/**
* load image of phpmyvisites logo and calls performance log and footer display
* 
* @param string $logo name of the logo '1.png'
* @param int $idSite
* 
* @return exit
*/
function loadImage($logo='pixel.gif', $idSite)
{
	if(empty($logo)) $logo = 'pixel.gif';
	
	$img = INCLUDE_PATH."/images/logos/".$logo;
	if(file_exists($img) && !DEBUG)
	{
		header("Content-type: image/".substr($logo, -3, 3));
		readfile($img);
		if(!PROFILING)
		{
			exit();
		}
	}
	else
	{
		printDebug("<hr><b>IMAGE_HERE</b><hr>");
	}
	return;
}

/**
* exit the script of the ip is recorded as an ip to exclude
* or if the ip is in any range of ip to exclude for this site
* 
* @param int $ip2long ip2long version of ip
* @param string $logo logo name '1.png' necessary if we exit the script
* @param int $idSite
* 
* @return true or calls loadImage() and exit
*/
function exitIfIpExcluded($ip2long, $logo, &$o_site)
{
	printDebug("ip:".$ip2long);
	
	// look into the cookie for $a_ipToIgnore
	$a_ipToIgnore = $o_site->getIpArray();
	
	if(isset($a_ipToIgnore) 
	&& sizeof($a_ipToIgnore) >= 2)
	{
		$i = 0;
		// is the ip excludable ?
		while($i < sizeof($a_ipToIgnore))
		{
			$ip1 = $a_ipToIgnore[$i][0];
			$ip2 = $a_ipToIgnore[$i][1];

			if($ip2long >= $ip1 && $ip2long <= $ip2
				|| $ip2long == $ip1
				|| $ip2long == $ip2 )
			{
				printDebug("<b>=> Ip is excluded, exit</b><br>");
				loadImage($logo, $o_site->getId());
			}	
			$i++;
		}
	}
	
	printDebug("<b>=> Ip is not excluded</b><br>");
	return true;
}

/**
* process urls removing params in siteInfo following information contained in $siteInfo
* 
* @param string $url url, even imcomplete
* @param array $siteInfo array('params_choice' => 'all|except|only|none', 'params_exlude' => 'p1;p2', params_include_only => 'p1;p2' )
* 
* @return string url processed
*/
function processParams($url, $siteInfo)
{
	$parseUrl = parse_url($url);
	
	$a_varsToExclude = explode(';', $siteInfo['params_names']);
	$a_varsToInclude = explode(';', $siteInfo['params_names']);
	$choice = $siteInfo['params_choice'];

	// add PHPSESSID
	$a_toExclude[] = 'PHPSESSID';
	$a_toExclude[] = 'phpsessid';
	$a_toExclude[] = ''; // exclude empty var "&=&toto..."
	
	$a_varsToExclude = array_merge( $a_varsToExclude, $a_toExclude);
	
	if($choice == 'none')
	{
		$toReturn = isset($parseUrl['path'])?$parseUrl['path']:'';
	}
	else if(isset($parseUrl['query']))
	{
		$queryEx = explode('&amp;', $parseUrl['query']);
		$return = $parseUrl['path'] . '?';

		foreach($queryEx as $value)
		{
			$varAndValue = explode('=', $value);
		
			// include only parameters
			if( sizeof($varAndValue) == 2
				&& 
				(
					($choice == 'only' && in_array($varAndValue[0], $a_varsToInclude))
					||
					($choice == 'except' && !in_array($varAndValue[0], $a_varsToExclude))
					||
					($choice == 'all' && !in_array($varAndValue[0], $a_toExclude))
				)
			)
			{
				$return .= $varAndValue[0].'='.$varAndValue[1].'&amp;';
				
			}
		}
		
		if(substr($return, strlen($return)-5) == '&amp;')
		{
			$toReturn = substr($return, 0, strlen($return)-5);
		}
		else if(substr($return, strlen($return)-1) == '?')
		{
			$toReturn = substr($return, 0, strlen($return)-1);
		}
	}
	else
	{
		$toReturn = $url;
	}
	
	return $toReturn;
}

function displayPageWhenEmptyGet()
{
	print("<html>
			<head>
				<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
				<meta name=\"description\" content=\"phpMyVisites | Application de statistiques et de mesure d'audience de sites Internet | Logiciel gratuit et open source distribué sous licence GPL, développé en php/MySQL\" />
				<meta name=\"copyright\" content=\"phpMyVisites\" />
				<title>Websites statistics for ".Request::getCurrentCompletePath()."</title>
			</head>
			<body>");
	
	print("<h1>".$GLOBALS['lang']['head_titre']."</h1>");
	print("<p>".Request::getCurrentCompleteUrl()." have chosen phpMyVisites</p>");
	print("<p>".$GLOBALS['lang']['lang_libelle_en']
			 ."</p><p><a href='http://www.phpmyvisites.net/'>".$GLOBALS['lang']['head_description'] 
			 ."</a></p>
			 <p>Today's date is ".date('r')."</p>");
	
	displayLang();
	
	print("<h2>Citation</h2>");
	print("<p>You're allways right. By using phpMyVisites, you're just even more right than usually.</p>");
	
	displayLang();
	print("<h2>Who is behind phpMyVisites?</h2>");
	print("<p>".$GLOBALS['lang']['contacts_auteur']."</p><p>".$GLOBALS['lang']['contacts_merci3']."</p>");
	print("<p>".Request::getCurrentCompleteUrl()." have chosen phpMyVisites</p>");
	
	displayLang();
	print("<h1>Is phpmyvisites THE solution?</h1>");
	print(" <p>Free software is good for you!</p> <a href='http://www.phpmyvisites.net/'>Web Analytics</a>
			<br>
			<a href='http://www.phpmyvisites.net/'>Web websites statistics</a>");
	
	displayLang();
	print("<p>See statistics (if they are not protected by phpMyVisites administrator) for <a href=\"".Request::getCurrentCompletePath()."\">".Request::getCurrentCompletePath()."</a> </p>");
	print("<p>Today's date is also ".date('l dS \of F Y h:i A')."</p>");
	
	displayLang();
	
	print("</body></html>");
}

function displayLang()
{
	$keys = array_keys($GLOBALS['lang']);
	$stop = rand( 3, 20);
	$skeys = sizeof($keys);
	for ($i = 0; $i < $stop ; $i++)
	{
		$str = @$GLOBALS['lang'][$keys[rand(1,$skeys-1)]];
		if(is_string($str) && !ereg('%', $str)) print($str);
		print("<br>");		
	}
}

?>