//Admin facing js

(function( $ ) {

  //resize slider images accourding to window size
  var umslidermanager = {
    //fix wp_editor iframe height
    setwpeditorheight: function() {
      var additem = document.getElementById("umaddslideritemdetails_ifr");

      if(additem  !== null) additem .style.cssText="width: 100%; height: 100px; display: block;";

      var edititem = document.getElementById("umslideredititemdetails_ifr");

      if(edititem  !== null) edititem.style.cssText="width: 100%; height: 100px; display: block;";
    },

    thumbnailsizer: function() {
      var $container = $('div.umslideritemimagepreview');
      var containerheight = $container.height();
      var containerwidth = $container.width();
      var $image = $('img.umslideritemimage');
      $($image).each(function() {
  			var newcss=umis.zoom(containerwidth,containerheight,this.naturalWidth,this.naturalHeight);
  			this.style.cssText=newcss;
  		});
    },

    //wp media uploader
    mediaupload: function(id) {
      //the upload button
      var mediaUploader;

      $('#'+id+'upload').click(function(e) {
        e.preventDefault();
        if(mediaUploader) {
          mediaUploader.open();
          return;
        }

        //extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
              title: 'Choose Image',
              button: {
                text: 'Choose Image'
              },
              multiple: false
        });

        //when a file is selected, grab url and set it as the text field's value
        mediaUploader.on('select', function() {
          var attachment = mediaUploader.state().get('selection').first().toJSON();
          $('#'+id).val(attachment.id);
          $('#'+id+'preview').attr('src', attachment.url);
        });
        mediaUploader.open();
      });


    },

    bindeditbuttons: function() {
      //use json object umslideritems
      if(typeof umslideritems === 'undefined') return;
      for(var key in umslideritems) {

        (function(id) {

            document.getElementById('umslideritemeditbutton'+id).addEventListener('click', function(e){
              e.preventDefault();
              umslidermanager.populateitemeditor(id);
            });
        })(key);
      }
    },

    populateitemeditor: function( id ) {

      //store the unpopulated block of
      var title = umslideritems[id]['title'];
      var details = umslideritems[id]['details'];
      var imgurl = umslideritems[id]['imgurl'];
      var attachmentid = umslideritems[id]['attachmentid'];
      var pointertext = umslideritems[id]['pointertext'];
      var pointerurl = umslideritems[id]['pointerurl'];

      $('#umslideritemid').val(id);
      $('#umslideredititemtitle').val(title);
      $('#umslideredititemimage').val(attachmentid);
      $('#umslideredititemimagepreview').attr('src', imgurl);

      //set editor content

      if($('#wp-umslideredititemdetails-wrap').hasClass('html-active')) {
        $('#umslideredititemdetails').val(details);
      } else {
        details = details.replace(/\r/gi, '');
        detailsarr = details.split('\n');
        var formatteddetails='';
        var detailslength=detailsarr.length;
        for(var i=0;i<detailslength;i++) {
          if(detailsarr[i]==='') continue;
          formatteddetails+='<p>'+detailsarr[i]+'</p>';
        }
        var activeEditor = tinyMCE.get('umslideredititemdetails');
        activeEditor.setContent(formatteddetails);
      }

      $('#umslideredititempointer').val(pointertext);
      $('#umslideredititempointerurl').val(pointerurl);
    },

    bindsettings: function() {
      $('#umbcsettingsstyledd .dropdown-item').each(function(){
        (function(obj){
          obj.addEventListener('click', function (e){
            e.preventDefault();
            $("#umbc_settings_style").val(obj.id);
            if(!umslidermanager.styleclicked) umslidermanager.stylehtml = $("button#settingsdropdownMenuButton").html();
            var selected = $("#"+obj.id).html();
            $("button#settingsdropdownMenuButton").html(umslidermanager.stylehtml+': '+selected);
            umslidermanager.styleclicked=1;

            $('#umbcsettingsstyledd .dropdown-item').each(function(){
              if(this.id===obj.id) {
                document.getElementById(this.id).className="dropdown-item active";
              } else {
                document.getElementById(this.id).className="dropdown-item";
              }
            });
          });
        })(this);
      });
    },

    //if expired notice querystring is still in url strip it out
    cleanupquerystrings: function() {
      var val = Number(umbcclearnotice);

      if(val) {
        var qstr = window.location.search;
        var i=1;
        qstr = qstr.split('&');
        var len = qstr.length;
        var url = window.location.href;
        url = url.split("?", 2);
        url = url[0];
        console.log(url);
        url += qstr[0];
        for(i;i<len;i++) {
          //keep querystring if it isn't the expired notice query
          if(qstr[i].search('umbcnotice') === -1) url+='&'+qstr[i];
        }
        //only attempt to string the querystring if browser supports html5
        if (typeof (history.pushState) != "undefined") {
          var obj = { Title: 'initialstate', Url: url };
          history.pushState(obj, obj.Title, obj.Url);
        }
      }
    },

    afterresize: function() {
      clearTimeout(umslidermanager.thumbnails);
      umslidermanager.thumbnails = setTimeout(umslidermanager.thumbnailsizer, 500);
    }


  };

  $( window ).load(function() {
    umslidermanager.setwpeditorheight();
    umslidermanager.thumbnailsizer();
    umslidermanager.bindeditbuttons();
  });
  $( document ).ready(function(){
    umslidermanager.cleanupquerystrings();
    umslidermanager.mediaupload('umaddslideritemimage');
    umslidermanager.mediaupload('umslideredititemimage');
    umslidermanager.bindsettings();
  });
  $( window ).resize(function() {
    umslidermanager.afterresize();
  });



})( jQuery );
