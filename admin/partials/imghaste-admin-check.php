<?php

/*
** Callback for status check section.
*/
function imghaste_section_status_check_cb(){

	$options = Imghaste_Helpers::imghaste_get_options();

	$correct_style = 'color: green;';
	$error_style = 'color: red';
	$correct_icon = '<span class="dashicons dashicons-yes"></span> ';
	$error_icon = '<span class="dashicons dashicons-no"></span> ';

	
	/* 
	* Check for Manifest
	*/ 

	//Check if PWA is enabled
	if (isset($options['imghaste_field_pwa'])) :
		if ($options['imghaste_field_pwa'] == 1) :  ?>

			<h4><?php _e('PWA Check','imghaste'); ?></h4>

			<input id="manifest-url" type="hidden" value="<?php echo Imghaste_Helpers::imghaste_httpsify(Imghaste_PWA::imghaste_manifest( 'src' )) ; ?>">
			<table class="form-table" role="presentation">
				<tbody>
					<tr class="imghaste_row">
						<th scope="row"><label><?php _e('Check: Manifest','imghaste'); ?></label></th>
						<td id="manifest-generated-test"><?php echo __('Checking the Manifest File...', 'imghaste'); ?></td>
					</tr>
				</tbody>
			</table>
		<?php endif; 
	endif; 


	/* 
	* Check for CDN
	*/ 
	?>
	<h4><?php _e('Image optimazation Check','imghaste'); ?></h4>
	<?php 
    //Check if localhost
	if (Imghaste_Helpers::imghaste_is_localhost()): ?>


		<input id="imghaste_localhost_check" type="hidden" value="true" name="imghaste_localhost_check">
		<p style="<?php echo $error_style ; ?>" ><?php _e('Service Worker can not be effective on localhost','imghaste'); ?></p>

	<?php else:

		//HTTPS check
		$correct_https_message = $correct_icon . __('Your website is running safely on HTTPS', 'imghaste');
		$error_https_message = $error_icon . __('Your website is not running on HTTPS the Service Worker can not be registered, unfortunately you can only use this service using Rewrite URLS', 'imghaste');
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
			$https_message = $error_https_message;
			$https_style = $error_style;
		} else {
			$https_message = $correct_https_message;
			$https_style = $correct_style;
		}

		//Service Worker Test Runs on JS

		//Origin Server Test
		$OriginTestReq = array(
			"cdn_url" => $options['imghaste_field_cdn_url'],
			"origin" => get_site_url(),
		);
		$OriginTestConnection = curl_init('https://cdn.imghaste.com/v1/check/origin');
		curl_setopt($OriginTestConnection, CURLOPT_POSTFIELDS, $OriginTestReq);
		curl_setopt($OriginTestConnection, CURLOPT_RETURNTRANSFER, true);
		$OriginTestResJson = curl_exec($OriginTestConnection);
		curl_close($OriginTestConnection);
		$OriginTestRes = json_decode($OriginTestResJson);
		if ($OriginTestRes->status == 'REQUEST_OK') {
			$origin_message = $correct_icon . $OriginTestRes->notification;
			$origin_style = $correct_style;
		} else {
			$origin_message = $error_icon . $OriginTestRes->notification;
			$origin_style = $error_style;
		}

		?>
		<input id="imghaste_localhost_check" type="hidden" value="false" name="imghaste_localhost_check">
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Https','imghaste'); ?></label></th>
					<td id="running-on-https-test" style="<?php echo $https_style; ?>"><?php echo $https_message; ?></td>
				</tr>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Origin Server','imghaste'); ?></label></th>
					<td id="origin-server-test" style="<?php echo $origin_style; ?>"><?php echo $origin_message ; ?></td>
				</tr>
				<tr class="imghaste_row">
					<th scope="row"><label><?php _e('Check: Service Worker','imghaste'); ?></label></th>
					<td id="service-worker-test"><?php echo __('Checking the Service Worker status...', 'imghaste'); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	endif;
}