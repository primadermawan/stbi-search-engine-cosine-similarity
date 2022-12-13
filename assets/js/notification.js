

$(document).ready(function(){
	
	function clock(){
		$("#interval").empty();
		$.ajax({
			type: "POST",
			url: ""+conf.noti,
			data: {id_user: ""+conf.id_me, csrf_prima: ""+conf.csrf},
			success: function(hai){
				var obj = jQuery.parseJSON(hai);
				var x = obj.length;
				
				if(x > 10){
					for(var i=0; i<10; i++){
						$("#interval").append(obj[i].div);
					}
					$("#interval").append($("<li>").append($("<a></a>").attr('href', conf.nott).text(" "+x-10+" more unread notifications").prepend($("<i></i>").attr('class', 'icon-th-list'))));
				} else {
					$.each(obj, function(index, value){
						$("#interval").append(value.div);
					});
					$("#interval").append($("<li>").append($("<a></a>").attr('href', conf.nott).text(" see all notification").prepend($("<i></i>").attr('class', 'icon-th-list'))));
				}
			}
		});
	}
	clock();
	setInterval(function(){clock()}, 30000);
});