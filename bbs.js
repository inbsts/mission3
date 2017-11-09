$(function(){

$('.delete-open').click(function(){
	$(this).parents('.btns').find('.delete-alarm').fadeIn();
	$(this).parents('.btns').find('.btn-option').slideUp();

});

$('.delete-close').click(function(){
	$(this).parents('.btns').find('.btn-option').slideDown();
	$(this).parent('.delete-alarm').hide();
});

$('#post-show').click(function(){
	$('.post-wrapper').slideDown();
	
	var position = $('.post-wrapper').offset().top;
	$('html,body').animate({'scrollTop':0},'500');
});

$('#close').click(function(){
	$('.post-wrapper').slideUp();
});




});