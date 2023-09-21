/**
 * @author Cognizant
 * @version 1.0
 * Revised Date: 2010/06/24
 * @copyright (c) 2010 by LexisNexis Asset Company. All rights reserved.
 * @package Javascript
 * @access Public
 *  Javasript for security
 */



function secure_ajax( url, vars, callbackFunction ) {
	var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( "MSXML2.XMLHTTP.3.0" );
	request.open( "POST", url, true );
	request.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
	request.onreadystatechange = function()
	{
		if ( request.readyState == 4 && request.status == 200 )
		{
			if ( callbackFunction != null )
			{
				callbackFunction( request.responseText );
			}
		}
	};
	request.send( vars );
}