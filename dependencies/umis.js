/**
 * Image Sizer by Bryce Leue
 * Description : Resize images with javascript
 * Version : 1.0.0
 * Author : Bryce Leue
 * Author Email : bryce@umethod.net
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Features:
 *
 * SUMMARY
 * This utility gives you two methods for resizing images according to a parent container.
 * It is intended to be used with a parent container that has predefined dimensions.  It requires the initial dimensions for the image, and the parent dimensions.
 * It returns a string formatted for use with cssText.  There are two methods of resizing, zoom and center.  "Center" ensures that the larger of the two dimensions will expand to the
 * container boundaries, while the smaller will be centered.  "Zoom" ensures that the smaller of the two dimensions will expand to the boundaries of the container, while the larger will be cropped
 * with the image remaining centered.
 *
 * usage exaple:
 * var container = document.getElementById( mycountainerid );
 * var image = document.getElementById( myimg );
 * containerwidth = container.offsetWidth;
 * containerheight = container.offsetHeight;
 * imagewidth = image.naturalWidth;
 * imageheight = image.naturalHeight;
 *
 * window.onresize = function() {
 *  image.style.cssText=umis.zoom(containerwidth, containerheight, imagewidth, imageheight);
 *
 * };
 *
 *
 *
 **/


var umis = {

	center:function(imageWidth,imageHeight,parentWidth,parentHeight) {
		//center images horizontally and vertically with imageSizer.center()
		//images will not exceed the parent container

		var parentwidth=parentWidth;
		var parentheight=parentHeight;

		var imgWidth=imageWidth;
		var imgHeight=imageHeight;

		var topmargin=0;
		var leftmargin=0;


		//image native dimensions exceed parent
		//resize image
		if(parentheight<imgHeight || parentwidth<imgWidth) {


			//get multiplier for resizing of image
			var ar=imgWidth>=imgHeight ? imgHeight/imgWidth : imgWidth/imgHeight;

			//get orientation for checking
			var artype=imgWidth>=imgHeight ? "landscape" : "portrait";

			var parentratio=parentWidth/parentHeight;
			var imageratio=imageWidth/imageHeight;


			if(parentratio>imageratio) {

				imgHeight=parentheight;
				artype=="landscape" ? imgWidth=imgHeight/ar : imgWidth=imgHeight*ar;


			} else if(parentratio<imageratio) {

				imgWidth=parentwidth;
				artype=="portrait" ? imgHeight=imgWidth/ar : imgHeight=imgWidth*ar;

			//parent is 1:1
			} else {

				if(artype=="landscape") {

					imgWidth=parentwidth;
					imgHeight=imgHeight=imgWidth*ar;

				} else {

					imgHeight=parentheight;
					imgWidth=imgHeight*ar;

				}

			}


		}

		//set margins
		if(parentheight>imgHeight) { topmargin=(parentheight-imgHeight)/2; }

		if(parentwidth>imgWidth) { leftmargin=(parentwidth-imgWidth)/2; }

		//return string
		return 'height:'+imgHeight+'px;width:'+imgWidth+'px;margin-left:'+leftmargin+'px;margin-top:'+topmargin+'px;';

	},


	zoom: function(containerW, containerH, imageW, imageH) {
		//compare aspect ratio of container to image to determine whether x or y gets increased
		//value with a relative greater difference in size will be increased to size of container while the other will get a calculated value
		//this ensures that the image will always fill the container, but not be too zoomed in
		//the image dimension with a lesser difference will be larger than its container, so overflow:hidden is recommended

		//get aspect ratios
		var containerratio=containerW/containerH;
		var aspectratio=imageW/imageH;

		//create variables to represent dynamically changing image dimensions
		//initally store native dimensions
		var imagewidth=imageW;
		var imageheight=imageH;

		//create variables to store values that can be used as negative margins
		//xoffSet is for horizontal centering yoffSet for vertical centering
		var xoffSet=0;
		var yoffSet=0;
		//assign calculations based on aspect ratios
		if(containerratio < aspectratio) {
			//get multiplier for dimension calculation
			var multiplier=containerH/imageheight;
			//set image height to container height
			imageheight=containerH;
			//set image width with new image height and aspect ratio
			imagewidth=imagewidth*multiplier;
			//if not store half of extra width in xoffset
			xoffSet='-'+((imagewidth-containerW)/2)+'px';
		} else if(containerratio > aspectratio) {
			//get multiplier for dimension calculation
			var multiplier=containerW/imagewidth;
			//set image width to container width
			imagewidth=containerW;
			//set image height with multiplier
			imageheight=imageheight*multiplier;
			//store half of extra height in yoffset
			yoffSet='-'+((imageheight-containerH)/2)+'px';
		} else {
			//if image aspect ratio is identical set dimensions equal to container
			imagewidth=containerW;
			imageheight=containerH;
		}
		//return string with dimensions and margins
		return 'width:'+imagewidth+'px;height:'+imageheight+'px;margin:'+yoffSet+' 0 0 '+xoffSet+';';
	}
}
