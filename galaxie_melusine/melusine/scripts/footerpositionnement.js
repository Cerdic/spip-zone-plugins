function setFooter() {
	var gaucheHeight=document.getElementById('gauche').offsetHeight;
	var centreHeight=document.getElementById('centre').offsetHeight;
	var droiteHeight=document.getElementById('droite').offsetHeight;
	var heightGDMax = Math.max(droiteHeight, gaucheHeight)
	if (heightGDMax>centreHeight){
	   document.getElementById('centre').style.height=(heightGDMax)+'px';
	}
}