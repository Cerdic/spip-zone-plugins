function toggle(formObj, checkName) {
    for (var i = 0; i < formObj.elements.length; i++) {
        if(formObj.elements[i].name == checkName && formObj.elements[i].type == 'checkbox') {
            formObj.elements[i].checked = !(formObj.elements[i].checked);
        }
    }
    return true;
}

function checkbox2input(formObj, checkName, inputObj)
{
	var data = '';
	for (i = 0; i < formObj.elements.length; i++) {
	    obj = formObj.elements[i];
        if(obj.name == checkName && obj.type == 'checkbox' && obj.checked) {
            data = data + obj.value + ';';
        }
	}
	inputObj.value = data;
	return true;
}
