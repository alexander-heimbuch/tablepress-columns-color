<?php
/**
 * WordPress plugin "TablePress Column Colors" main file, responsible for initiating the plugin
 *
 * @package TablePress Plugins
 * @author Alexander Heimbuch
 * @version 0.1
 */

/*
Plugin Name: TablePress Extension: Column Colors
Plugin URI: http://aktivstoff.de/
Description: Extend TablePress tables with the ability to highlight multiple columns
Version: 0.1
Author: Alexander Heimbuch
Author URI: http://aktivstoff.de
Author email: kontakt@aktivstoff.de
Text Domain: tablepress
Domain Path: /i18n
License: GPL 2
*/

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

add_action( 'tablepress_run', array( 'TablePress_Columns_Color', 'init' ) );

class TablePress_Columns_Color {

    protected static $slug = 'tablepress-columns-color';
    protected static $version = '0.1';

    public static function init() {
        add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'shortcode_table_default_shortcode_atts' ) );
        add_filter( 'tablepress_table_render_options', array( __CLASS__, 'table_render_options' ), 10, 2 );
        add_filter( 'tablepress_table_js_options', array( __CLASS__, 'table_js_options' ), 10, 3 );
        add_filter( 'tablepress_table_output', array( __CLASS__, 'table_output' ), 10, 3 );
    }

    public static function shortcode_table_default_shortcode_atts( $default_atts ) {
        $default_atts['columns-color'] = '';

        return $default_atts;
    }

    public static function table_render_options( $render_options, $table ) {
        if ( strlen( $render_options['columns-color'] ) == 0 ) {
            $render_options['columns-color'] = null;
        } else {
            $colors = array();
            $render_options['columns-color'] = split( ',', $render_options['columns-color'] );

            foreach( $render_options['columns-color'] as $columnColor ) {
                $columnColor = split( ':', $columnColor );
                $colors[intval( $columnColor[0] )] = trim( $columnColor[1] );
            }

            $render_options['columns-color'] = $colors;
            $render_options['use_datatables'] = true;
        }

        return $render_options;
    }

    public static function table_js_options( $js_options, $table_id, $render_options ) {
        if( !$render_options['columns-color'] ) {
            return $js_options;
        }

        wp_enqueue_script( self::$slug, plugins_url( 'tablepress-columns-color.js', __FILE__ ), array( 'tablepress-datatables' ), self::$version, true );

        return $js_options;
    }

    public static function table_output( $output, $table, $render_options ) {
        if( !$render_options['columns-color'] ) {
            return $output;
        }

        return $output . '<script>
            if (window.TABLE_COLORS === undefined) {
                window.TABLE_COLORS = {};
            }

            window.TABLE_COLORS["' . $render_options['html_id'] . '"] = JSON.parse(\'' . json_encode( $render_options['columns-color'] ) . '\');
        </script>';
    }
}
?>
