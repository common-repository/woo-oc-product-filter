<?php
/**
*Plugin Name: woo-oc-product-filter
*Description: This plugin allows create product table.
* Version: 1.0
* Author: Ocean Infotech
* Author URI: https://www.xeeshop.com
* Copyright: 2019 
*/

if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('OCPF_PLUGIN_NAME')) {
  define('OCPF_PLUGIN_NAME', 'woo-oc-product-filter');
}
if (!defined('OCPF_PLUGIN_VERSION')) {
  define('OCPF_PLUGIN_VERSION', '1.0.0');
}
if (!defined('OCPF_PLUGIN_FILE')) {
  define('OCPF_PLUGIN_FILE', __FILE__);
}
if (!defined('OCPF_PLUGIN_DIR')) {
  define('OCPF_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('OCPF_DOMAIN')) {
  define('OCPF_DOMAIN', 'ocpf');
}



if (!class_exists('OCPFMAIN')) {

  class OCPFMAIN {

    protected static $instance;

    //Load all includes files
    function includes() {
      include_once('admin/ocpf-backend.php');
      include_once('admin/ocpf-shortcode.php');

    }

    function init() {
      add_action('admin_enqueue_scripts', array($this, 'ocpf_load_admin_script_style'));
      add_action('wp_enqueue_scripts',  array($this, 'ocpf_load_script_style'));
    }

	

    //Add JS and CSS on Frontend
    function ocpf_load_script_style()
    {
      wp_enqueue_style( 'OCPF_front_datatable_css', OCPF_PLUGIN_DIR . '/includes/css/jquery.dataTables.css' );
      wp_enqueue_script( 'OCPF_front_datatable_js', OCPF_PLUGIN_DIR . '/includes/js/jquery.dataTables.js' );
      wp_enqueue_style( 'OCPF_front_css', OCPF_PLUGIN_DIR . '/includes/css/ocpf_front_style.css', false, '1.0.0' );
      wp_enqueue_script( 'OCPF_product_datatables', OCPF_PLUGIN_DIR . '/includes/js/ocpf_front.js', array(), '1.0', true );
      wp_localize_script( 'OCPF_product_datatables', 'ajax_url', admin_url('admin-ajax.php?action=product_datatables') );
      $translation_array = OCPF_PLUGIN_DIR;
      wp_localize_script( 'OCPF_product_datatables', 'object_name', $translation_array );
      wp_enqueue_style( 'OCPF_front_rowreorder_css', OCPF_PLUGIN_DIR . '/includes/css/rowReorder.dataTables.min.css' );
      wp_enqueue_script( 'OCPF_front_rowreorder_js', OCPF_PLUGIN_DIR . '/includes/js/dataTables.rowReorder.min.js');
      wp_enqueue_style( 'OCPF_front_resposive_css', OCPF_PLUGIN_DIR . '/includes/css/responsive.dataTables.min.css' );
      wp_enqueue_script( 'OCPF_front_resposive_js', OCPF_PLUGIN_DIR . '/includes/js/dataTables.responsive.min.js');
    }

    //Add JS and CSS on Backend
    function ocpf_load_admin_script_style() {
      wp_enqueue_style( 'OCPF_admin_css', OCPF_PLUGIN_DIR . '/includes/css/ocpf_admin_style.css', false, '1.0.0' );
      wp_enqueue_script( 'OCPF_admin_js', OCPF_PLUGIN_DIR . '/includes/js/ocpf_admin.js', false, '1.0.0' );
    }

    //Plugin Rating
    public static function do_activation() {
      set_transient('occp-first-rating', true, MONTH_IN_SECONDS);
    }

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
        self::$instance->includes();
      }
      return self::$instance;
    }

  }

  add_action('plugins_loaded', array('OCPFMAIN', 'instance'));

  register_activation_hook(OCPF_PLUGIN_FILE, array('OCPFMAIN', 'do_activation'));
}

