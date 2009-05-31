<?php

include_spip('inc/widgets/Widget');

function callback_MCEWidget(&$content) {
	static $tag='<!-- TINY_MCE -->';
	$len=strlen($tag);
	if(substr($content, 0, $len)==$tag) {
		include_spip('inc/sale');
		$content= sale(substr($content, $len));
	}
	error_log("callback_MCEWidget => $content");
	return null;
}

//
// Un Widget avec une verification de code de securite
// A priori, tous les widgets devraient dériver de celui ci
//
class MCEWidget extends Widget {
	static $first= true;

	function code($callbacks=null) {
		return parent::code($callbacks.';widgets/MCEWidget:MCEWidget');
	}

	function input() {
		$res= '';
		if(MCEWidget::$first) {
			$res.= "<script language='javascript' type='text/javascript' src='".find_in_path('js/tiny_mce.js')."'></script>
<script language='javascript' type='text/javascript' src='".find_in_path('js/mce_widget2.js')."'></script>
";
			MCEWidget::$first= false;
		}
		$res.= '<textarea onfocus=\'toggleEdit("content_'.$this->key.'");\' id="content_'.$this->key.'" name="content_'.$this->key.'">'
				. htmlspecialchars($this->text) . "</textarea>\n";
		return $res;
	}

}

?>
