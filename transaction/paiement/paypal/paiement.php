<?php
/*****************************************************************************
 *
 * Auteur   : Bruno | atnos.com (contact: contact@atnos.com)
 * Version  : 0.1
 * Date     : 29/07/2007
 *
 *
 * Copyright (C) 2007 Bruno PERLES
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *****************************************************************************/

	include_once(realpath(dirname(__FILE__)) . "/config.php");
		
	session_start();

	$total = 0;

    $total = $_SESSION['total'];
    


?>

<html>
<head>
</head>
<body onload="document.getElementById('formpaypal').submit()">
<?php
//"
// Référence
$Reference_Cde = urlencode($_SESSION['ref']);

// Montant
$Montant          = $total;


?>

	<br />
	

	
	<form action="<?php echo $serveur; ?>" id="formpaypal" method="post">
		
		<input type="hidden" name="upload" value="1">
		<input type="hidden" name="first_name" value="" />
		<input type="hidden" name="last_name" value="" />
		<input type="hidden" name="address1" value="" />
		<input type="hidden" name="city" value="" />
		<input type="hidden" name="zip" value="" />
		<input type="hidden" name="amount" value="" />
		<input type="hidden" name="email" value="">
		<input type="hidden" name="shipping_1" value="0" />
		
		<input type="hidden" name="item_name_1" value="Mon panier" />
		<input type="hidden" name="amount_1" value="<?php echo $Montant; ?>" />
		<input type="hidden" name="quantity_1" value="1" />
		
		
		<input type="hidden" name="business" value="<?php echo $compte_paypal; ?>" />
		<input type="hidden" name="receiver_email" value="<?php echo $compte_paypal; ?>" />
		<input type="hidden" name="cmd" value="_cart" />
		<input type="hidden" name="currency_code" value="<?php echo $Devise; ?>" />
		<input type="hidden" name="payer_id" value="" />
		<input type="hidden" name="payer_email" value="" />
		<input type="hidden" name="return" value="<?php echo $retourok; ?>" />
		<input type="hidden" name="notify_url" value="<?php echo $confirm; ?>" />
		<input type="hidden" name="cancel_return" value="<?php echo $retournok; ?>" />
		<input type="hidden" name="invoice" value="<?php echo $Reference_Cde; ?>" />
		
	</form>
	

	
</body>
</html>
