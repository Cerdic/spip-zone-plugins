<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 3                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

define('FORMAT_JSON', 'json');
define('FORMAT_XML', 'xml');

function exists($id_objet, $objet){
	if (sql_countsel('spip_'.$objet.'s', "id_".$objet."=".intval($id_objet)))
		return true;
	return false;
}

/**
 *
 * Remplace les valeurs du tableau de type Array par la valeur de substitution (null par defaut)
 * @param $arr
 * @param $substitueValue
 */
function secureArrayFields($arr, $substitueValue=null){
	if ($arr){
		foreach ($arr as $k => $v){
			if (is_array($v))	$arr[$k] = $substitueValue;
		}
	}
	return $arr;
}

/**
 * transforme un objet (XML, Map...) en tableau
 * @param unknown_type $arrObjData
 * @param unknown_type $arrSkipIndices
 */
function xmlIntoArray($xml){
	$res = null;
	// NOTE la methode simplexml_load_string() transforme les entites XML en leur representation texte (ex. : "&#60;"=>"<")
	if ($xmlObj = simplexml_load_string($xml)){
		$res =  xmlIntoArrayRec($xmlObj);
	}else{
		spip_log("IMPOSSIBLE de transformer le XML en tableau - XML -> ".$xml,"spipservice");
	}
	return $res;
}
function xmlIntoArrayRec($xmlObj){
	$arr = array();
	if (is_object($xmlObj)){
		$xmlObj = get_object_vars($xmlObj);
	}
	if (is_array($xmlObj)){
		foreach ($xmlObj as $key => $value) {
			$arr[$key] = xmlIntoArrayRec($value);
		}
		return $arr;
	}else{
		return unformateXmlEntity($xmlObj);
	}
}

/**
 * transforme un tableau en XML<br />
 * <i>info : le corps de cette méthode fait en sorte qu'une répétition d'un même élément du même niveau dans le tableau soit affiché de manière répétitive dans le XML</i><br />
 * <i>exemple : un tableau ['article'=>['0'=>'article0', '1'=>'article1]] donnera au format XML la structure suivante [article]article0[/article][article]article1[/article]</i>
 * @param array $arr
 */
function arrayIntoXml($arr){
	return '<root>'.arrayIntoXmlRec($arr).'</root>';
}
function arrayIntoXmlRec($arr, $keyParent=null){
	$res = "";
	if (is_array($arr)){
		foreach ($arr as $key => $value){
			$fuck = false;
			if (is_array($value)){
				$fuck = true;
				foreach ($value as $keyTemp=>$valueTemp){
					if (!is_numeric($keyTemp)){
						$fuck = false;
					}
				}
			}
			if ($fuck){
				$res .= arrayIntoXmlRec($value, $key);
			}else{
				if ($keyParent){
					$res .= "<".$keyParent.">";
					$res .= arrayIntoXmlRec($value);
					$res .= "</".$keyParent.">";
				}else{
					$res .= "<".$key.">";
					$res .= arrayIntoXmlRec($value);
					$res .= "</".$key.">";
				}

			}
		}
	}else if (is_bool($arr)){
		if ($arr) $res.='true';
		else $res.='false';
	}else{
		$res .= formateXmlEntity($arr);
	}
	return $res;
}

function getCodeStatut($statut) {
	switch ($statut) {
		case 'publie':
			return $statut; // 3
			break;
		case 'prop':
			return $statut; // 2
			break;
		case 'prepa':
			return $statut; // 1
			break;
		case 'refuse':
			return $statut; // -1
			break;
		case 'poubelle':
			return $statut; // -2
			break;
		default:
			return $statut; // 0
			break;
	};
}

function isAvailableStatut($statut){
	switch ($statut) {
		case 'publie':
			return true; // 3
			break;
		case 'prop':
			return true; // 2
			break;
		case 'prepa':
			return true; // 1
			break;
		case 'refuse':
			return true; // -1
			break;
		case 'poubelle':
			return true; // -2
			break;
		default:
			return false; // 0
			break;
	};
}

/**
 * remplace les caracteres speciaux (non compatibles XML) par leur representation XML
 * @param unknown_type $s
 */
function formateXmlEntity($s){
	$xmlEntitiesMap = array("&#38;"=>"&"
			, "&#34;"=>"\""
			, "&#39;"=>"'"
			, "&#60;"=>"<"
			, "&#62;"=>">"
			, "&#161;"=>"¡"
			, "&#162;"=>"¢"
			, "&#163;"=>"£"
			, "&#164;"=>"¤"
			, "&#165;"=>"¥"
			, "&#166;"=>"¦"
			, "&#167;"=>"§"
			, "&#168;"=>"¨"
			, "&#169;"=>"©"
			, "&#170;"=>"ª"
			, "&#171;"=>"«"
			, "&#172;"=>"¬"
			, "&#174;"=>"®"
			, "&#175;"=>"¯"
			, "&#176;"=>"°"
			, "&#177;"=>"±"
			, "&#178;"=>"²"
			, "&#179;"=>"³"
			, "&#180;"=>"´"
			, "&#181;"=>"µ"
			, "&#182;"=>"¶"
			, "&#183;"=>"·"
			, "&#184;"=>"¸"
			, "&#185;"=>"¹"
			, "&#186;"=>"º"
			, "&#187;"=>"»"
			, "&#188;"=>"¼"
			, "&#189;"=>"½"
			, "&#190;"=>"¾"
			, "&#191;"=>"¿"
			, "&#192;"=>"À"
			, "&#193;"=>"Á"
			, "&#194;"=>"Â"
			, "&#195;"=>"Ã"
			, "&#196;"=>"Ä"
			, "&#197;"=>"Å"
			, "&#198;"=>"Æ"
			, "&#199;"=>"Ç"
			, "&#200;"=>"È"
			, "&#201;"=>"É"
			, "&#202;"=>"Ê"
			, "&#203;"=>"Ë"
			, "&#204;"=>"Ì"
			, "&#205;"=>"Í"
			, "&#206;"=>"Î"
			, "&#207;"=>"Ï"
			, "&#208;"=>"Ð"
			, "&#209;"=>"Ñ"
			, "&#210;"=>"Ò"
			, "&#211;"=>"Ó"
			, "&#212;"=>"Ô"
			, "&#213;"=>"Õ"
			, "&#214;"=>"Ö"
			, "&#215;"=>"×"
			, "&#216;"=>"Ø"
			, "&#217;"=>"Ù"
			, "&#218;"=>"Ú"
			, "&#219;"=>"Û"
			, "&#220;"=>"Ü"
			, "&#221;"=>"Ý"
			, "&#222;"=>"Þ"
			, "&#223;"=>"ß"
			, "&#224;"=>"à"
			, "&#225;"=>"á"
			, "&#226;"=>"â"
			, "&#227;"=>"ã"
			, "&#228;"=>"ä"
			, "&#229;"=>"å"
			, "&#230;"=>"æ"
			, "&#231;"=>"ç"
			, "&#232;"=>"è"
			, "&#233;"=>"é"
			, "&#234;"=>"ê"
			, "&#235;"=>"ë"
			, "&#236;"=>"ì"
			, "&#237;"=>"í"
			, "&#238;"=>"î"
			, "&#239;"=>"ï"
			, "&#240;"=>"ð"
			, "&#241;"=>"ñ"
			, "&#242;"=>"ò"
			, "&#243;"=>"ó"
			, "&#244;"=>"ô"
			, "&#245;"=>"õ"
			, "&#246;"=>"ö"
			, "&#247;"=>"÷"
			, "&#248;"=>"ø"
			, "&#249;"=>"ù"
			, "&#250;"=>"ú"
			, "&#251;"=>"û"
			, "&#252;"=>"ü"
			, "&#253;"=>"ý"
			, "&#254;"=>"þ"
			, "&#255;"=>"ÿ"
			, "&#338;"=>"Œ"
			, "&#339;"=>"œ"
			, "&#352;"=>"Š"
			, "&#353;"=>"š"
			, "&#376;"=>"Ÿ"
			, "&#402;"=>"ƒ"
			, "&#710;"=>"ˆ"
			, "&#732;"=>"˜"
			, "&#913;"=>"Α"
			, "&#914;"=>"Β"
			, "&#915;"=>"Γ"
			, "&#916;"=>"Δ"
			, "&#917;"=>"Ε"
			, "&#918;"=>"Ζ"
			, "&#919;"=>"Η"
			, "&#920;"=>"Θ"
			, "&#921;"=>"Ι"
			, "&#922;"=>"Κ"
			, "&#923;"=>"Λ"
			, "&#924;"=>"Μ"
			, "&#925;"=>"Ν"
			, "&#926;"=>"Ξ"
			, "&#927;"=>"Ο"
			, "&#928;"=>"Π"
			, "&#929;"=>"Ρ"
			, "&#931;"=>"Σ"
			, "&#932;"=>"Τ"
			, "&#933;"=>"Υ"
			, "&#934;"=>"Φ"
			, "&#935;"=>"Χ"
			, "&#936;"=>"Ψ"
			, "&#937;"=>"Ω"
			, "&#945;"=>"α"
			, "&#946;"=>"β"
			, "&#947;"=>"γ"
			, "&#948;"=>"δ"
			, "&#949;"=>"ε"
			, "&#950;"=>"ζ"
			, "&#951;"=>"η"
			, "&#952;"=>"θ"
			, "&#953;"=>"ι"
			, "&#954;"=>"κ"
			, "&#955;"=>"λ"
			, "&#956;"=>"μ"
			, "&#957;"=>"ν"
			, "&#958;"=>"ξ"
			, "&#959;"=>"ο"
			, "&#960;"=>"π"
			, "&#961;"=>"ρ"
			, "&#962;"=>"ς"
			, "&#963;"=>"σ"
			, "&#964;"=>"τ"
			, "&#965;"=>"υ"
			, "&#966;"=>"φ"
			, "&#967;"=>"χ"
			, "&#968;"=>"ψ"
			, "&#969;"=>"ω"
			, "&#977;"=>"ϑ"
			, "&#978;"=>"ϒ"
			, "&#982;"=>"ϖ"
			, "&#8211;"=>"–"
			, "&#8212;"=>"—"
			, "&#8216;"=>"‘"
			, "&#8217;"=>"’"
			, "&#8218;"=>"‚"
			, "&#8220;"=>"“"
			, "&#8221;"=>"”"
			, "&#8222;"=>"„"
			, "&#8224;"=>"†"
			, "&#8225;"=>"‡"
			, "&#8226;"=>"•"
			, "&#8230;"=>"…"
			, "&#8240;"=>"‰"
			, "&#8242;"=>"′"
			, "&#8243;"=>"″"
			, "&#8249;"=>"‹"
			, "&#8250;"=>"›"
			, "&#8254;"=>"‾"
			, "&#8260;"=>"⁄"
			, "&#8364;"=>"€"
			, "&#8465;"=>"ℑ"
			, "&#8472;"=>"℘"
			, "&#8476;"=>"ℜ"
			, "&#8482;"=>"™"
			, "&#8501;"=>"ℵ"
			, "&#8592;"=>"←"
			, "&#8593;"=>"↑"
			, "&#8594;"=>"→"
			, "&#8595;"=>"↓"
			, "&#8596;"=>"↔"
			, "&#8629;"=>"↵"
			, "&#8656;"=>"⇐"
			, "&#8657;"=>"⇑"
			, "&#8658;"=>"⇒"
			, "&#8659;"=>"⇓"
			, "&#8660;"=>"⇔"
			, "&#8704;"=>"∀"
			, "&#8706;"=>"∂"
			, "&#8707;"=>"∃"
			, "&#8709;"=>"∅"
			, "&#8711;"=>"∇"
			, "&#8712;"=>"∈"
			, "&#8713;"=>"∉"
			, "&#8715;"=>"∋"
			, "&#8719;"=>"∏"
			, "&#8721;"=>"∑"
			, "&#8722;"=>"−"
			, "&#8727;"=>"∗"
			, "&#8730;"=>"√"
			, "&#8733;"=>"∝"
			, "&#8734;"=>"∞"
			, "&#8736;"=>"∠"
			, "&#8743;"=>"∧"
			, "&#8744;"=>"∨"
			, "&#8745;"=>"∩"
			, "&#8746;"=>"∪"
			, "&#8747;"=>"∫"
			, "&#8756;"=>"∴"
			, "&#8764;"=>"∼"
			, "&#8773;"=>"≅"
			, "&#8776;"=>"≈"
			, "&#8800;"=>"≠"
			, "&#8801;"=>"≡"
			, "&#8804;"=>"≤"
			, "&#8805;"=>"≥"
			, "&#8834;"=>"⊂"
			, "&#8835;"=>"⊃"
			, "&#8836;"=>"⊄"
			, "&#8838;"=>"⊆"
			, "&#8839;"=>"⊇"
			, "&#8853;"=>"⊕"
			, "&#8855;"=>"⊗"
			, "&#8869;"=>"⊥"
			, "&#8901;"=>"⋅"
			, "&#8968;"=>"⌈"
			, "&#8969;"=>"⌉"
			, "&#8970;"=>"⌊"
			, "&#8971;"=>"⌋"
			, "&#9001;"=>"〈"
			, "&#9002;"=>"〉"
			, "&#9674;"=>"◊"
			, "&#9824;"=>"♠"
			, "&#9827;"=>"♣"
			, "&#9829;"=>"♥"
			, "&#9830;"=>"♦");
	foreach ($xmlEntitiesMap as $key => $value) {
		$s = str_replace($value, $key, $s);
	}
	return $s;
}

/**
 * remplace les caracteres XML par leur valeur (inverse de formateXmlEntity)
 * @param unknown_type $s
 */
function unformateXmlEntity($s){
	$xmlEntitiesMap = array("&#38;"=>"&"
			, "&#34;"=>"\""
			, "&#39;"=>"'"
			, "&#60;"=>"<"
			, "&#62;"=>">"
			, "&#161;"=>"¡"
			, "&#162;"=>"¢"
			, "&#163;"=>"£"
			, "&#164;"=>"¤"
			, "&#165;"=>"¥"
			, "&#166;"=>"¦"
			, "&#167;"=>"§"
			, "&#168;"=>"¨"
			, "&#169;"=>"©"
			, "&#170;"=>"ª"
			, "&#171;"=>"«"
			, "&#172;"=>"¬"
			, "&#174;"=>"®"
			, "&#175;"=>"¯"
			, "&#176;"=>"°"
			, "&#177;"=>"±"
			, "&#178;"=>"²"
			, "&#179;"=>"³"
			, "&#180;"=>"´"
			, "&#181;"=>"µ"
			, "&#182;"=>"¶"
			, "&#183;"=>"·"
			, "&#184;"=>"¸"
			, "&#185;"=>"¹"
			, "&#186;"=>"º"
			, "&#187;"=>"»"
			, "&#188;"=>"¼"
			, "&#189;"=>"½"
			, "&#190;"=>"¾"
			, "&#191;"=>"¿"
			, "&#192;"=>"À"
			, "&#193;"=>"Á"
			, "&#194;"=>"Â"
			, "&#195;"=>"Ã"
			, "&#196;"=>"Ä"
			, "&#197;"=>"Å"
			, "&#198;"=>"Æ"
			, "&#199;"=>"Ç"
			, "&#200;"=>"È"
			, "&#201;"=>"É"
			, "&#202;"=>"Ê"
			, "&#203;"=>"Ë"
			, "&#204;"=>"Ì"
			, "&#205;"=>"Í"
			, "&#206;"=>"Î"
			, "&#207;"=>"Ï"
			, "&#208;"=>"Ð"
			, "&#209;"=>"Ñ"
			, "&#210;"=>"Ò"
			, "&#211;"=>"Ó"
			, "&#212;"=>"Ô"
			, "&#213;"=>"Õ"
			, "&#214;"=>"Ö"
			, "&#215;"=>"×"
			, "&#216;"=>"Ø"
			, "&#217;"=>"Ù"
			, "&#218;"=>"Ú"
			, "&#219;"=>"Û"
			, "&#220;"=>"Ü"
			, "&#221;"=>"Ý"
			, "&#222;"=>"Þ"
			, "&#223;"=>"ß"
			, "&#224;"=>"à"
			, "&#225;"=>"á"
			, "&#226;"=>"â"
			, "&#227;"=>"ã"
			, "&#228;"=>"ä"
			, "&#229;"=>"å"
			, "&#230;"=>"æ"
			, "&#231;"=>"ç"
			, "&#232;"=>"è"
			, "&#233;"=>"é"
			, "&#234;"=>"ê"
			, "&#235;"=>"ë"
			, "&#236;"=>"ì"
			, "&#237;"=>"í"
			, "&#238;"=>"î"
			, "&#239;"=>"ï"
			, "&#240;"=>"ð"
			, "&#241;"=>"ñ"
			, "&#242;"=>"ò"
			, "&#243;"=>"ó"
			, "&#244;"=>"ô"
			, "&#245;"=>"õ"
			, "&#246;"=>"ö"
			, "&#247;"=>"÷"
			, "&#248;"=>"ø"
			, "&#249;"=>"ù"
			, "&#250;"=>"ú"
			, "&#251;"=>"û"
			, "&#252;"=>"ü"
			, "&#253;"=>"ý"
			, "&#254;"=>"þ"
			, "&#255;"=>"ÿ"
			, "&#338;"=>"Œ"
			, "&#339;"=>"œ"
			, "&#352;"=>"Š"
			, "&#353;"=>"š"
			, "&#376;"=>"Ÿ"
			, "&#402;"=>"ƒ"
			, "&#710;"=>"ˆ"
			, "&#732;"=>"˜"
			, "&#913;"=>"Α"
			, "&#914;"=>"Β"
			, "&#915;"=>"Γ"
			, "&#916;"=>"Δ"
			, "&#917;"=>"Ε"
			, "&#918;"=>"Ζ"
			, "&#919;"=>"Η"
			, "&#920;"=>"Θ"
			, "&#921;"=>"Ι"
			, "&#922;"=>"Κ"
			, "&#923;"=>"Λ"
			, "&#924;"=>"Μ"
			, "&#925;"=>"Ν"
			, "&#926;"=>"Ξ"
			, "&#927;"=>"Ο"
			, "&#928;"=>"Π"
			, "&#929;"=>"Ρ"
			, "&#931;"=>"Σ"
			, "&#932;"=>"Τ"
			, "&#933;"=>"Υ"
			, "&#934;"=>"Φ"
			, "&#935;"=>"Χ"
			, "&#936;"=>"Ψ"
			, "&#937;"=>"Ω"
			, "&#945;"=>"α"
			, "&#946;"=>"β"
			, "&#947;"=>"γ"
			, "&#948;"=>"δ"
			, "&#949;"=>"ε"
			, "&#950;"=>"ζ"
			, "&#951;"=>"η"
			, "&#952;"=>"θ"
			, "&#953;"=>"ι"
			, "&#954;"=>"κ"
			, "&#955;"=>"λ"
			, "&#956;"=>"μ"
			, "&#957;"=>"ν"
			, "&#958;"=>"ξ"
			, "&#959;"=>"ο"
			, "&#960;"=>"π"
			, "&#961;"=>"ρ"
			, "&#962;"=>"ς"
			, "&#963;"=>"σ"
			, "&#964;"=>"τ"
			, "&#965;"=>"υ"
			, "&#966;"=>"φ"
			, "&#967;"=>"χ"
			, "&#968;"=>"ψ"
			, "&#969;"=>"ω"
			, "&#977;"=>"ϑ"
			, "&#978;"=>"ϒ"
			, "&#982;"=>"ϖ"
			, "&#8211;"=>"–"
			, "&#8212;"=>"—"
			, "&#8216;"=>"‘"
			, "&#8217;"=>"’"
			, "&#8218;"=>"‚"
			, "&#8220;"=>"“"
			, "&#8221;"=>"”"
			, "&#8222;"=>"„"
			, "&#8224;"=>"†"
			, "&#8225;"=>"‡"
			, "&#8226;"=>"•"
			, "&#8230;"=>"…"
			, "&#8240;"=>"‰"
			, "&#8242;"=>"′"
			, "&#8243;"=>"″"
			, "&#8249;"=>"‹"
			, "&#8250;"=>"›"
			, "&#8254;"=>"‾"
			, "&#8260;"=>"⁄"
			, "&#8364;"=>"€"
			, "&#8465;"=>"ℑ"
			, "&#8472;"=>"℘"
			, "&#8476;"=>"ℜ"
			, "&#8482;"=>"™"
			, "&#8501;"=>"ℵ"
			, "&#8592;"=>"←"
			, "&#8593;"=>"↑"
			, "&#8594;"=>"→"
			, "&#8595;"=>"↓"
			, "&#8596;"=>"↔"
			, "&#8629;"=>"↵"
			, "&#8656;"=>"⇐"
			, "&#8657;"=>"⇑"
			, "&#8658;"=>"⇒"
			, "&#8659;"=>"⇓"
			, "&#8660;"=>"⇔"
			, "&#8704;"=>"∀"
			, "&#8706;"=>"∂"
			, "&#8707;"=>"∃"
			, "&#8709;"=>"∅"
			, "&#8711;"=>"∇"
			, "&#8712;"=>"∈"
			, "&#8713;"=>"∉"
			, "&#8715;"=>"∋"
			, "&#8719;"=>"∏"
			, "&#8721;"=>"∑"
			, "&#8722;"=>"−"
			, "&#8727;"=>"∗"
			, "&#8730;"=>"√"
			, "&#8733;"=>"∝"
			, "&#8734;"=>"∞"
			, "&#8736;"=>"∠"
			, "&#8743;"=>"∧"
			, "&#8744;"=>"∨"
			, "&#8745;"=>"∩"
			, "&#8746;"=>"∪"
			, "&#8747;"=>"∫"
			, "&#8756;"=>"∴"
			, "&#8764;"=>"∼"
			, "&#8773;"=>"≅"
			, "&#8776;"=>"≈"
			, "&#8800;"=>"≠"
			, "&#8801;"=>"≡"
			, "&#8804;"=>"≤"
			, "&#8805;"=>"≥"
			, "&#8834;"=>"⊂"
			, "&#8835;"=>"⊃"
			, "&#8836;"=>"⊄"
			, "&#8838;"=>"⊆"
			, "&#8839;"=>"⊇"
			, "&#8853;"=>"⊕"
			, "&#8855;"=>"⊗"
			, "&#8869;"=>"⊥"
			, "&#8901;"=>"⋅"
			, "&#8968;"=>"⌈"
			, "&#8969;"=>"⌉"
			, "&#8970;"=>"⌊"
			, "&#8971;"=>"⌋"
			, "&#9001;"=>"〈"
			, "&#9002;"=>"〉"
			, "&#9674;"=>"◊"
			, "&#9824;"=>"♠"
			, "&#9827;"=>"♣"
			, "&#9829;"=>"♥"
			, "&#9830;"=>"♦");
	foreach ($xmlEntitiesMap as $key => $value) {
		$s = str_replace($key, $value, $s);
	}
	return $s;
}

function getFileExtension($File, $Dot){
	if ($Dot == true) {
		$Ext = strtolower(substr($File, strrpos($File, '.')));
	}else{
		$Ext = strtolower(substr($File, strrpos($File, '.') + 1));
	}
	return $Ext;
}

function getArrayResponse($response){
	if ($response && $response!=''){
		if (checkErrorPHP($response)){
			return array("ERROR"=>$response);
		}else if (checkError001($response)){
			return array('ERROR'=>'Disable Service');
		}else if (!checkAuthentication($response)){
			return array('ERROR'=>'Bad Authentication');
		}else if (!checkPluginAvailable($response)){
			return array('ERROR'=>'Plugin disabled');
		}else{
			$arrXml = xmlIntoArray("<root>".$response."</root>");
			return $arrXml;
		}
	}else{
		return false;
	}
}
function getStringResponse($response){
	if ($response && $response!='' && $response!='<root></root>'){
		if (checkErrorPHP($response)){
			return 'ERROR - '.$response;
		}else if (checkError001($response)){
			return 'ERROR - Disable Service';
		}else if (!checkAuthentication($response)){
			return 'ERROR - Bad Authentication';
		}else if (!checkPluginAvailable($response)){
			return 'ERROR - Plugin disabled';
		}else{
			return $response;
		}
	}else{
		return false;
	}
}
function getBooleanResponse($response){
	if ($response && $response!=''){
		if (checkErrorPHP($response)){
			return false;
		}else if (checkError001($response)){
			return false;
		}else if (!checkAuthentication($response)){
			return false;
		}else if (!checkPluginAvailable($response)){
			return false;
		}else{
			if ($response==1 || $response=='true'|| $response==true){
				return true;
			}
			return false;
		}
	}else{
		return false;
	}
}

function checkPluginAvailable($response){
	if (strpos($response, 'spipservice introuvable')){
		return false;
	}
	return true;
}
function checkAuthentication($response){
	if (strpos($response, 'formulaire_login')){
		return false;
	}
	return true;
}
function checkError001($response){
	if (strpos($response, 'ERROR 001')){
		return true;
	}
	return false;
}
function checkErrorPHP($response){
	if (strpos($response, 'Parse error')){
		return true;
	}
	return false;
}



function uncrypt($in){
	$tab = explode("-|-", $in);
	if (count($tab)>1){
		$key = hash('sha256',floor(time()/60).$in);
		$divIn = floor(strlen($tab[1])/(strlen($tab[1])-$tab[0])); // 10
		$cpIn = 0;
		$out = "";
		for ($i = 0; $i < strlen($tab[1]); $i++) {
			if ($i%$divIn == 0 && $cpIn<(strlen($tab[1])-$tab[0])){
				$out.=substr($tab[1], $i, 1);
				$cpIn++;
			}
		}
		if (encrypt($out)==$in){
			// donnee cryptee avec la bonne cle
			return $out;
		}else{
			// cas de donnee non cryptee (ou avec la mauvaise cle)
			// on la retourne en clair
			return $in;
		}
	}else{
		// cas de donnee non cryptee
		// on la retourne en clair
		return $in;
	}
}
function encrypt($in){
	$key = hash('sha256',floor(time()/60).$in);
	$tabKey = str_split($key);
	$tabIn = str_split($in);
	$cpIn = 0;
	$divIn = count($tabIn);
	$crypto = strlen($key)."-|-";
	$inc = floor(count($tabKey)/$divIn);
	for ($i = 0; $i < count($tabKey); $i++) {
		if ($i%$inc == 0 && $cpIn<$divIn){
			$crypto.=$tabIn[$cpIn];
			$cpIn++;
		}
		$crypto.=$tabKey[$i];
	}
	return $crypto;
}

/**
 * récupère un paramètre qu'il soit en GET ou POST (note : GET en priorité)
 * @param unknown_type $name
 * @return null si le paramètre n'existe pas
 */
function getRequestParam($name){
	if (isset($_GET[$name]) && $_GET[$name]!= null && $_GET[$name]!='')
		return stripcslashes($_GET[$name]);
	else if(isset($_POST[$name]) && $_POST[$name]!= null && $_POST[$name]!='')
		return stripcslashes($_POST[$name]);
	else return null;
}

/**
 * Parsing XML/JSON en Array
 * @param unknown_type $requestParam
 * @param unknown_type $format
 */
function getRESTParams($requestParam, $format=FORMAT_XML){
	$result = NULL;
	// JSON
	if ($format==FORMAT_JSON){
		$result = json_decode($requestParam, true);
	}
	// XML
	elseif ($format==FORMAT_XML){
		$result = xmlIntoArray($requestParam);
	}
	// format inconnu, on n'y touche pas
	else{
		$result = $requestParam;
	}
	return $result;
}

/**
 * extrait le protocole de l'URL<br />
 * <strong>exemple : </strong> getProtocole("http://www.studio-lambda.com/labo/web/ps?param=value") : "http"
 * @param string $url
 * @return le protocole
 */
function getProtocole($url){
	if (preg_match( "/^(.+):\/\//i", $url, $matches)){
		return $matches[1];
	}
	return null;
}

/**
 * extrait le host de l'URL<br />
 * <strong>exemple : </strong> getHost("http://www.studio-lambda.com/labo/web/ps?param=value") : "www.studio-lambda.com"
 * @param string $url
 * @return le host
 */
function getHost($url){
	if (preg_match( "/^.+:\/\/?([^\/]+)/i", $url, $matches)){
		return $matches[1];
	}
	return null;
}

/**
 * extrait ce qui suit le host de l'URL<br />
 * <strong>exemple : </strong> getAfterHost("http://www.studio-lambda.com/labo/web/ps?param=value") : "/labo/web/ps?param=value"
 * @param string $url
 * @return le protocole
 */
function getAfterHost($url){
	if (preg_match( "/^.+:\/\/?[^\/]+(\/.*)/i", $url, $matches)){
		return $matches[1];
	}
	return null;
}

/**
 * log un tableau associatif - spip_log("le tableau", "spipservice"); donc dans le fichier spipservice.log de spip
 */
function logArray($a){
	if (is_array($a)){
		foreach ($a as $k => $v){
			spip_log("[".$k."]=>".logArray($v),"spipservice");
		}
	}else{
		spip_log("=>".$a,"spipservice");
	}
}

?>