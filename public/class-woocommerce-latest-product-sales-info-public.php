<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		add_action( 'woocommerce_single_product_summary', array( $this, 'time_remaining_since_last_sell' ), 10 );
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-latest-product-sales-info-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-latest-product-sales-info-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Convert seconds to date time format.
	 *
	 * @since    1.0.0
	 */ 
	private function secondsToTime( $seconds ) {
	    $dtF = new DateTime("@0");
	    $dtT = new DateTime("@$seconds");
	
	    if ( $seconds < 86400 ) {
	
	        $format = '%h horas y %i minutos';
	    }elseif( ($seconds >= 86400) && ($seconds < 604800 ) ){
	
	        $format = '%a días y %h horas';
	    }
	    return $dtF->diff($dtT)->format( $format );
	}
	
	/**
	 * Search order by product in last seven days
	 *
	 * @since    1.0.0
	 */
	public function search_order_by_product_in_last_week( $product_id ) {
	    $orders = get_posts( array('post_type' => 'shop_order') );
	    $time_remaining = '';
	
	    foreach ($orders as $order) {
	        $order_id = $order->ID;
	        $order = new WC_Order($order_id);
	
	        $now = time();
	        $diferencia = $now - strtotime( $order->order_date );
	
	        if( ( $diferencia/86400 ) <= 7 ){
	
	            $items = $order->get_items();
	
	            foreach( $items as $item ) {
	
	                if ( $item['product_id'] == $product_id ) {
	
	                    return $this->secondsToTime( $diferencia );
	                }
	            }
	        }
	    }
	
	    return $time_remaining;
	}
	
	/**
	 * Display the remaining time since last sell of product.
	 *
	 * @since    1.0.0
	 */
	public function time_remaining_since_last_sell(){
	    global $post;
	
	    $time_remaining = $this->search_order_by_product_in_last_week( $post->ID );
	
	    if ( !empty( $time_remaining ) ) {

	        ob_start();
        	include( dirname(__FILE__) .'/partials/woocommerce-latest-product-sales-info-public-display.php' );
        	$payment_history_table = ob_get_clean();
			echo $payment_history_table;
	    }
	}
	
}
	