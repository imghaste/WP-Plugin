
jQuery(document).ready(function($){

	/* 
	* PWA Fields
	*/ 

	//Color Picker
 	$('.imghaste-colorpicker').wpColorPicker();	// Color picker
 	//Icon Picker
	$('.imghaste-pwa-icon-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var imghaste_media_uploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = imghaste_media_uploader.state().get('selection').first().toJSON();
			$('.imghaste-icon').val(attachment.url);
		})
		.open();
	})
	//Spash Icon Picker
	$('.imghaste-pwa-splash-icon-upload').click(function(e) {	// Splash Screen Icon upload
		e.preventDefault();
		var imghaste_media_uploader = wp.media({
			title: 'Splash Screen Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = imghaste_media_uploader.state().get('selection').first().toJSON();
			$('.imghaste-splash-icon').val(attachment.url);
		})
		.open();
	});

	/* 
	* Check PWA Manifest
	*/ 

	var manifestUrl = document.getElementById("manifest-url").value;
	if (manifestUrl) {
		//Get Manifest Test
		var manifestStatus = document.getElementById("manifest-generated-test");
		//Fetch for manifest url
		fetch(manifestUrl, {redirect: 'manual', cache: "no-store"})
		.then(function (response) {
			let contentType = response.headers.get("content-type");
			if (contentType && contentType.indexOf("/json") !== -1 && response.status == 200) {
				setTimeout(function () {
					manifestStatus.style.color = 'green';
					manifestStatus.innerHTML = '<span class="dashicons dashicons-yes"></span> Manifest generated successfully. You can see it <a href="'+manifestUrl+'" target="_blank">here</a>';
				}, 1000)
			} else {
				setTimeout(function () {
					manifestStatus.style.color = 'red';
					manifestStatus.innerHTML = '<span class="dashicons dashicons-no"></span> Manifest generation failed. Have you filled the PWA fields?'
				}, 1000)

			}
		})
	}


});