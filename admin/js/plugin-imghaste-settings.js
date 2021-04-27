document.addEventListener("DOMContentLoaded", function (event) {

	//Check Service Worker
	var checkIfLocalhost = document.getElementById('imghaste_localhost_check').value;
	var serviceWorkerStatus = document.getElementById("service-worker-test");
	if (checkIfLocalhost == "true") {
		//Do nothing
	} else {
		var service_worker_url = window.location.origin + "/image-service.ih.js"
		fetch(service_worker_url, {redirect: 'manual', cache: "no-store"})
			.then(function (response) {
				let contentType = response.headers.get("content-type");
				if (contentType && contentType.indexOf("/javascript") !== -1 && response.status == 200) {
					setTimeout(function () {
						serviceWorkerStatus.style.color = 'green'
						serviceWorkerStatus.innerHTML = '<span class="dashicons dashicons-yes"></span> The Service Worker is active'
					}, 1000)
				} else {
					setTimeout(function () {
						serviceWorkerStatus.style.color = 'red'
						serviceWorkerStatus.innerHTML = '<span class="dashicons dashicons-no"></span> The Service Worker is not active. Please Consult Support for False Negatives'
					}, 1000)

				}
			})
	}

	//Purge SlimCSS
	var purge_button = document.querySelector('#slimcss_purge_button');
	if (purge_button) {
		purge_button.addEventListener('click', function (e) {
			var slimPurgeVersionInput = document.getElementById('imghaste_field_slimcss_purgeversion');
			var UpdateSlimPurgeVersion = (parseInt(slimPurgeVersionInput.value) || 0) + 1;
			slimPurgeVersionInput.setAttribute('value', UpdateSlimPurgeVersion.toString());
			document.getElementById("submit").click();
		});
	}

});
