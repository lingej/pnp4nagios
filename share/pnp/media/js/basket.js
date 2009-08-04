/*
*
*
*
*/

$(document).ready(function(){ 

	$("#basket_action_add a").click(function(){
		var item = (this.id)
		$.ajax({
      		type: "POST",
      		url: "ajax/basket/add",
      		data: { item: item },
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});

  	$("#basket_action_remove-all a").click(function(){
		$.ajax({
      		type: "POST",
      		url: "ajax/basket/remove-all/",
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});
  	$("#basket_action_remove a").live("click", function(){
		var item = (this.id)
		$.ajax({
      		type: "POST",
      		url: "ajax/basket/remove/"+item,
      		data: { item: item },
      		success: function(msg){
    			$("#basket_items").html(msg);
      		}
    	});
  	});
});

