<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;
Use \HPWooRewardsIncludes\Helper;

/**
 * Product class
 */
class Product {

	public $points;

	/**
	 * Construct
	 *
	 * A place to add hooks and filters
	 *
	 * @since 1.0
	 *
	 */
	public function __construct() {
		$point_class = Helper::get_class_name('\HPWooRewardsIncludes\Points');
    	$this->points = new $point_class();

		if ($this->points->is_point_system_enabled() == true) {
			//quick fix for theme Hotel Queen as it doesn't call woocommerce_single_product_summary
			$theme = wp_get_theme();
		
			if ( stripos($theme->name, 'Hotel Queen') !== false ) {
				add_action( 'hotel_booking_single_price', array($this,'show_points'), 15 );
			} 
			else {
				add_action( 'woocommerce_single_product_summary', array($this,'show_points'), 15 );
			}
		}
	}

	/**
	 * Get product points, either it's simple, variable, or grouped product
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	private function get_product_point() {
		global $product;
		$hp_woo_points = '';

		if( $product->is_type('variable')) {
			//variable

			$min_price = $product->get_variation_price('min');
			$max_price = $product->get_variation_price('max');

			$min_arg = array(
	            'qty'   => '1',
	            'price' => $min_price
	        );

	        $max_arg = array(
	            'qty'   => '1',
	            'price' => $max_price
	        );

			$min_points = $this->get_product_total_points($product, $min_arg);
			$max_points = $this->get_product_total_points($product, $max_arg);
			$hp_woo_points = ($min_points == $max_points)? $min_points : $min_points . '-' . $max_points; //thanks kim :)
		}
		elseif ($product->is_type('grouped')) {
			//grouped
			foreach ( $product->get_children() as $child_id ) {
			    $all_prices[] = get_post_meta( $child_id, '_price', true );
			}

			if (! empty( $all_prices )) {
			    $max_price = max( $all_prices );
			    $min_price = min( $all_prices );

			    $min_arg = array(
		            'qty'   => '1',
		            'price' => $min_price
		        );

		        $max_arg = array(
		            'qty'   => '1',
		            'price' => $max_price
		        );        

			    $min_points = $this->get_product_total_points($product, $min_arg);
				$max_points = $this->get_product_total_points($product, $max_arg);
				$hp_woo_points = ($min_points == $max_points)? $min_points : $min_points . '-' . $max_points; //thanks kim :)				
			} 
		}

		if (empty($hp_woo_points)) {
			$hp_woo_points = $this->get_product_total_points($product);
		}

		return $hp_woo_points;
	}

	/**
	 * Helper function, get product total
	 *
	 * @since 1.1.1
	 * @return void
	 *
	 */
	protected function get_product_total_points($product, $args = array()) {
		return $this->points->get_total_points(wc_get_price_excluding_tax($product, $args));
	}

	/**
	 * Display existing points, points can earn on product page
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function show_points() {

		$message = '';

		$hp_woo_points = $this->get_product_point();
		$default_message = Helper::get_settings_option('single_product_message');
		$values = array(
	      'points_label'  => $this->points->point_name,
	      'points'        => $hp_woo_points
	    );

		$message .= Helper::parse_customized_message($default_message, $values).'<br>';

		$customer_id = get_current_user_id();
		$existing_points = $this->points->get_customer_total_points($customer_id);
		if ($customer_id > 0 && $existing_points > 0) {

			$default_message = Helper::get_settings_option('total_points_message');
			$values = array(
		      'points_label'  => $this->points->point_name,
		      'points'        => $existing_points
		    );
			$message .= Helper::parse_customized_message($default_message, $values);
		}

		if (strlen($message) > 0) {			
			echo "<span>";
			echo $message;
			echo "</span>";
		}
	}//function
}
