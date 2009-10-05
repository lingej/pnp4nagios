/*
*
*
*
*/


$(document).ready(function(){ 

	var path = location.pathname.split("/");
	path = "/" + path[1] + "/";
	$("img").fadeIn(1500);

	$("#basket_action_add a").click(function(){
		var item = (this.id)
		$.ajax({
      		type: "POST",
      		url: path + "ajax/basket/add",
      		data: { item: item },
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});

  	$("#basket_action_remove-all a").click(function(){
		$.ajax({
      		type: "POST",
      		url: path + "ajax/basket/remove-all/",
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});
  	$("#basket_action_remove a").live("click", function(){
		var item = (this.id)
		$.ajax({
      		type: "POST",
      		url: path + "ajax/basket/remove/"+item,
      		data: { item: item },
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});
	$("#remove_timerange_session").click(function(){
		$.ajax({
      		type: "GET",
      		url: path + "ajax/remove/timerange",
			success: function(){
				location.reload();
			}
    	});
  	});

});

