


function checkAll(field)
{
var field2=document.getElementsByName(field);
for (i = 0; i < field2.length; i++)
	field2[i].checked = true ;
}

function uncheckAll(field)
{
var field2=document.getElementsByName(field);
for (i = 0; i < field2.length; i++)
	field2[i].checked = false ;
}
