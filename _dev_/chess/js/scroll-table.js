function addEndCol(obj)
{
	if(document.all)return;
	var rows = obj.getElementsByTagName('TR');
	for(var no=0;no<rows.length;no++){
		var cell = rows[no].insertCell(-1);
		cell.innerHTML = ' ';
		cell.style.width = '13px';
		cell.width = '13';
	}			
}
function cancelTableWidgetEvent()
{
	return false;
}

function createScrollTable(objId,width,height)
{
	var isMSIE = navigator.userAgent.indexOf('MSIE')>=0?true:false;
	width = width + '';
	height = height + '';
	var obj = document.getElementById(objId);
	if(obj.parentNode.className){
		obj.parentNode.className = obj.parentNode.className + ' widget_tableDiv';
	}else{
		obj.parentNode.className='widget_tableDiv';
	}
	if(isMSIE){
		obj.parentNode.style.overflowY = 'scroll';
	}		
	if(width.indexOf('%')>=0){
		obj.style.width = '100%';
		obj.parentNode.style.width = width;
	}else{
		obj.style.width = width + 'px';
		obj.parentNode.style.width = width + 'px';
	}
	if(height.indexOf('%')>=0){
		obj.parentNode.style.height = height;			
	}else{
		obj.parentNode.style.height = height + 'px';
	}
	if(!isMSIE){
		addEndCol(obj);
	}else{
		obj.style.cssText = 'width:expression(this.parentNode.clientWidth)';
	}
			
	
	obj.cellSpacing = 0;
	obj.cellPadding = 0;
	obj.className='tableWidget';
	var tHead = obj.getElementsByTagName('THEAD')[0];
	var cells = tHead.getElementsByTagName('TD');
	for(var no=0;no<cells.length;no++){
		cells[no].className = 'tableWidget_headerCell';
		cells[no].onselectstart = cancelTableWidgetEvent;
		if(no==cells.length-1){
			cells[no].style.borderRight = '0px';	
		}
		cells[no].style.cursor = 'default';			
	}		
	var tBody = obj.getElementsByTagName('TBODY')[0];
	if(document.all && navigator.userAgent.indexOf('Opera')<0){
		tBody.className='scrollingContent';
		tBody.style.display='block';			
	}else{
		tBody.className='scrollingContent';
		tBody.style.height = (obj.parentNode.clientHeight-tHead.offsetHeight) + 'px';
		if(tBody.style.height=='0px')tBody.style.height = (height-20) + 'px';
		if(navigator.userAgent.indexOf('Opera')>=0){
			obj.parentNode.style.overflow = 'auto';
		}
	}
}