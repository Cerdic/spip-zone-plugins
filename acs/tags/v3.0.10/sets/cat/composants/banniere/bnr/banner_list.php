<?PHP

/* getBanners function read files which name begin by "bnr_".
 *
 */

require_once("banner_lib.php");

$bnrs = getBanners();

print_r($bnrs);

foreach($bnrs as $bnr)
	{
	echo "<img src=\"$bnr\"><br/>";
	}


?>
