<?php
function loopedslider_insert_head($flux)
{
	$flux .= '<script type="text/javascript" src="'.url_absolue(find_in_path('js/jquery.tagsphere.js')).'"></script>';

return $flux;
}
?>