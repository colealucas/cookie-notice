jQuery(document).ready(function($) {
	"use strict";

	var accept_cookie_name = "icomply-cookie-accepted",
	    notifyTimeout;

	function setCookie(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}

	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)===' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}

	function deleteCookie(cookieName){
		if( cookieName.length && getCookie(cookieName) ) eraseCookie(cookieName);
	}

    $(document).on('click touch', '.wbl-media-upload-button', function(e) {
        e.preventDefault();
		
		var preview_img = $(this).parents(".wbl-media-upload").find(".wbl-media-preview");
		var preview_url = $(this).parents(".wbl-media-upload").find(".wbl-media-url");

		var custom_uploader = wp.media({
			title: 'Custom Media',
			button: {
				text: 'Upload Media'
			},
			multiple: false
		})
		.on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			
			if( preview_img.length ) preview_img.attr('src', attachment.url);
			if( preview_url.length ) preview_url.val(attachment.url);

		}).open();
	});
	
	$(document).on( 'click touch', '.wbl-clear-media', function(e) {
        e.preventDefault();
		
		var parent = $(this).parents(".wbl-media-upload");
		var preview_img = parent.find(".wbl-media-preview");
		var preview_url = parent.find(".wbl-media-url");
	
		if( preview_img.length ) preview_img.attr('src', '');
		if( preview_url.length ) preview_url.val(' ');
	});

	$(document).on('click touch', '.wbl-delete-all-cookie', function(e) {
		e.preventDefault();
		deleteCookie(accept_cookie_name);

		$(this).attr('disabled', 'disabled');
		$(this).addClass('wbl-disabled');
		$(this).parent().append("<div class='wbl-notify-inline wbl-notify-success'>Cookies Deleted!</div>");
	});

});
