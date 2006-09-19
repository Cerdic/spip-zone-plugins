<?php
global $priority;
if (!$priority) $priority=1.0;
global $level;
if (!$level) $level=0;
if (!$GLOBALS['meta']['sitemap_priority_step'])
	$GLOBALS['meta']['sitemap_priority_step']=0.1;


function redirection($chapo){
	$test = preg_match('{\A=}i',$chapo);
	return $test;
}
	
function remonte_date_modif(){
	global $level;
	global $tab_date;
	update($tab_date[$level+1]);
	return "";
	
}
function update($newdate,$newdateredac = '2005-12-01',$offset=0) {
	global $level;
	global $tab_date;
	$ladate = $newdate;
	if ($ladate=="0000-00-00")
		$ladate = $newdateredac;
	if (!isset($tab_date[$level+$offset]))
		$tab_date[$level+$offset]=$ladate;
	else {
		if (strcmp($newdate,$tab_date[$level+$offset])>0)
			$tab_date[$level+$offset]=$ladate;
	}
//echo "level::$level:$newdate::\n";
//foreach ($tab_date as $key=>$value)
//  echo "tab_date[$key]=$value";
	return "";
}
function date_modif($offset=0){
	global $level;
	global $tab_date;
	return $tab_date[$level+$offset];
}

function reduit_priorite($texte){
	global $priority;
	$priority-=$GLOBALS['meta']['sitemap_priority_step'];
	return "";
}
function augmente_priorite($texte){
	global $priority;
	$priority+=$GLOBALS['meta']['sitemap_priority_step'];
	return "";
}
function priorite($texte){
	global $priority;
	return max(0,min(1,$priority));
}

function descend_niveau($texte){
	global $level;
	$level++;
	return "";
}
function remonte_niveau($texte){
	global $level;
	$level--;
	return "";
}
function niveau($texte){
	global $level;
	return max(0,$level);
}

function affiche_xml($void,$charset) {  
  echo "<"."?xml version=\"1.0\" encoding=\"$charset\"?".">\n";
}

?>
