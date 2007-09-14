<?php


function maxi(){return 50;}

function encode($str)
{
		return (dc_encoding == 'UTF-8') ? utf8_encode($str) : htmlentities($str);
}

function nombres($i)
	{
		$arr = array(
			0 =>'z&eacute;ro',
			1 =>'un',
			2 =>'deux',
			3 =>'trois',
			4 =>'quatre',
			5 =>'cinq',
			6 =>'six',
			7 =>'sept',
			8 =>'huit',
			9 =>'neuf',
			10 =>'dix',
			11 =>'onze',
			12 =>'douze',
			13 =>'treize',
			14 =>'quatorze',
			15 =>'quinze',
			16 =>'seize',
			17 =>'dix-sept',
			18 =>'dix-huit',
			19 =>'dix-neuf',
			20 =>'vingt',
			21 =>'vingt et un',
			22 =>'vingt-deux',
			23 =>'vingt-trois',
			24 =>'vingt-quatre',
			25 =>'vingt-cinq',
			26 =>'vingt-six',
			27 =>'vingt-sept',
			28 =>'vingt-huit',
			29 =>'vingt-neuf',
			30 =>'trente',
			31 =>'trente et un',
			32 =>'trente-deux',
			33 =>'trente-trois',
			34 =>'trente-quatre',
			35 =>'trente-cinq',
			36 =>'trente-six',
			37 =>'trente-sept',
			38 =>'trente-huit',
			39 =>'trente-neuf',
			40 =>'quarante',
			41 =>'quarante et un',
			42 =>'quarante-deux',
			43 =>'quarante-trois',
			44 =>'quarante-quatre',
			45 =>'quarante-cinq',
			46 =>'quarante-six',
			47 =>'quarante-sept',
			48 =>'quarante-huit',
			49 =>'quarante-neuf',
			50 =>'cinquante',
		);
		return $i >= 0 ? $arr[$i] : array_values($arr);
	}

function position_lettre($size, $n=0)
	{
		$res = '';
		switch($n)
		{
			case 0  : $res = 'le premier'; break;
			case 1  : $res = 'le deuxi&egrave;me'; break;
			case 2  : $res = 'le troisi&egrave;me'; break;
			case 3  : $res = 'le quatri&egrave;me'; break;
			case 4  : $res = 'le cinqui&egrave;me'; break;
			case 5  : $res = 'le sixi&egrave;me'; break;
			case 6  : $res = 'le septi&egrave;me'; break;
			case 7  : $res = 'le huiti&egrave;me'; break;
			case 8  : $res = 'le neuvi&egrave;me'; break;
			case 9 : $res = 'le dixi&egrave;me'; break;
			case 10 : $res = 'le onzi&egrave;me'; break;
			case 11 : $res = 'le douzi&egrave;me'; break;
			case 12 : $res = 'le treizi&egrave;me'; break;
			case 13 : $res = 'le quatorzi&egrave;me'; break;
			case 14 : $res = 'le quinzi&egrave;me'; break;
			case 15 : $res = 'le seizi&egrave;me'; break;
			case 16 : $res = 'le dix-septi&egrave;me'; break;
			case 17 : $res = 'le dix-huiti&egrave;me'; break;
			case 18 : $res = 'le dix-neuvi&egrave;me'; break;
			case 19 : $res = 'le vingti&egrave;me'; break;
		}

		if (1+$n == $size) $res= 'le dernier';
		else if ( 1+$n == $size-1 ) $res='l\'avant-dernier';

		return $res;
	}



function mots()
	{
		$mots = nombres(-1); //recupere tous les nombres
		//array_map('mots','l10n', $mots); //internalisation

		if ( !empty($GLOBALS['captcha_my_words']) )
		{
			$mots = array_merge($mots, $GLOBALS['captcha_my_words']); //ajoute les mots personnels , todo virer la globale
		}

		$i= rand(0, count($mots)-1); //mot aleatoire
		return $mots[$i];
	}



function addition()
	{
		while ( ( $c =($a = rand(0, maxi())) + ($b = rand(0, maxi()))) > maxi() );
		return array( sprintf(('combien font %s et %s ? (chiffres)'), (nombres($a)), (nombres($b)) ) , $c);
	}



function soustraction()
	{
		while ( ( $c =($a = rand(0, maxi())) - ($b = rand(0, maxi()))) < 0 );
		return array( sprintf(('que donne %s moins %s ? (chiffres)'), (nombres($a)), (nombres($b)) ) , $c);
	}



function multiplication()
	{
		while ( ( $c =($a = rand(0, maxi())) * ($b = rand(0, maxi()))) > maxi() );
		return array( sprintf(('que donne %s fois %s ? (chiffres)'), (nombres($a)), (nombres($b)) ) , $c);
	}


function lettre_mot()
	{
		$f = rand(0, 3);

		if ($f <= 1) // 0
		{
			if ($f)  // 1 : on recupere un mot
			{
				do{
					do{
						$str = mots();
						$ok = strpos($str, ' ') === FALSE;
					} while (!$ok);
					$l = strlen($str);
					$nbr = rand(0, $l);
				}while ((!$ok = position_lettre($l, $nbr)) || !isset($str[$nbr]) );
			}
			else // 0 : on genere un chaine de caracteres
			{
				$str = '';
				$l = rand(4, 16); //4 a 16 caracteres
				$chars = range(0, 9); // chiffres de 0 a 9 : 0123456789
				$chars = array_merge($chars, range('A', 'Z')); //=> 0123456789ABCDEF...XYZ
				$chars = array_merge($chars, array('-', '_', '*')); //=> 0123456789ABCDEF...XYZ-_*
				shuffle($chars); // on touille

				for ($i=0;  $i < $l; $i++)
				{
					$c= array_pop($chars);
					$str.= rand(0, 1) ? strtolower($c) : strtoupper($c); // minuscule/MAJUSCULE
				}

				$nbr = rand(0, $l-1);
				$ok = position_lettre($l, $nbr);
			}
			return array(sprintf(('quel est %s caract&egrave;re du mot "%s" ?'), ($ok), encode($str)), $str[$nbr]) ;
		}

		else //2, 3 : mot a completer
		{
			do{
				$str = mots();
				$l = strlen($str);
			}while ($l < 6);
			$chars = range(0, $l-1);
			shuffle($chars);
			$res = $str;

			$nbr = 1;
			for ($i=0;  $i < $nbr; $i++)
			{
				do {
					$j = array_pop($chars);
				} while( $res[$j] == ' ');
				$str[$j] = '*';
			}
			$str = sprintf(('compl&eacute;tez le mot "%s" ...'), encode($str)) ;

			$str = str_replace('*', '<span style="color:red">*</span>', $str);
			return array($str , $res);
		}
	}



function chiffre_mot_suivante()
	{
		$str = '';
		$nbr = rand(4, 10);

		$numbers = range(0,9);
		shuffle($numbers);
		for ($i=0;  $i < $nbr; $i++) $str .= array_pop($numbers);

		$f = rand(0, 1);
		if ($f)
		{
			$nbr = rand(0, $nbr-2);
			return array(encode('dans le nombre "'.$str.'", quel chiffre vient apr&egrave;s '. nombres($str[$nbr]). '&nbsp;? (chiffres) ') , $str[1+$nbr]);
		}
		else
		{
			$nbr = rand(1, $nbr-1);
			return array(encode('dans le nombre "'.$str.'", quel chiffre vient avant '. nombres($str[$nbr]). '&nbsp;? (chiffres) ') , $str[$nbr-1]);
		}
	}



function caractere_mot_suivante()
	{
		$str='';
		$l = 15;
		$nbr = rand(4, $l);

		$chars = range(0, 9);
		$chars = array_merge($chars, range('A', 'Z'));
		shuffle($numbers);
		for ($i=0;  $i < $nbr; $i++) $str.=array_pop($numbers);

		$nbr = rand(0, $nbr-1);
		$nbr = position_lettre($l, $nbr);

		return array(sprintf(('quel est %s caract&egrave;re du mot "%s" ?'), ($nbr), encode($str)) , $str[$nbr]);
	}



function my_captcha()
	{
		if ( !empty($GLOBALS['my_captcha']) )
		{
			$temp = array();
			foreach($GLOBALS['my_captcha'] as $k => $v) $temp[] = array($k, $v);
			shuffle($temp);
			list($question, $sol) = array_pop($temp);
			return array($question , $sol);
		}
		else return lettre_mot();
	}



function captcha_level($l = 0)
	{
		$l = intval($l);
		$GLOBALS['captcha_level'] = ( ($l < 0) || ($l > 2) ) ? 0 : $l;
	}



function question($s='%s')
	{
			if ( empty($GLOBALS['captcha_level']) )
			{
				if ( !empty($GLOBALS['captcha_fonctions']) )
				{
					$fct_i = rand(0, count($GLOBALS['captcha_fonctions'])-1 );
					$fct = $GLOBALS['captcha_fonctions'][$fct_i];
				}
				else $fct = 'lettre_mot';
				$GLOBALS['dc_captcha_index'] = $fct();
				save_captcha();
				//printf($s, $GLOBALS['dc_captcha_index'][0]);
				$result = '<span class="spip_form_label">'.$GLOBALS['dc_captcha_index'][0].'</span>';
			}
			else
			{
				$captcha_id = md5( uniqid(microtime()) );
				$GLOBALS['dc_captcha_index'] = array ($captcha_id, mt_rand(100000, 999999), 'gd');
				//$url = _DIR_PLUGIN_CAPTCHA."inc/imagevide.php";
				//$url = $_SERVER['REQUEST_URI'];
				
				//$captchaimg = mt_rand(100000, 999999);
				
				if (dc_url_scan == 'path_info')
				{
					if ( strpos(' '.$url, '?') )
						$url = str_replace('?', '?img_captcha=' . $GLOBALS['dc_captcha_index'][0] .'&', $url.'&');
					else $url .= '?img_captcha=' . $GLOBALS['dc_captcha_index'][0] .'&';
				}
				else
				{
					$url .= '?img_captcha=' . $GLOBALS['dc_captcha_index'][0] .'&';
				}

				if ( !function_exists('imagePng') && !function_exists('imageJpeg') ) $GLOBALS['captcha_level'] = 1;

				save_captcha();

				if ( $GLOBALS['captcha_level'] == 1 )
				{
				

				$result = '';
				$result =	show_captcha_img($GLOBALS['dc_captcha_index'][0],'0');
				$result .=	show_captcha_img($GLOBALS['dc_captcha_index'][0],'1');
				$result .=	show_captcha_img($GLOBALS['dc_captcha_index'][0],'2');
				$result .=	show_captcha_img($GLOBALS['dc_captcha_index'][0],'3');
				$result .=	show_captcha_img($GLOBALS['dc_captcha_index'][0],'4');
				$result .=	show_captcha_img($GLOBALS['dc_captcha_index'][0],'5');
					//$result = '';
					//$result = '<img src="captcha.png" alt="' . str_replace('"', '', ('Si vous ne voyez aucun chiffre lisible, utilisez le formulaire de contact pour signaler le probl&egrave;me')) . '" border="0" />';
					
				}
				else 	if ( $GLOBALS['captcha_level'] == 2 )
				{
				$result = '';
				$result =	show_captcha_img($GLOBALS['dc_captcha_index'][0],'0');
				}
				else
				{
					echo '<img src="'. $url . '" alt="' . str_replace('"', '', ('Si vous ne voyez aucun chiffre lisible, utilisez le formulaire de contact pour signaler le probl&egrave;me')) . '" title="' . str_replace('"', '', ('Si vous ne voyez aucun chiffre lisible, utilisez le formulaire de contact pour signaler le probl&egrave;me')) . '" border="0" />';
				}
				//echo '<br /><strong>' . ('Captcha anti-spam') . '&nbsp;: </strong>';
				$result .= '<br /><span class="spip_form_label">recopiez le code num&eacute;rique indiqu&eacute; ci-dessus</span>';
			}

		return $result;
	}



function save_captcha()
	{
		global $con, $blog;
		//if ( empty($GLOBALS['captcha_enable']) || !isset($GLOBALS['dc_captcha_index'][1]) ) return;

		if ( !empty($GLOBALS['dc_captcha_index'][2]) )
		{
		 $code = $GLOBALS['dc_captcha_index'][0];
		 $q = $GLOBALS['captcha_level'] . '-' . $GLOBALS['dc_captcha_index'][1];
		}
		else
		{
			$q = md5(strtolower($GLOBALS['dc_captcha_index'][1]));
			$code = md5(uniqid(microtime()));
		}
		clear();

		$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
		$browser = substr($_SERVER['HTTP_USER_AGENT'], 0, 100);
		$timer = time();
		spip_query("INSERT INTO `spip_captcha` (`captcha_code`, `captcha_solution`, `captcha_time`, `captcha_ip_address`, `captcha_user_agent`) VALUES ('$code', '$q', '$timer', '$ip', '$browser');");

		//$con = spip_query($insReq);
		//if (!$con = spip_query($insReq))
		//$blog->setError('MySQL : '.$con->error(), 2000);
		$GLOBALS['captcha_sol'] = $q;
		$GLOBALS['captcha_code'] = $code;
  }



function field($id_form,$champ)
	{
	//	global $con, $blog;
	//	if ( empty($GLOBALS['captcha_code']) ) return;
	//xxxxxxxxxxxxxx
	  $result = '';
		$result = '<input name="'.$champ.'" id="input-'.$id_form.'-'.$champ.'" type="text" title="captcha anti-spam" size="10" class="password formo" />';
		$result .= '<input name="'.$champ.'_code" id="input-'.$id_form.'-'.$champ.'_code" value="' . $GLOBALS['captcha_code'] . '" type="hidden" />';
		return $result;
  }



function clear($code='')
	{
		global $con;

		$strReq = "DELETE FROM spip_captcha WHERE captcha_time <" . (time() - 60 * 60 * 2);

		if ( !empty($code) )
			$strReq .= " OR captcha_code='" .$code. "'";
		
		spip_query($strReq);
		//if (!$con->spip_query($strReq))
		//	return 0;
		return 1;
	}



function check($md5val, $value)
	{
		global $con;

		$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
		$browser = substr($_SERVER['HTTP_USER_AGENT'], 0, 100);

//			'captcha_ip_address = \'' . $con->escapeStr($ip) . '\' AND '.

		$strReq = "SELECT captcha_solution FROM spip_captcha WHERE captcha_code = '$md5val' AND captcha_user_agent = '$browser'";
		
		$s = spip_query($strReq);
		$row = spip_fetch_array($s);
		$solution = $row['captcha_solution'];
		
		
		$ok = 0;

				if ( strlen($solution) < 32 )
				{
					list($GLOBALS['captcha_level'], $solution) = split('-', $solution);
					if($value == $solution)
					{
					$ok = 1;
					}else{$ok = 0;}
				}
				else
				{
					if(md5(strtolower($value)) == $solution)
					{
					$ok = 1;
					}else{$ok = 0;}
				}
				clear($md5val);



		return $ok;
	}

function make_gd_img($content = '')
	{
		$content = '  '. preg_replace( '/(\w)/', '\\1 ', $content) .' ';

		$temp_width= 135;
		$temp_height = 20;

		$image_width = 150;
		$image_height = 40;

		if ( function_exists('imageCreateTrueColor') )
		{
			$temp = imageCreateTrueColor($temp_width, $temp_height);
			$image  = imageCreateTrueColor($image_width, $image_height);
		}
		else
		{
			$temp = imageCreate($temp_width, $temp_height);
			$image  = imageCreate($image_width, $image_height);
		}

		$white  = imageColorAllocate($temp, 255, 255, 255);
		$black  = imageColorAllocate($temp, 0, 0, 0);
		$grey   = imageColorAllocate($temp, 210, 210, 210);
		$text_color = imageColorAllocate($temp, rand(0, 150), rand(0, 50), rand(0, 60));

		imageFill($temp, 0, 0, $white);

		$nbr_obj = rand(3, 5);
		for ( $i = 0; $i < $nbr_obj; $i++ )
		{
			$nbr_corners = rand(3, 20);
			$poly = array();
			for ( $j = 0; $j < $nbr_corners; $j++ )
			{
				$poly[] = rand(0, $temp_width);
				$poly[] = rand(0, $temp_height);
			}
			$color = imageColorAllocate($temp, rand(130, 255), rand(130,255), rand(130, 255));
			imageFilledPolygon($temp, $poly, $nbr_corners, $color);

		}

		imageString($temp, 5, 0, 2, $content, $text_color);
		imageCopyResized($image, $temp, 0, 0, 0, 0, $image_width, $image_height, $temp_width, $temp_height);
		imageDestroy($temp);

		$nbr_waves = rand(1, 3);
		$coef_wave = ($nbr_waves * 360) / $image_width;
		$maxY = $image_height-rand(0, $image_height/2);
		for ( $bit = 0; $bit <= 1; $bit++ )
		{
			$curX = 0;
			$curY = $bit ? 0 : $maxY;
			for($pt = 0; $pt < $image_width; $pt++)
			{
				$newX = $curX + 1;
				$newY = $bit
					?($image_height/2) + (sin(deg2rad($newX * $coef_wave - 90)) * ($maxY/2))
					:($image_height/2) + (cos(deg2rad($newX * $coef_wave)) * ($maxY/2));
				ImageLine($image, $curX, $curY, $newX, $newY, $text_color);
				$curX = $newX;
				$curY = $newY;
			}
		}

		$nbr_obj = $image_width * $image_height / rand(5, 10);
		for ( $i = 0; $i < $nbr_obj; $i++ )
		{
			imageSetPixel($image, rand(0, $image_width), rand(0, $image_height), $black);
		}

		//$type = function_exists('imageJpeg') ? 'jpeg' : 'png';
		//@header('Content-Type: image/' . $type);
		//@header('Cache-control: no-cache, no-store');
		//($type =='png') ?imagePng($image) : imageJpeg($image);
		//ImageDestroy($image);
		
		$string = "$content";
		$query = md5($string);
		$dossier = sous_repertoire(_DIR_VAR, 'cache-image');
		$fichier = "$dossier$query.png";
		
		imagepng($image, $fichier);
		imagedestroy($image);
		$images = $fichier;
		
		return "<img src='".$images."' alt='' border='0' />";
	}

function make_png_char($char,$i)
	{
		$png = array(
		
			0 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACBUlEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKh3IiLL959+PFLgINNX0IIjzKAAKLIpkknri28ePvhhy8QrrwAT5+7ub2CBFbFAAHESF7OBfqjaOdJIAmxQEGAB+IzIHdvvCdWzwEEEDk2AQ0NXrkXaC7Q+Xnm2hBPALnFO09uvPEIyF0b7oypCyCAmMi2Js5ABWgiPKyA8QS0Fcg4+OAFxHNoACCASLMJaATcGmCUoMnCAw0SqmgAIIBIswkYPkBrgCZiWkMQAAQQCTYBgwUYDUBGnb0hroCFhySmLEAAkWDToou3gSQwYnClY7hNWNMeQAAxER9DEA/F6avi9PTD50DSX0MOqyxAADERH3QQBi6DgPkX4hQ/dXmsCgACiFibIAUBnvKm6eB5SC7G5RSAACIt7WGNaiAA+gbiITxpEiCASM5PWBMCMPWDotBABVdiAQKAACLWJogRQEPRciW8yAAGLK7UDwEAAUSsTUCDIJYBC1ZI6gDGHLAsd164HWINsGTCFbYQABBAJJSwcOejiedZaAFLPPzWAAFAAJFWlgP9MfHk1U03HkGqPj8NOX91eTxxgwwAAoiRbi1LgACiXzsCIIDoZxNAANHPJoAAop9NAAFEP5sAAoh+NgEEEP1sAggg+tkEEED0swkgwABMhs4JaJfE5gAAAABJRU5ErkJggg==',
			1 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAABjklEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAKLIpocfvlx88Y5IxQABxEKq6UCjDz54cfHlWyD54ccvoMib8mhiNAIEEGk2Ba/cC7QAWSTOQIVIvQABRELoAe2AWAM0vc/dXICDDcjON9cmUjtAADH8JwUcuP/8/fefQMbE41eFO5YU7jhBvF6AACIt9OwVJIAkMHomnbxKmocYGAACiJy0t+nGI6BlQFvlBXiI1wUQQOTYNBHsIX91eZJ0AQQQExmpHJiNgMmB+FQHAQABRLJNCy/eBpJ+GnKkagQIICYyIglIxuurkqoRIIBIs2kjOC0AE4K+hBCpNgEEEGk2HXz4HJQWSA86IAAIICYygk5fXJgMmwACiLTSCFKkkucngABiIil9w4sJMgBAADGRGklkpAUIAAggEmyC2EFeJAEBQAAx0q1lCRBA9GtHAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAKKfTQABRD+bAAKIfjYBBBgA9Hy/mlzuqpUAAAAASUVORK5CYII=',
			2 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACEUlEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKhRPPFF+8+/PgFZOhLCAlwsOFXDBBA5Nh08MGLRRdvb7zxCFkwzkClzt4Qj30AAcRIas5tOnh+0olrEDbEKw8+fHn44QuEuzbcGZdlAAFEmk2LLtwp2nkSyMiz0IrXV5UX4IGIA/1XvPMkMCSBPutzN8eqFyCASEsR/GD3As0CBhTcGiDw15DLM9cGMjahBikyAAgg0uIJaKK/RjRWKWDQAUlIAsEKAAKIaqkcElXIHkUDAAFEHZuAXpl48iqQYa8ggUsNQAAxUcWa4JV7gX4Cpjpg/OFSBhBALBRaA8xbwNQIsQZPEgcCgABioSRigHkLkn+ByWGevy2eSAICgABiIc8OYKwA8xaQDfQEMH0DsxdBXQABRLJNwAJi0smrkNRMsARCBgABxEKSV5I2HgaWqpCMhZZ5CQKAACLWJqAFwAQG9ArQB73u5kCbSA0MgAAiqtwD+sZ54XZIiAH9oQD2CtBKfXFhCAMoSLDiAAggomwCpmNI/OMH+MtygAAiKvQU+HmBwWUvL4kcMZAIe/DxM9DHwFwFEQFlLHABiAkAAoiRWi1LoGXAUMWTRgACiJFubViAAKJfiwUggOhnE0AA0c8mgACin00AAUQ/mwACiH42AQQQ/WwCCCD62QQQYAAKbLSfiCgo0QAAAABJRU5ErkJggg==',
			3 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACCElEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKhRPPDD18efPgCZOhLCAlwsOFXDBBA5Nj04cevSSevLrpwB8iAC9orSNTZGwKtxKULIIAYSc25G288Kt55EmKHvACPggAPkH3xxTuI7Fx/W38NOawaAQKINJuAwWU8cyPEB33u5kCb4OJJGw8D7QOG4Z38UKx6AQKI5BQBNAvo6rXhznBrIJ6b528LCdiDD15g1QgQQKTFE9BEXE5GthgrAAggqqXySSeuQewDBixWBQABxEQta5oOngcygMkPlxqAACIzPwEjH5jQISkQyAYyCKZygAAi0yZgtAOTO3Ik5Zlr47EGCAACiJHsmhCSxoDpe+PNh0A2ME3ujffEky4AAoiRKnVu8Mq9QMviDFSAmQyXGoAAok6KsJeXhPgPjxqAAGIismgAuhpY0OFS8ODjZ4JZCiCAWIgs64CBA0xjwDjHjHagOMQRBuLCeAwBCCCi/AQsfoARDkzKQJ8Bsw68PAX6FcgFCkJKQmA84TEEIICITRFA04t2noTbgemUXndz/FUUQACRlvaAoXTw4XNgSMJrDaBX/NXlcZVAyAAggBjp1rIECCD6tSMAAoh+NgEEEP1sAggg+tkEEED0swkggOhnE0AA0c8mgACin00AAUQ/mwACDAAW68RXFduHKgAAAABJRU5ErkJggg==',
			4 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAB8UlEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAKKyTRdfvPvw4xdWKYAAIt+mSSeuqUxcnbzxMFzk4IMXzgu3L7pwB6t6gABiIc+aop0ngSbKC/DkmWvDBZsOngeS/hpyWLUABBA5NgFNBFqjLyG0NtxZgIMN7iFg0NkrSACtx6oLIIBIDj2gHcBwA1owz98Wbg0oME9eBZJx+qq4NAIEEGk2PfzwBRJEve7myG4H+gboJ6AIrqADAoAAYiI1eoBJC2gcmokLL94GkvG4PQQEAAHERFK4AR0ODDGgh5DFgXZvuvEIT1qAAIAAIsGmieCYACY25OgBAqA1QMvwpAUIAAggFuI9BIwkeE6Ciz/4+BnoURDjw5fglXuBDHt5yTwLLUwTAAKIWJvgOR+SIrAmFohTcPkMIICItQmrM4FZatHF2xtvPAIGHTwLA9lYTQAIIEYKa3fjmRuBXulzN48zUMGvEiCAmCgsTyEh5oc31UEAQABRZBMkLQCDCy01YgUAAUSRTRtvPoQkNmIUAwQQpaGHJ7GhAYAAosgmYNoDhhuuxIYGAAKIkW4tS4AAol87AiCA6GcTQADRzyaAAKKfTQABRD+bAAKIfjYBBBD9bAIIIPrZBBBA9LMJIMAAL2Sb+wxF0loAAAAASUVORK5CYII=',
			5 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAB6UlEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKh3IiDD16gidgrSGAqAwggcmy6+OJd0sbDDz98waVgbbgzpmUAAUSOTUU7T8Kt0ZcQEuBgQ5aVF+DB6ieAACLZpkUX7gD9BDTubLo/SRoBAojkFHHw4XMgmW+uTapGgAAizaYPP35tvPEIyPDTkCPVJoAAIi30NoGt8deQA8YNMKoegGMLM6qwAoAAIs2mCy/fAkmgBSoTVwP9BxePM1ABhicw8vDoBQggRpJqjeCVe+G5B+IVoH3ABALkAtl74z3xWAYQQKTZBDQUaBMkHcNDDBiMwOwFlAIKAnMSLr0AAcRIlZoQaD3Qu0AGMOnj8hZAAFGn3INn1Qe4Cw6AAKKOTcipAxcACCAmIgNHpHPppBPX8BQckESBtRyCAIAAYiIyIQDJpoPnIQxMa4BSQEYe3oIDIICIsgmSVSGpHOgzePEKtBhY2gIRJNEDcxUeQwACiNi0BzQUaA2u+AA6pdfdHH9JARBAJKRyoDXAgNp48yE8DIH+AEaMv7o8kEFQO0AAMdKtZQkQQPRrRwAEEP1sAggg+tkEEED0swkggOhnE0AA0c8mgACin00AAUQ/mwACiH42AQQYAH9WrBYy0ynJAAAAAElFTkSuQmCC',
			6 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACKElEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKh3IiHH748+PAFyLBXkMCjDCCAKLJp0olrCy/efgi2BgLiDFTyzbXlBXgwFQMEECN5Offii3dJGw9D7ACaqyDAA/QWhCvAwbY33hPTMoAAIsdPiy7caTp4/sOPX8Dg6nM3hxt68MGL5I2HgeJAJI+hCyCASPYT0DfOC7dDAgpoDZos0A6gz/QlhDA1AgQQaTYBDTKZuRFIYrUGPwAIINJSOSTQgMFVZ29IapgDBBALSakZGENABtA3H3/8ArIPPnwOSREG4sJAX+LXDhBAJIRe0c6TQNNB5koIbbzxCE0WGDdAF2CNIQgACCASbFKZuBoYdBA2MNXZy0tCzAWmkUknrwKlgFxg+salHSCAiA09YAqGWIPpdpCtChLABAm0EuhXfw05rCYABBAT8YkbEiVrw50xgwgoAimKkMsLNAAQQKSlPWBZACwCyCu6AAKINJse4HAy0CvA4MVfyAIEELE2QUIfbiKaNcAyEGINnrQHEEDE2gSMIYhlwJINmNYhqQNoB7A4h6QFoIK5/rZ4TAAIIBJSOdD04JV7IUkDDQB9A7QGfxQCBBDJJSzQQxtvPoSEIdBooB1x+qr460AIAAggRrq1LAECiH7tCIAAop9NAAFEP5sAAoh+NgEEEP1sAggg+tkEEED0swkggOhnE0AA0c8mgAADAPJW3sAgdjPCAAAAAElFTkSuQmCC',
			7 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAABo0lEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGIhSfXFF+8+/PiFX42CAI+8AA+mOEAAsZBkjfPC7cSofFMejSkIEEAk2AR0qb+GHC4/AcWBTgEy6uwNsSoACCBGqpQRQGuCV+4F2hRnoNLnbo5VDUAAUSdFJG88DLTGXkEClzVAABBAVLCp6eD5gw9e6EsIzfW3xaMMIIAotWnRhTuTTlwT4GAD+gZI4lEJEEAU2QQMMaCHIKkA6Cf8igECiCKbinaeBKYFYIIEJgSCigECiHybgIEG9BMwxHpxpwJkABBAZNoETAKQcOslFD1wABBATOTlHmC4ARnAcAMiInUBBBATeent4YcvQK/gKg6wAoAAYiI7veWZa2MtSXEBgABiIiOfQspAYtIbMgAIICZSww2YFoCMfHNtIhMCHAAEEGk2HXz4nDwPAQFAAJFmEyRigB4iIx0BBBAj3VqWAAFEv3YEQADRzyaAAKKfTQABRD+bAAKIfjYBBBD9bAIIIPrZBBBA9LMJIIDoZxNAgAEAhMxxWX0cOi4AAAAASUVORK5CYII=',
			8 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACJ0lEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKhRPOHH78uvngHZAhwsOlLCOFXDBBAZNp08MGLSSevAkm4iLwAT7y+ap6FFi4tAAHESEbOnXTiWtPB8xC2vYIEkAT6DOg/CHdtuDNWXQABRLJNQH8Er9wLZMQZqNTZGwLDDc16oCBWnwEEEMk2JW88vPHGI6xuh0hB2HvjPdFiDiCAWMhIBaBQkpeEizz88GXhxduLLtyBSEESCNyvcAAQQCTbBHQpMACBRgNDD24HRMpfQw7oAmDSUBDgAZJoGgECiOTQA5ruvHA70PlAV0M8AUl1QIsx/YEMAAKInFQO8RbBxIYGAAKIidT0bTxzI9AaoD8gCQzIBiYEeAzhAQABRGzoAc0CJm5IiQBPx0Bu0sbDwPAE+hLoM/yhBxBATMQnbqC5QLOAyReeXYAWQFIzUApYZOA3ASCAmIjMrZCCB+hwtFwCtDvPXBvIgKdAXAAggIiyCRJoQDvwFKMEowoggJhIzbOYgpBwA6Zy/NoBAogom4BZEhhKwJiHxBY8YwGTosnMjZD4yweHIR4AEEDEpj1ggVa88yRWbwGzVJ+7OWahgAYAAoiEMgJS9gCTBsRbQKOBdviry0MqDoIAIIAY6dayBAgg+rUjAAKIfjYBBBD9bAIIIPrZBBBA9LMJIIDoZxNAANHPJoAAop9NAAFEP5sAAgwAe4HslWhrrAcAAAAASUVORK5CYII=',
			9 => 'iVBORw0KGgoAAAANSUhEUgAAACMAAAAjCAIAAACRuyQOAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAACJ0lEQVR42mL8//8/A10AQAAxMdALAAQQ/WwCCCD62QQQQPSzCSCA6GcTQADRzyaAAGKhRPPDD18efPgCZOhLCAlwsOFXDBBA5Nj04cevRRfuLLx4+yHYGgiwV5DIM9cGkrh0AQQQI6k59+KLd0kbD0PskBfgURDgATIOPngBkZ3rb+uvIYdVI0AAkWYT0ALnhduBfgIGV529IdwHQJGmg+eBHgWG4Zl0f6whCRBApKWIop0nIdasDXdGDiig0UCLgSRQFu4/NAAQQEwkeQhiCsRQNFmgCNAFEGVYtQMEEAk2PYAZgSvagR7Cox0ggEiwCe4PrK4GRhIwsSArQwMAAUSCTcDAkQenNGDkIzsfyJ504howCvH7GCCASEt7G288St54GJK+galZgJ39wcfPm248glsMFD+b7o9VL0AAkZZzwXnFFugnYAAC/QEXz7PQAoYe0L54fVVcegECiJG8OhfoOXjmBQYX0Bqg9XgyExAABBCZ5R5yQQBMCJNOXgX5zFwbT+kHEECUluVAnwELJ0h2BoYhHpUAAURRWQ7MyMlga4BeAZYa+BUDBBALqXEDDDdg3ADt2HjzITB6IKm/z92cYK0BEEAkpAiRzqWYgnEGKlgLJ0wAEEAk2AT0wcSTVx/Cqj5gkgOmaUheJgYABBAj3VqWAAFEv3YEQADRzyaAAKKfTQABRD+bAAKIfjYBBBD9bAIIIPrZBBBA9LMJIIDoZxNAgAEAbJjWW8gqdR8AAAAASUVORK5CYII=',
);
		$im = imagecreatefromstring(base64_decode($png[$char]));
		

		$string = "$char-$i";
		$query = md5($string);
		$dossier = sous_repertoire(_DIR_VAR, 'cache-image');
		$fichier = "$dossier$query.png";
		
		//if (file_exists($fichier)){
		//	$image = $fichier;
		//}	
		//else{	
			imagepng($im, $fichier);
			imagedestroy($im);
			$image = $fichier;
		//}
		
		if ($i == '0' ){
			return "<img src='".$image."' alt='" . str_replace('"', '', ('Si vous ne voyez aucun chiffre lisible, utilisez le formulaire de contact pour signaler le probl&egrave;me')) . "' border='0' />";
		}
		else{
			return "<img src='".$image."' alt='' border='0' />";
		}
		
	}

function show_captcha_img($code = '',$i = '')
	{
		global $con;

		$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
		$browser = substr($_SERVER['HTTP_USER_AGENT'], 0, 100);

//			'captcha_ip_address = \'' . $con->escapeStr($ip) . '\' AND '.

		$strReq = "SELECT captcha_solution FROM spip_captcha WHERE captcha_code = '$code' AND captcha_user_agent = '$browser'";
		$s = spip_query($strReq);
		$row = spip_fetch_array($s);
		$solution = $row['captcha_solution'];
		list($GLOBALS['captcha_level'], $solution) = split('-', $solution);
					//$i = intval($_GET['i']);
					$istr = $i;
					$i = intval($i);
					$number = substr($solution, $i, 1 );
					
		if ( $GLOBALS['captcha_level'] == 1 ){
		return 	make_png_char($number, $istr);
		}
		if ( $GLOBALS['captcha_level'] == 2 ){
		return 	make_gd_img($solution);
		}
		
	}
?>