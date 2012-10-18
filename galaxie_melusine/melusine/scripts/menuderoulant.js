function hover(obj) {
	if(document.all) {
		UL = obj.getElementsByTagName('ul');
		if(UL.length > 0) {UL[0].style.display = 'block';}
	}
}

function hout(obj) {
	if(document.all) {
		UL = obj.getElementsByTagName('ul');
		if(UL.length > 0) {UL[0].style.display = 'none';}
	}
}
function setHover(){
	LI = document.getElementById('nav1').getElementsByTagName('li');
	nLI = LI.length;
	for(i=0; i < nLI; i++){
		LI[i].onmouseover = function(){hover(this);}
		LI[i].onmouseout = function(){hout(this);}
	}
}
