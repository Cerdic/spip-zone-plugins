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
	$mode = '1';
	$site = 'SITE';
	$rang = 'RANG';
	$id = 'IDENTIFIANT';
	$devise = '978';
	$serveur = $GLOBALS['meta']['adresse_site']."/cgi-bin/modulev2.cgi";
	//FIN CONFIGURATION DU PAIEMENT PAYBOX
	    	
	$lang = $_SESSION['langue_paybox'];
	$retourok = $GLOBALS['meta']['adresse_site']."/?page=transaction_merci";
	$retourko = $GLOBALS['meta']['adresse_site']."/?page=transaction_regret";
	
	session_start();

	$total = $_SESSION['total'];

	$total *= 100;

	$transaction = urlencode($_SESSION['ref']);

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
	
	<form action="<?php echo $serveur; ?>" id="formpaybox" method="post">
 		<input type="hidden" name="PBX_MODE" value="<?php echo $mode; ?>"> 
 		<input type="hidden" name="PBX_SITE" value="<?php echo $site; ?>"> 
 		<input type="hidden" name="PBX_RANG" value="<?php echo $rang; ?>"> 
 		<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $id; ?>"> 
 		<input type="hidden" name="PBX_TOTAL" value="<?php echo $total; ?>">
 		<input type="hidden" name="PBX_DEVISE" value="<?php echo $devise; ?>"> 
 		<input type="hidden" name="PBX_PORTEUR" value=""> 
 		<input type="hidden" name="PBX_REFUSE" value="<?php echo $retourko; ?>"> 
 		<input type="hidden" name="PBX_ANNULE" value="<?php echo $retourko; ?>"> 
 		<input type="hidden" name="PBX_CMD" value="<?php echo $transaction; ?>"> 
 		<input type="hidden" name="PBX_RETOUR" value="montant:M;ref:R;auto:A;trans:T;erreur:E"> 
 		<input type="hidden" name="PBX_EFFECTUE" value="<?php echo $retourok; ?>"> 
 		
 		
 		
		<input type="image" src="<?php echo ($GLOBALS['meta']['adresse_site'].'/'.find_in_path("paiement/paybox/logo.jpg")); ?>" />
	</form>
	
	</td>
  </tr>
</table>
	
</body>
</html>
