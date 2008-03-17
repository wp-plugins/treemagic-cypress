//RodwanS 31/12/2007 change the script to replacing the display to visibility
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ''); };
//String.prototype. Capitalizing the first character in a string
var isUsed = false;
var strSelectedText = "Selected Test" ;
// Getting the selectes test
function getSelectionText() {
    
	var strSelectedText = "" ;

    if (document.getSelection) {
        strSelectedText = document.getSelection();
        
    } else if (document.selection && document.selection.createRange) {
        
        var range = document.selection.createRange();
        strSelectedText = range.text;
        
    } else {
        
        strSelectedText = "Sorry, this is not possible with your browser.";
        
    }
    
    return strSelectedText.trim() ;
    
}

//  brows the selected URL
function browsURL(URLRequested, oprationNumber) {

	isUsed = true;
    
    strSelectedText = strSelectedText.charAt(0).toUpperCase() + strSelectedText.substring(1, strSelectedText.length).toLowerCase();  //BG 2007.08.08: Capitalizing the first character in a string
    if ( oprationNumber == 0 ) {
        //open in same window
        window.location.href = URLRequested + encodeURIComponent(strSelectedText ) ;
    }else{
        //Open in a new window..
        if ( strSelectedText ){
			//BasheerG 2008.03.05: Supporting GreyBox feature
			//window.open( URLRequested + encodeURIComponent(strSelectedText ), 'dict', 'width=700,height=500,resizable=1,menubar=1,scrollbars=1,status=1,titlebar=1,toolbar=1,location=1,personalbar=1');
			GB_showCenter('TM-Cypress, Search Result:', URLRequested + encodeURIComponent(strSelectedText ), 500, 700 );
		}
    }
    
}//End function

//Getting the location of the Mouse
function getMouseXY(e) {
    var posX = 0;
    var posY = 0;
    var e = (!e) ? window.event : e;
    if (e.pageX || e.pageY) {
        posX = e.pageX;
        posY = e.pageY;
    } else if (e.clientX || e.clientY) {
        if (document.body.scrollLeft || document.body.scrollTop) {
            posX = e.clientX + document.body.scrollLeft;
            posY = e.clientY + document.body.scrollTop;
        } else {
            posX = e.clientX + document.documentElement.scrollLeft;
            posY = e.clientY + document.documentElement.scrollTop;
        }
    }	
	var menu = document.getElementById( "aw_popup" );
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth + window.pageXOffset;
		myHeight = window.innerHeight +  window.pageYOffset;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth + document.body.scrollLeft;
		myHeight = document.documentElement.clientHeight + document.body.scrollTop;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth + document.documentElement.scrollLeft;
		myHeight = document.body.clientHeight + document.documentElement.scrollTop;
	}
	var menuWidth = menu.offsetWidth;
	var menuHeight = menu.offsetHeight;
	if ( posX + menuWidth > myWidth )
	{
		posX = posX - menuWidth - 20  ;
		
	}
	if ( posY + menuHeight > myHeight )
	{
		posY = posY - menuHeight - 20 ;
	}
	
		
	if ( myWidth - posX > 400 )
	{
		var elements = menu.getElementsByTagName( "li" );
		for( index = 0; index < elements.length ; index++){
			var classType = elements[index].className;
			if ( classType )
			{
				elements[index].className = 'subRight';
				var container = elements[index].getElementsByTagName( "ul" )[0];
				var subMenu = container.getElementsByTagName( "li" ).length ;
				var containerLength = subMenu  * 26  ;
				var availableHeight = posY + elements[index].offsetTop + containerLength ;
				if ( availableHeight > myHeight )
				{
					container.style.top = -1 * ( containerLength - 26 ) + "px";
				}else{
					container.style.top ="0px";
				}
			}
		}
	}else {
		var elements = menu.getElementsByTagName( "li" );
		for( index = 0; index < elements.length ; index++){
			classType = elements[index].className;
			if ( classType )
			{
				elements[index].className = 'subLeft';
				var container = elements[index].getElementsByTagName( "ul" )[0];
				var subMenu = container.getElementsByTagName( "li" ).length ;
				var containerLength = subMenu  * 26  ;
				var availableHeight = posY + elements[index].offsetTop + containerLength ;
				container.style.top = -1 *container.offsetHeight + "px";
				if ( availableHeight > myHeight )
				{
					container.style.top = -1 * ( containerLength - 26 )  + "px";
				}else{
					container.style.top ="0px";
				}
			}
		}
	}
	
    menu.style.left = posX + "px";
    menu.style.top = posY + "px";
    menu.style.visibility = "visible";
}

//Hide the poup Menu
function hide_menu( clear ) {
	//remove selection if exist
	if (clear == 1 )
	{
		if (document.getSelection) {
			window.getSelection().removeAllRanges() ;
		}else if (document.selection && document.selection.createRange) {
			document.selection.empty();
		}
	}
		
    //get the named menu
    var menu_element = document.getElementById( "aw_popup" );
    //hide it with a style attribute
    menu_element.style.visibility = "hidden";
    
}
document.onmouseup = showManu ;
document.ondblclick = showManu ;
//document.onmousedown = hide_menu;

//Show the Menu
function showManu(e) {

	var e = (!e) ? window.event : e;
    var strSelectedTextTemp = getSelectionText() ;
	isUsed = (strSelectedTextTemp.toLowerCase() == strSelectedText.toLowerCase());
	if ( !isUsed )
	{
		//var visibility = document.getElementById("aw_popup").style.visibility;
		if ( strSelectedTextTemp )
		{
			strSelectedText = strSelectedTextTemp;
			getMouseXY(e);		
		}else{	
			hide_menu( 0 ) ;
		}
	}else if ( e.type == 'dblclick' && strSelectedTextTemp){
		strSelectedText = strSelectedTextTemp;
		getMouseXY(e);
	}else {
		hide_menu( 0 ) ;
	}

}	