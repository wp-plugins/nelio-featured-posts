<?php

// Creating the widget
class NelioFP_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'NelioFP_Widget',

			// Widget name will appear in UI
			__('Featured Posts by Nelio', 'neliofp'),

			// Widget description
			array( 'description' => __( 'Display a list of your featured posts.', 'neliofp' ), )
		);

		if ( !is_admin() ) {
			wp_enqueue_style( 'neliofp_style_css',
				neliofp_asset_link( '/style.min.css' ) );
		}
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		$fps = NelioFPSettings::get_list_of_feat_posts();
		if ( count( $fps ) > 0 ) {
			require_once( NELIOFP_DIR . '/featured-post-template.php' );
			echo '<nav>';
			foreach ( $fps as $fp ) {
				echo NelioFPFeaturedPostTemplate::render( $fp );
			}
			echo '</nav>';
		}
		else {
			echo '<p class="neliofp-none">' . __( 'No featured posts.' ) . '</p>';
		}
		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance['title'] ) )
			$title = $instance['title'];
		else
			$title = __( 'Featured Posts', 'neliofp' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input
				class="widefat" type="text"
				id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}

// Register and load the widget
add_action( 'widgets_init', 'neliofp_load_widget' );
function neliofp_load_widget() {
	register_widget( 'NelioFP_Widget' );
}
