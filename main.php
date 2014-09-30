<?php
/**
 * Copyright 2013 Nelio Software S.L.
 * This script is distributed under the terms of the GNU General Public
 * License.
 *
 * This script is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License. This script is
 * distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */


/*
 * Plugin Name: Nelio Featured Posts
 * Description: Select the featured posts you want to show at any time and include them in your theme using a widget.
 * Version: 1.1.2
 * Author: Nelio Software
 * Plugin URI: http://neliosoftware.com
 * Text Domain: neliofp
 */

// ==========================================================================
// PLUGIN INFORMATION
// ==========================================================================
	define( 'NELIOFP_PLUGIN_VERSION', '1.1.2' );
	define( 'NELIOFP_PLUGIN_NAME', 'Nelio Featured Posts' );
	define( 'NELIOFP_PLUGIN_DIR_NAME', basename( dirname( __FILE__ ) ) );

// Defining a few important directories
	define( 'NELIOFP_ROOT_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
	define( 'NELIOFP_DIR', NELIOFP_ROOT_DIR . '/includes' );
	define( 'NELIOFP_ADMIN_DIR', NELIOFP_DIR . '/admin' );

// Some URLs...
	define( 'NELIOFP_ASSETS_URL', plugins_url() . '/' . NELIOFP_PLUGIN_DIR_NAME . '/assets' );


// ==========================================================================
// INCLUDING CODE
// ==========================================================================
	require_once( NELIOFP_DIR . '/settings.php' );

// ADMIN STUFF
	if ( is_admin() ) {
		require_once( NELIOFP_ADMIN_DIR . '/settings-page.php' );
	}

// REGULAR STUFF
	require_once( NELIOFP_DIR . '/utils.php' );
	require_once( NELIOFP_DIR . '/widget.php' );

