<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
if(isset ($_GET['envio'])) {		
	$query = "UPDATE spip_gis SET value='".$_POST['numeromap']."' WHERE name='googlemapkey'";
			$result= spip_query($query);
			//echo $query;
}
?>