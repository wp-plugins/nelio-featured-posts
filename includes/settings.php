<?php

abstract class NelioFPSettings {

	const DEFAULT_USE_FEAT_IMAGE_IF_AVAILABLE = true;

	public static function get_settings() {
		return get_option( 'neliofp_settings', array()	);
	}

	public static function use_feat_image_if_available() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['use_feat_image'] ) )
			return $settings['use_feat_image'];
		return NelioFPSettings::DEFAULT_USE_FEAT_IMAGE_IF_AVAILABLE;
	}

	public static function get_list_of_feat_post_ids() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['list_of_feat_post_ids'] ) )
			return $settings['list_of_feat_post_ids'];
		return array();
	}

	public static function get_list_of_feat_posts() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['list_of_feat_posts'] ) )
			return $settings['list_of_feat_posts'];
		return array();
	}





	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public static function sanitize( $input ) {
		$new_input = array();

		$fn = 'use_feat_image';
		$new_input[$fn] = false;
		if( isset( $input[$fn] ) && ( 'on' === $input[$fn] || true === $input[$fn] ) )
			$new_input[$fn] = true;

		// Save the IDs
		$fn = 'list_of_feat_post_ids';
		$new_input[$fn] = array();
		if( isset( $input[$fn] ) ) {
			if ( is_array( $input[$fn] ) )
				$new_input[$fn] = $input[$fn];
			elseif ( is_string( $input[$fn] ) )
				$new_input[$fn] = json_decode( urldecode( $input[$fn] ) );
		}

		// Save the posts
		$fps = array();
		foreach ( $new_input['list_of_feat_post_ids'] as $id ) {
			$p = get_post( $id );
			if ( $p )
				array_push( $fps, $p );
		}
		$new_input['list_of_feat_posts'] = $fps;

		return $new_input;
	}

}

