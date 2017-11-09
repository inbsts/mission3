$(function(){

$('#show-add').click(function(){
	$('#add').slideDown();
	
	var position = $('#add').offset().top;
	$('html,body').animate({"scrollTop":position},"500");
});

$('.close-btn').click(function(){
	$('#add').slideUp();
	$('#edit').slideUp();
	$('#delete-mode').fadeOut();
	$('#edit-mode').fadeOut();

	$('.cards').css("position","unset");
});

$('.front').click(function(){
	$(this).hide();
	$(this).parent('.card-container').find('.back').css("display","inline-block");
});

$('.back').click(function(){
	$(this).hide();
	$(this).parent('.card-container').find('.front').css("display","inline-block");
});

$('#front').click(function(){
	$('.card').hide();
	$('.front').css("display","inline-block");
});

$('#back').click(function(){
	$('.card').hide();
	$('.back').css("display","inline-block");
});

$('.card').hover(
	function(){
		$(this).parents('.card-wrapper').find('.card-detail').fadeIn();
	},
	function(){
		$(this).parents('.card-wrapper').find('.card-detail').fadeOut();
	}
);

$('#show-delete').click(function(){
	$('html,body').scrollTop(0);
	$('.cards').css("position","fixed");
	$('#delete-mode').show();
	$('.back').hide();
	$('.front').css("display","inline-block");
});


$('#show-edit').click(function(){
	$('html,body').scrollTop(0);
	$('.cards').css("position","fixed");
	$('#edit-mode').show();
	$('.front').css("display","inline-block");
});


});