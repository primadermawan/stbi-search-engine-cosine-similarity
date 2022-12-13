$(document).ready(function(){
		$(".accordion-toggle").one("click", function(){
			var id_group = $(this).attr('value');
			var csrf = $("input:hidden[name='csrf_prima']").val();
			var key = $(this).attr('href');
			// $.support.cors = true;
			$.ajax({
					type: "POST",
					url: Setting.url,
					data: {id_group: ""+id_group, csrf_prima: ""+csrf},
					//"id_group=" +id_group,
					success: function(hai){
						var obj = jQuery.parseJSON(hai);
						$.each(obj, function(index, value){
							$(key).children().append($("<label>").attr({class:"checkbox", id:"ayam"+index}).text(""+value.username));
							$(key).find("label#ayam"+index).prepend($("<input>").attr({type:"checkbox",name:"penerima[]",value: "" +value.id}));
							if (value.id == Setting.id_me){
								$(key).find("label#ayam"+index).attr({class: "hidden", name:"hidden"});
								$(key).find("label#ayam"+index).children().attr({type: "hidden" });
							}
						});
					}
			});
		});
	});


$(document).ready(function(){
		var keys = $(".accordion-toggle").size();
		var index = new Array();
		for(var i=0; i<keys; i++){
			index[i] = "#button"+i;
		}
		
		$(''+index).click(function(){
			var target = $(this).siblings('label').children();
			if($(this).attr('class') == "btn btn-warning active")
			{
				target.removeAttr('disabled').prop('checked', false);
				$(this).siblings('.buttoninput').remove();
			} else {
				target.prop('checked', true).attr('disabled', 'disabled');
				$(this).parent().prepend($("<input>").attr({class: 'buttoninput', type: 'hidden', value: ''+$(this).attr('value'), name: 'button[]'}));
				// target.clone(true).appendTo($(this).parent()).attr({type: "hidden", class: "clear"}).removeAttr('disabled');
			}
		});
	});