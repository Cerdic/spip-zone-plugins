
 
if (jQuery.browser.msie) {
	function hover(obj) {
		if(document.all) {
			UL = obj.getElementsByTagName('ul');
			if(UL.length  0) {UL[0].style.display = 'block';}
		}
	}
	
	function hout(obj) {
		if(document.all) {
			UL = obj.getElementsByTagName('ul');
			if(UL.length  0) {UL[0].style.display = 'none';}
		}
	}
	function setHover(){
		if (document.getElementById('menu_rubriques')) {
			LI = document.getElementById('menu_rubriques').getElementsByTagName('li');
			nLI = LI.length;
			for(i=0; i  nLI; i++){
				LI[i].onmouseover = function(){hover(this);}
				LI[i].onmouseout = function(){hout(this);}
			}
		}
	}

	jQuery(document).ready(function() {
		setHover()
	});
}
