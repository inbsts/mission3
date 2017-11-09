$(function(){

$('.box').hover(
	function(){
		$(this).find('.circle').css("box-shadow","inset 1px 2px 4px 0px black");
		$(this).css("box-shadow","0 0 4px 1px rgba(0, 0, 0, 0.37)")
	},
	function(){
		$(this).find('.circle').css("box-shadow","inset 13px 14px 16px 7px rgba(0, 0, 0, 0.28)");
		$(this).css("box-shadow","0 0 10px 3px rgba(0, 0, 0, 0.29)");
	}
);

$('.circle').click(function(){
	location.href = $(this).prev('a').attr("href");
});



});