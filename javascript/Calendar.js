jQuery(document).ready(

	function(){
		Calendar.init();
	}


);


var Calendar = {

	navigationbarID: "",

	calenderHolderID: "",

	init: function(){
		var prev = jQuery(this.navigationbarID+".previous.ajaxified a");
		var previousLink  = jQuery(prev).attr("rel");
		jQuery(prev).attr("href", previousLink);
		var next = jQuery(this.navigationbarID+".next.ajaxified a");
		var nextLink = jQuery(next).attr("rel");
		jQuery(next).attr("href", nextLink);
	}

}
