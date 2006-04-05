function findPosX(obj)
{
	var curleft = 0;
	curleft += obj.offsetLeft;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			obj = obj.offsetParent;
			curleft += obj.offsetLeft;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	curtop += obj.offsetTop;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			obj = obj.offsetParent;
			curtop += obj.offsetTop;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

SPIP.widget.Calendar2up_INT_Cal = function(id, containerId, monthyear, selected) {
	if (arguments.length > 0)
	{
		this.init(id, containerId, monthyear, selected);
	}
}

SPIP.widget.Calendar2up_INT_Cal.prototype = new SPIP.widget.Calendar2up_Cal();

SPIP.widget.Calendar2up_INT = function(id, containerId, monthyear, selected) {
	if (arguments.length > 0)
	{	
		this.buildWrapper(containerId);
		this.init(2, id, containerId, monthyear, selected);
	}
}

SPIP.widget.Calendar2up_INT.prototype = new SPIP.widget.Calendar2up();

SPIP.widget.Calendar2up_INT.prototype.constructChild = function(id,containerId,monthyear,selected) {
	var cal = new SPIP.widget.Calendar2up_INT_Cal(id,containerId,monthyear,selected);
	return cal;
};
