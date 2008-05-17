<?php

function swfupload_form() {
$swfupload_form = '
<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
		<div>'._T('swfupload:texte_swfupload').'</div>

		<div class="content">
			<fieldset class="flash" id="fsUploadProgress">
				<legend>'._T('swfupload:texte_uploadqueue').'</legend>
			</fieldset>
			<div id="divStatus">0 '._T('swfupload:texte_filesupload').'</div>
			<div>
				<input type="button" value="'._T('swfupload:texte_boutonupload').'" onclick="swfu.selectFiles()" style="font-size: 8pt;" />
				<input id="btnCancel" type="button" value="'._T('swfupload:texte_cancelupload').'" onclick="swfu.cancelQueue();" disabled="disabled" style="font-size: 8pt;" /><br />

			</div>
		</div>
	</form>';
return $swfupload_form;
}
?>
