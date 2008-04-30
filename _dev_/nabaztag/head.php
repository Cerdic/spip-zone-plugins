<?php

function Nabaztag_insert_head($flux){
$id_token = lire_config('googleanalytics/idGoogle');

if (!$id_google || $id_google == '_' || $id_google == 'UA-xxxxxx') {
		return '';
	}
	else {

$flux .= '
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "'.$id_google.'";
urchinTracker();
</script>';
return $flux;
}
}

?>
