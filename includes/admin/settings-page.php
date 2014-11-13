<?php

class NelioFPSettingsPage {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_posts_page(
			'Nelio Featured Posts',
			'Featured by Nelio',
			'manage_options',
			'neliofp-settings',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = NelioFPSettings::get_settings();
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Featured Posts by Nelio</h2>
			<br />
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'neliofp_settings_group' );
				do_settings_sections( 'neliofp-settings' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {

		require_once( NELIOFP_ADMIN_DIR . '/ajax.php' );
		add_action( 'wp_ajax_neliofp_search_posts',   'neliofp_search_posts' ) ;
		add_action( 'wp_ajax_neliofp_get_post_by_id', 'neliofp_get_post_by_id' ) ;

		if ( isset( $_GET['page'] ) && 'neliofp-settings' === $_GET['page'] ) {
			// Post Searcher
			wp_enqueue_style( 'neliofp_style_css',
				neliofp_asset_link( '/admin/style.min.css' ) );
			wp_enqueue_style( 'neliofp_select2_css',
				neliofp_asset_link( '/admin/lib/select2-3.5.0/select2.min.css' ) );
			wp_enqueue_script( 'neliofp_select2',
				neliofp_asset_link( '/admin/lib/select2-3.5.0/select2.min.js' ) );
			wp_enqueue_style( 'neliofp_post_searcher_css',
				neliofp_asset_link( '/admin/post-searcher.min.css' ) );
			wp_enqueue_script( 'neliofp_post_searcher',
				neliofp_asset_link( '/admin/post-searcher.min.js' ) );
		}

		register_setting(
			'neliofp_settings_group',
			'neliofp_settings',
			array( 'NelioFPSettings', 'sanitize' )
		);

		add_settings_section(
			'feat_posts_section',
		// ================================================================
			'Featured Posts',
		// ================================================================
			array( $this, 'print_feat_post_section' ),
			'neliofp-settings'
		);

		add_settings_section(
			'advanced_section',
		// ================================================================
			'Advanced Settings',
		// ================================================================
			array( $this, 'print_section_info' ),
			'neliofp-settings'
		);

		add_settings_field(
			'use_feat_image',
			'Print Featured Image',
		// ----------------------------------------------------------------
			array( $this, 'use_feat_image_callback' ),
			'neliofp-settings',
			'advanced_section'
		);

	}

	public function print_feat_post_section() {
		$fn = 'feat_posts';
		neliofp_the_post_searcher( 'neliofp-searcher' ); ?>
		<a id="neliofp-add-first" class="button button-primary"><?php _e( 'Add First' ); ?></a>
		<a id="neliofp-add-last" class="button"><?php _e( 'Add Last' ); ?></a>

		<br><br>
		<h4><?php _e( 'These are your featured posts:', 'neliofp' ); ?></h4>
		<div id="neliofp-list-of-feat-posts">
		</div>

		<?php
		$fn = 'list_of_feat_post_ids';
		printf(
			'<input type="hidden" id="%1$s" name="neliofp_settings[%1$s]" value="%2$s" />',
			$fn, urlencode( json_encode( NelioFPSettings::get_list_of_feat_post_ids() ) )
		); ?>

		<script type="text/javascript">
		var xxx;
		(function($) {
			function addFeatPost( id, position ) {
				var node = '<div class="result-content"></div>';
				node = $(node);
				if ( 'first' == position )
					$("#neliofp-list-of-feat-posts").prepend(node);
				else
					$("#neliofp-list-of-feat-posts").append(node);
				jQuery.ajax( {
					type:     'POST',
					async:    true,
					url:      ajaxurl,
					dataType: "json",
					data: {
						action: 'neliofp_get_post_by_id',
						id:     id,
					},
					success: function( data ) {
						if (data.id <= 0 )
							return;
						var content = '<div class="result-image">';
						content += data.thumbnail;
						content += '</div><div class="result-item">';
						content += '<div class="result-title"><span class="select2-match">';
						content += '<a href="' + data.link + '" target="_blank">';
						content += data.title;
						content += '</a>';
						content += '</div><div class="row-actions"><span class="delete" data-post_id="';
						content += data.id;
						content += '"><a href="#"><?php echo str_replace( '\'', '\\\'', __( 'Delete' ) ); ?></a>';
						content += '</span>';
						node.append(content);
						node.find('.delete').click(function() {
							node.remove();
						});
					},
				});
			}

			var aux = JSON.parse( decodeURIComponent(
				$("#list_of_feat_post_ids").attr( 'value' )
			) );
			for ( var i = 0; i < aux.length; ++i )
				addFeatPost(aux[i]);

			$("#neliofp-add-first").click( function() {
				var id = jQuery("#neliofp-searcher").attr('value');
				if ( id > 0 ) {
					try {
						addFeatPost(parseInt(id), 'first');
					} catch (e) {}
				}
			});

			$("#neliofp-add-last").click( function() {
				var id = jQuery("#neliofp-searcher").attr('value');
				if ( id > 0 ) {
					try {
						addFeatPost(parseInt(id), 'last');
					} catch (e) {}
				}
			});

			$(document).ready(function() {
				$("input[type=submit]").click(function() {
					NelioFPList = [];
					$("#neliofp-list-of-feat-posts .delete").each(function() {
						NelioFPList.push( parseInt( $(this).data('post_id') ) );
					});
					$("#list_of_feat_post_ids").attr( 'value',
						encodeURIComponent( JSON.stringify( NelioFPList ) )
						.replace("'","%27") );
				});
			});
		})(jQuery);
		</script>
		<?php
	}

	public function print_section_info() {
	}

	public function use_feat_image_callback() {
		$fn = 'use_feat_image'; ?>
		<input type="checkbox" id="<?php echo $fn; ?>" name="neliofp_settings[<?php echo $fn; ?>]"
			<?php checked( NelioFPSettings::use_feat_image_if_available() ); ?> /><?php
	}

}

if ( is_admin() )
	$my_settings_page = new NelioFPSettingsPage();

