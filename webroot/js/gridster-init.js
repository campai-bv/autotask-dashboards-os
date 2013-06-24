var iTotalWidth = $(document).width();
var iMarginFull = 30;
var iMarginHorizontal = 10;
var iMarginVertical = 10;

var iUnitWidth = ( iTotalWidth - ( 5 * iMarginFull ) ) / 6;

if( iTotalWidth <= 1050 ) {

	iUnitHeight = iTotalWidth/5.2;

} else if(
	iTotalWidth > 1050
	&&
	iTotalWidth <= 1280
) {

	iUnitHeight = iTotalWidth/6;

} else if(
	iTotalWidth > 1280
	&&
	iTotalWidth < 1900
) {

	iUnitHeight = iTotalWidth/7.5;

} else {
	iUnitHeight = iTotalWidth/9;
}