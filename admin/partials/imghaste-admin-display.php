<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.imghaste.com/
 * @since      1.0.0
 *
 * @package    Imghaste
 * @subpackage Imghaste/admin/partials
 */

function imghaste_options_page_cb() {
    // Check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'imghaste' ); ?>
			<div class="main-section">
				<?php do_settings_sections( 'imghaste' ); ?>
			</div>
			<?php 	
			//Check if PWA is enabled to show fiels
			$options = Imghaste_Helpers::imghaste_get_options();
			$show_pwa_fields = false;
			if (isset($options['imghaste_field_pwa'])) {
				if ($options['imghaste_field_pwa'] == 1) {
					$show_pwa_fields = true;
				}
			}?>
			<div class="pwa-section" <?php if($show_pwa_fields==false) { echo 'style="display:none"' ; } ?> >
				<?php do_settings_sections( 'imghaste_pwa' ); ?>
			</div>
			<div class="check-section">
				<?php do_settings_sections( 'imghaste_check' ); ?>
			</div>
			<?php submit_button( 'Save Settings' ); ?>
		</form>
		<br><br>
		<span><?php echo __('If you enjoy our 100% White Labeled Image Optimization Service, Leave a ', 'imghaste'); ?></span>
		<a style="display: inline flow-root; display: inline-block;" href="https://wordpress.org/plugins/imghaste/#reviews" target="_blank"><?php wp_star_rating( array( 'rating' => 5, 'type' => 'rating')); ?></a> <span><?php echo __('rating to endorse the efforts!', 'imghaste') ; ?></span>
	</div>
	<?php
}

/*
** Callback functions for setting fields
*/
function imghaste_section_main_cb( $args ) {
	?><p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings of imghaste Plugin. You need to add your CDN url to start using the service', 'imghaste' ); ?></p><?php
}

/*
** CDN URL field
*/
function imghaste_field_cdn_url_cb( $args ) {

	$options = Imghaste_Helpers::imghaste_get_options();

	$field_value = '';
	
	if (isset($options['imghaste_field_cdn_url'])){
		$field_value = $options['imghaste_field_cdn_url'];
	}
	if (isset($_POST['imghaste_field_cdn_url'])) {
		$field_value = esc_url($_POST('imghaste_field_cdn_url'));
	}


	?>

	<input style="width:350px;"
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="text"
	value="<?php echo $field_value; ?>"
	>

	<p class="description"><?php echo __('To get your own CDN URL, register', 'imghaste' ) . ' ' . '<a href="//app.imghaste.com/signup" target="_blank">' . __('here', 'imghaste') . '</a>' . '.'; ?></p>
	<?php
}

/*
** Rewrite Checkbox
*/
function imghaste_field_rewrite_cb( $args ) {

	$options = Imghaste_Helpers::imghaste_get_options();

	$current_checkbox = isset($options['imghaste_field_rewrite']) ? $options['imghaste_field_rewrite'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>


	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('Enabling will re-write your URLs. It will force a fast first-impression but you will leak SEO. We advice against. Read me here.: ', 'imghaste' ); ?>

		<a href="https://www.imghaste.com/blog/service-worker-as-your-image-optimization-service" target="_blank"><?php echo __('a Service Worker as your image Service', 'imghaste');?></a>
		<?php /* echo __('As well as: ', 'imghaste' ); ?>
		<a href="https://www.imghaste.com/blog/how-does-google-measure-your-site-speed" target="_blank"><?php echo __('a Service Worker as your image Service', 'imghaste');?></a>
		<?php */ ?>
	</p>
	<?php
}

/*
** Enable SlimCss Checkbox
*/
function imghaste_field_slimcss_cb( $args ) {

	$options = Imghaste_Helpers::imghaste_get_options();

	$current_checkbox = isset($options['imghaste_field_slimcss']) ? $options['imghaste_field_slimcss'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>

	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('SlimCSS (Open Beta) Will remove the unused CSS from your homepage.', 'imghaste' ); ?>
	</p>
	<?php
}


/*
** Purge SlimCss Checkbox
*/
function imghaste_field_purge_slimcss_cb($args){

	$options = Imghaste_Helpers::imghaste_get_options();

	//Get & initiate Purge Version
	$current_purgeversion = 1;
	if (isset($options['imghaste_field_slimcss_purgeversion'])) {
		$current_purgeversion = $options['imghaste_field_slimcss_purgeversion'];
	}
	?>

	<input type="button" name="slimcss_purge_button" id="slimcss_purge_button" class="button button-primary" value="<?php echo __('Purge SlimCSS', 'imghaste'); ?>">
	<input id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['imghaste_custom_data']); ?>" name="imghaste_options[<?php echo esc_attr($args['label_for']); ?>]" type="hidden" value="<?php echo $current_purgeversion; ?>">
	<p class="description">
		<?php echo __('Purge the SlimCSS cache. Each url will be reanalyzed and compiled', 'imghaste'); ?>
	</p>

	<?php
}

/*
** Enable PWA Checkbox
*/
function imghaste_field_pwa_cb( $args ) {

	$options = Imghaste_Helpers::imghaste_get_options();

	$current_checkbox = isset($options['imghaste_field_pwa']) ? $options['imghaste_field_pwa'] : '0';
	$checked_attribute = '';
	if ($current_checkbox == '1') {
		$checked_attribute .= 'checked';
	}
	?>

	<input
	id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['imghaste_custom_data'] ); ?>"
	name="imghaste_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	type="checkbox"
	value="1"
	<?php echo $checked_attribute; ?>
	>

	<p class="description">
		<?php echo __('Enable to create a PWA for you website', 'imghaste' ); ?>
	</p>
	<?php
}
