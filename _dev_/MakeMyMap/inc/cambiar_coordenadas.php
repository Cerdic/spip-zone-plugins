<?php
if(isset ($_GET['envio'])) {		
	$query = "UPDATE spip_mymap SET value='".$_POST['numeromap']."' WHERE name='googlemapkey'";
			$result= spip_query($query);
			//echo $query;
}
?>