startList = function() 
{
	if (document.all&&document.getElementById) 
	{
		navRoot = document.getElementById("abcal_table");
		uls=navRoot.getElementsByTagName('ul');
		for(j in uls)
		{
   			if(/multievent/.test(uls[j].className))
   			{
				for (i=0; i<uls[j].childNodes.length; i++) 
				{
					node = uls[j].childNodes[i];
					if (node.nodeName=="LI") 
					{
						node.onmouseover=function() {
						this.className+=" over";
  						}
  						node.onmouseout=function() {
  						this.className=this.className.replace(" over", "");
   						}
   					}
   				}
  			}
 		}	
	}
}
window.onload=startList;