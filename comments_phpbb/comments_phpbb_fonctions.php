<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function datetime_mysql2unix($str) {
	/// convert from MySQL to UNIX timestamp	
	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);

	$timestamp = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);

	return $timestamp;
}

function datetime_unix2mysql($timestamp) {
	/// convert from UNIX to MySQL timestamp
	return date('Y-m-d H:i:s', $timestamp);
}

function bbcode($chaine) {
/*
 * Source originale : Plugin "Du BBCode dans SPIP
 * https://plugins.spip.net/Du-BBcode-dans-SPIP
 * Modifiée par David Dorchies http://dorch.fr 13/07/2009
 */
  $chaine = stripslashes($chaine);
  $chaine = str_replace("[code]","<html><code>",$chaine);	
  $chaine = str_replace("[/code]","</code></html>",$chaine);
  $chaine = preg_replace("!\\[url\\]\\[img\\](.+)\\[/img\\]\\[/url\\]!Umi","<html><a href=\"\\1\" title=\"img\"><img src=\"\\1\" alt=\"img\"/></a></html>",$chaine);
  $chaine = preg_replace("!\\[url=(.+)\\]\\[img\\](.+)\\[/img\\]\\[/url\\]!Umi","<html><a href=\"\\1\" title=\"img\"><img src=\"\\2\" alt=\"img\"/></a></html>",$chaine);
  $chaine = preg_replace("!\\[url\\](.+)\\[/url\\]!Umi","<html><a href=\"\\1\" title=\"\\1\">\\1</a></html>",$chaine);
  $chaine = preg_replace("!\\[url=(.+)\\](.+)\\[/url\\]!Umi","<html><a href=\"\\1\" title=\"\\2\">\\2</a></html>",$chaine);
  $chaine = preg_replace("!\\[email\\](.+)\\[/email\\]!Umi","<html><a href=mailto:\"\\1\">\\1</a></html>",$chaine);
  $chaine = preg_replace("!\\[email=(.+)\\](.+)\\[/email\\]!Umi","<html><a href=mailto:\"\\1\">\\2</a></html>",$chaine);
  $chaine = preg_replace("!\\[color=(.+)\\](.+)\\[/color\\]!Umi","<html><span style=\"color:\\1\">\\2</span></html>",$chaine);
  $chaine = preg_replace("!\\[size=(.+)\\](.+)\\[/size\\]!Umi","<html><span style=\"font-size:\\1px\">\\2</span></html>",$chaine);
  $chaine = preg_replace("!\[list\](.+)\[/list\]!Umi","<html><ul> \\1 </ul></html>",$chaine);
  $chaine = preg_replace("!\[list=1\](.+)\[/list\]!Umi","<html><ol> \\1 </ol></html>",$chaine);  
  $chaine = preg_replace("!\[list=a\](.+)\[/list\]!Umi","<html><ol type='a'> \\1 </ol></html>",$chaine);
  $chaine = preg_replace("!\[\*\](.+)(?=(\[\*\]|</ul>))!Umi","<li>\\1</li>",$chaine);
  $chaine = preg_replace("!\\[b.*\\](.*)\\[/b.*\\]!Umi","{{\\1}}",$chaine);
  $chaine = preg_replace("!\\[i.*\\](.*)\\[/i.*\\]!Umi","{\\1}",$chaine);
  $chaine = preg_replace("!\\[u.*\\](.*)\\[/u.*\\]!Umi","<html><span style='text-decoration:underline;'>\\1</span></html>",$chaine);
  $chaine = preg_replace("!\\[center.*\\](.*)\\[/center.*\\]!Umi","<html><center>\\1</center></html>",$chaine);
  $chaine = preg_replace("!\\[img.*\\](.*)\\[/img.*\\]!Umi","<html><img src=\"\\1\" alt=\"img\" /></html>",$chaine);
  $chaine = preg_replace("!\\[quote=\"(.*)\\\".*\\](.*)\\[/quote.*\\]!Umi","<html><quote>\\1 a &eacute;crit<br/>\\2</quote></html>",$chaine);
  $chaine = preg_replace("!\\[quote.*\\](.*)\\[/quote.*\\]!Umi","<html><quote>\\1</quote></html>",$chaine);
  $chaine = str_replace("[scroll]","<cadre>",$chaine);	
  $chaine = str_replace("[/scroll]","</cadre>",$chaine);
  $chaine = str_replace("\n","\n\n",$chaine);
  
  return propre($chaine);
}

?>
