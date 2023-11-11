// function for edit boxes
function txtNormal(obj) {
  obj.className="edit1";
}
function txtFocus(obj) {
  obj.className="edit2";
}

// functions for buttons
function btnNormal(obj) {
    window.status = "";
    obj.className = obj.className.replace("button2", "button1");
}
function btnOnFocus(obj) {
    window.status = obj.value;
    obj.className = obj.className.replace("button1", "button2");
}

function tdOnFocus(obj)
{
	window.status = obj.value;
  obj.className="td-red";
}

function tdNormal(obj)
{
	window.status = obj.value;
  obj.className="td-grey";
}

// functions for radio buttons
function radioNormal(obj) {
  obj.className="radio1";
}
function radioOnFocus(obj) {
  obj.className="radio2";
}

function setBtn2(which) {

   document.getElementById(which+'btnMiddle').className='button2';
   //document.getElementById(which+'btnLeft').src = picLeftOn.src;
   //document.getElementById(which+'btnRight').src = picRightOn.src;   
}

function unsetActive(which)
{
   document.getElementById(which+'btnMiddle').className='button1';
   //document.getElementById(which+'btnLeft').src = picLeft.src;
   //document.getElementById(which+'btnRight').src = picRight.src;
  // document.getElementById(which+'lnk').className='filter';
   
}


function disable(id1, id2) {

    if (document.getElementById(id1))
        document.getElementById(id1).disabled = true;

    if (id2)
        if (document.getElementById(id2))
            document.getElementById(id2).disabled = true;
            
    return true;
}

function DonotAllowSpacesInATextBox(event) {

    var keyCode = event.keyCode || event.which;
    if (keyCode == 32) {
        event.returnValue = false;        
        return false;
    } 
}

function disableInputs(idToDisable) {
    var el = document.getElementById(idToDisable),
        all = el.getElementsByTagName('input'),
        i;
    for (i = 0; i < all.length; i++) {
        all[i].disabled = true;
    }
}
