// public facing js for basic style option
(function( $ ) {

  //resize slider images accourding to container size
  var umbcimagesizer = {
    imageSizer: function() {
      var $container = $('div.carousel-item.active > .um-carousel-image-container');
      var containerheight = $container.height();
      var containerwidth = $container.width();
      var $image = $('.carouselimg');
      $($image).each(function() {
        console.log("container width: "+containerwidth+", "+"container height: "+containerheight);
        console.log("image width: "+this.naturalWidth+", "+"image height: "+this.naturalHeight);

  			var newcss=umis.zoom(containerwidth,containerheight,this.naturalWidth,this.naturalHeight);
  			this.style.cssText=newcss;
  		});
    }

  };

  $( window ).load(function() {
    umbcimagesizer.imageSizer();
  });

  $( window ).resize(function() {
    umbcimagesizer.imageSizer();
  });



})( jQuery );
