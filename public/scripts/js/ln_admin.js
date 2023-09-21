/**
 * @author Cognizant
 * @version 1.0
 * Revised Date: 2010/06/24
 * @copyright (c) 2010 by LexisNexis Asset Company. All rights reserved.
 * @package Javascript
 * @access Public
 * LexisNexis ln admin
 */

// delete confirmation
$(document).ready (function () {
	$('.delete').click (function () {
		return confirm ("Are you sure you want to delete " + $(this).attr ("title") + "?") ;
	}) ; 
}) ;

/*Datepicker ver 1.0
Author: Abhi*/

$(function() {
	  $("#datepicker").datepicker();
	//  $("#datepicker").datepicker({minDate: -1, maxDate: '+2Y +12M'});
	  
	 });