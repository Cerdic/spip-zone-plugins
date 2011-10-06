<?php

/*
 * convert a string to a 32-bit integer
 */
function PB_PR_StrToNum($Str, $Check, $Magic)
{
    $Int32Unit = 4294967296;  // 2^32

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
        $Check *= $Magic; 	
        //If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31), 
        //  the result of converting to integer is undefined
        //  refer to http://www.php.net/manual/en/language.types.integer.php
        if ($Check >= $Int32Unit) {
            $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
            //if the check less than -2^31
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
        }
        $Check += ord($Str{$i}); 
    }
    return $Check;
}

/* 
 * Genearate a hash for a url
 */
function PB_PR_HashURL($String)
{
    $Check1 = PB_PR_StrToNum($String, 0x1505, 0x21);
    $Check2 = PB_PR_StrToNum($String, 0, 0x1003F);

    $Check1 >>= 2; 	
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);	
	
    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
	
    return ($T1 | $T2);
}

/* 
 * genearate a checksum for the hash string
 */
function PB_PR_CheckHash($Hashnum)
{
    $CheckByte = 0;
    $Flag = 0;

    $HashStr = sprintf('%u', $Hashnum) ;
    $length = strlen($HashStr);
	
    for ($i = $length - 1;  $i >= 0;  $i --) {
        $Re = $HashStr{$i};
        if (1 === ($Flag % 2)) {              
            $Re += $Re;     
            $Re = (int)($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag ++;	
    }

    $CheckByte %= 10;
    if (0 !== $CheckByte) {
        $CheckByte = 10 - $CheckByte;
        if (1 === ($Flag % 2) ) {
            if (1 === ($CheckByte % 2)) {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }

    return '7'.$CheckByte.$HashStr;
}

function pb_getpagerank($url, $racine=false) {
	
		$url = preg_replace(",</?[^>]*>,", "", $url);


		if ($racine) {
			$url = parse_url($url);
			$url = $url["host"];
		}

		$url = preg_replace(",^http(s)?://,", "", $url);
	

		$fichier_pagerank = sous_repertoire(_DIR_VAR, 'cache-pagerank') . md5($url).".php";

		$date_init = time() -  60 * 60 * 24 * 30;

		// Systeme de cache pour les variables exif								
		if (file_exists($fichier_pagerank) && @filemtime($fichier_pagerank) > $date_init) {
			lire_fichier($fichier_pagerank, $pagerank);
			$pagerank = unserialize($pagerank);

			return $pagerank;
		}
	
	$fp = fsockopen("toolbarqueries.google.com", 80, $errno, $errstr, 30);
	if (!$fp) {	
   		return '';
	} else {
		$out = "GET /tbr?client=navclient-auto&ch=".PB_PR_CheckHash(PB_PR_HashURL($url))."&features=Rank&q=info:".$url."&num=100&filter=0 HTTP/1.1\r\n";
		$out .= "Host: toolbarqueries.google.com\r\n";
		$out .= "User-Agent: Mozilla/4.0 (compatible; GoogleToolbar 2.0.114-big; Windows XP 5.1)\r\n";
		$out .= "Connection: Close\r\n\r\n";
	
		fwrite($fp, $out);
	   
		//$pagerank = substr(fgets($fp, 128), 4);
		//echo $pagerank;
		while (!feof($fp)) {
			$data = fgets($fp, 128);
			$pos = strpos($data, "Rank_");
			if($pos === false){} else{
				$pagerank = substr($data, $pos + 9);

		   		include_spip("inc/metas");
		   		ecrire_meta('pagerank', $pagerank);
		   		ecrire_metas();

				$pb_ecrire = serialize($pagerank);
				ecrire_fichier($fichier_pagerank, $pb_ecrire);
				

				return $pagerank;
			}
	   	}
   		fclose($fp);
	}
}



?>