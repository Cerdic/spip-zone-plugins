<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php
	function footprint($params_paybox, $total, $transaction, $porteur, $time) {
		$msg =
			"PBX_SITE=".$params_paybox['site'].
			"&PBX_RANG=".$params_paybox['rang'].
			"&PBX_IDENTIFIANT=".$params_paybox['id'].
			"&PBX_TOTAL=$total".
			"&PBX_DEVISE=978".
 			"&PBX_CMD=$transaction".
			"&PBX_PORTEUR=$porteur".
			"&PBX_RETOUR=Mt:Mt:M;Ref:R;Auto:A;Erreur:E".
			(isset($params_paybox['retour_ok'])
				? "&PBX_EFFECTUE=".urlencode($params_paybox['retour_ok'])
				: '').
			(isset($params_paybox['retour_ko'])
				? "&PBX_REFUSE=".urlencode($params_paybox['retour_ko'])
				: '').
			(isset($params_paybox['retour_ok'])
				? "&PBX_ANNULE=".urlencode($params_paybox['retour_ko'])
				: '').
			"&PBX_HASH=SHA512".
			"&PBX_TIME=$time";


		$cle_bin = pack("H*", $params_paybox['cle']);
		$hmac = strtoupper(hash_hmac('SHA512', $msg, $cle_bin));

		return $hmac;
	}

	//Charger SPIP
	if (!defined('_ECRIRE_INC_VERSION')) {
		// recherche du loader SPIP.
		$deep = 2;
		$lanceur ='ecrire/inc_version.php';
		$include = '../../'.$lanceur;
		while (!defined('_ECRIRE_INC_VERSION') && $deep++ < 6) { 
			// attention a pas descendre trop loin tout de meme ! 
			// plugins/zone/stable/nom/version/tests/ maximum cherche
			$include = '../' . $include;
			if (file_exists($include)) {
				chdir(dirname(dirname($include)));
				require $lanceur;
			}
		}	
	}
	if (!defined('_ECRIRE_INC_VERSION')) {
		die("<strong>Echec :</strong> SPIP ne peut pas etre demarre.<br />
			Vous utilisez certainement un lien symbolique dans votre repertoire plugins.");
	}

	//CONFIGURATION DU PAIEMENT PAYBOX
	$params_paybox = false;
	if (isset($GLOBALS['PARAMS_PAYBOX'])) {
		$params_paybox = $GLOBALS['PARAMS_PAYBOX'];
	} else {
		$params_paybox = array(
			'site' => '1999888',
			'rang' => '32',
			'id' => '2',
			'cle' => '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
			'serveur' => 'https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi',
			//'retour_ok' => $GLOBALS['meta']['adresse_site']."/?page=transaction_merci",
			//'retour_ko' => $GLOBALS['meta']['adresse_site']."/?page=transaction_regret",
		);
	}
	//FIN CONFIGURATION DU PAIEMENT PAYBOX

	session_start();

	$lang = $_SESSION['langue_paybox'];
	$total = intval($_SESSION['total']) * 100;
	$transaction = urlencode($_SESSION['ref']);
	$porteur = $_SESSION['porteur'];
	$time = date("c");

	$hmac = footprint($params_paybox, $total, $transaction, $porteur, $time);
?>

<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<title>
  Paiement Paybox
</title>
</head>
<body onload="document.getElementById('formpaybox').submit();">

<table align="center">
  <tr>
    <td>
	<form action="<?php echo $params_paybox['serveur']; ?>" id="formpaybox" method="post" />
 		<input type="hidden" name="PBX_SITE" value="<?php echo $params_paybox['site']; ?>" />
 		<input type="hidden" name="PBX_RANG" value="<?php echo $params_paybox['rang']; ?>" />
 		<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $params_paybox['id']; ?>" />
 		<input type="hidden" name="PBX_TOTAL" value="<?php echo $total; ?>" />
 		<input type="hidden" name="PBX_DEVISE" value="978" />
 		<input type="hidden" name="PBX_CMD" value="<?php echo $transaction; ?>" />
 		<input type="hidden" name="PBX_PORTEUR" value="<?php echo $porteur; ?>" />
 		<input type="hidden" name="PBX_RETOUR" value="Mt:Mt:M;Ref:R;Auto:A;Erreur:E">
		<?php if (isset($params_paybox['retour_ok'])) { ?>
 		<input type="hidden" name="PBX_EFFECTUE" value="<?php echo urlencode($params_paybox['retour_ok']); ?>" />
		<?php } ?>
		<?php if (isset($params_paybox['retour_ko'])) { ?>
 		<input type="hidden" name="PBX_REFUSE" value="<?php echo urlencode($params_paybox['retour_ko']); ?>" />
 		<input type="hidden" name="PBX_ANNULE" value="<?php echo urlencode($params_paybox['retour_ko']); ?>" />
		<?php } ?>
 		<input type="hidden" name="PBX_HASH" value="SHA512" />
 		<input type="hidden" name="PBX_TIME" value="<?php echo $time; ?>" />
 		<input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>" />

		<input type="image" src="<?php echo ($GLOBALS['meta']['adresse_site'].'/'.find_in_path("paiement/paybox/logo.jpg")); ?>" />
	</form>
	
	</td>
  </tr>
</table>
	
</body>
</html>
