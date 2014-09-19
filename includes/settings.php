<?php

class NelioFPSettings {

	const DEFAULT_NUM_OF_WORDS_IN_EXCERPT  = 80;
	const DEFAULT_USE_FEAT_IMAGE_IF_AVAILABLE = true;
	const DEFAULT_USE_EXCERPT_IF_AVAILABLE = true;

	public static function get_settings() {
		return get_option( 'neliofp_settings', array()	);
	}

	public static function use_feat_image_if_available() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['use_feat_image'] ) )
			return $settings['use_feat_image'];
		return NelioFPSettings::DEFAULT_USE_FEAT_IMAGE_IF_AVAILABLE;
	}

	public static function use_excerpt_if_available() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['use_excerpt'] ) )
			return $settings['use_excerpt'];
		return NelioFPSettings::DEFAULT_USE_EXCERPT_IF_AVAILABLE;
	}

	public static function get_max_num_of_words_in_excerpt() {
		$settings = NelioFPSettings::get_settings();
		if ( isset( $settings['max_num_of_words_in_excerpt'] ) )
			return $settings['max_num_of_words_in_excerpt'];
		return NelioFPSettings::DEFAULT_NUM_OF_WORDS_IN_EXCERPT;
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
	public function sanitize( $input ) {
		$new_input = array();

		// FEATURED POSTS
		// ------------------------------------------------

		// ADVANCED
		// ------------------------------------------------

		$new_input['use_feat_image'] = 0;
		if( isset( $input['use_feat_image'] ) )
			$new_input['use_feat_image'] = 1;

		$new_input['use_excerpt'] = 0;
		if( isset( $input['use_excerpt'] ) )
			$new_input['use_excerpt'] = 1;

		$new_input['max_num_of_words_in_excerpt'] = NelioFPSettings::DEFAULT_NUM_OF_WORDS_IN_EXCERPT;
		if( isset( $input['max_num_of_words_in_excerpt'] ) )
			$new_input['max_num_of_words_in_excerpt'] = absint( $input['max_num_of_words_in_excerpt'] );
		$new_input['max_num_of_words_in_excerpt'] = max( 10, $input['max_num_of_words_in_excerpt'] );

		$new_input['list_of_feat_post_ids'] = array();
		if( isset( $input['list_of_feat_post_ids'] ) )
			$new_input['list_of_feat_post_ids'] = json_decode( urldecode(
				$input['list_of_feat_post_ids'] ) );
		$fps = array();
		foreach ( $new_input['list_of_feat_post_ids'] as $id ) {
			$p = get_post($id);
			if ( $p )
				array_push( $fps, $p );
		}
		$new_input['list_of_feat_posts'] = $fps;

		return $new_input;
	}

}

