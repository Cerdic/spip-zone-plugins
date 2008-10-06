/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| diverses functions ; non une !
+--------------------------------------------+
*/
/* transfert option rubrique iframe dw2_affect_doc*/
function rechercheart() {
	var index=document.designedoc.id_rub.selectedIndex;

	if(document.designedoc.id_rub.options[index].value=="") {
		alert("rubrique non fournie ! ");
		return;
	}

	window.frames['action_dw2'].document.table_art.id_rub.value = document.designedoc.id_rub.options[index].value;
	
	window.frames['action_dw2'].document.table_art.submit();

}
