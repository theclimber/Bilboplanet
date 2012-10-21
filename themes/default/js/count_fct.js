/*********************************************************************************************/
//  To set the countdown timer's target change the values of below variables
var day     = 27;         //  target day
var month   = 12;         //  target month
var year    = 2010;       //  target year
var hour    = 23;         //  target hour
var minutes = 59;         //  target minutes 
var seconds = 00;         //  target seconds
/**********************************************************************************************/

$(document).ready(function(){

	$(function () {
		var austDay = new Date();
		austDay = new Date(year, month - 1, day, hour, minutes, seconds );
		$('#count_down').countdown({until: austDay});
	});
});

