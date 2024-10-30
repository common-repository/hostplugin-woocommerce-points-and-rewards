<?php

/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

Use \HPWooRewardsIncludes\Helper;

class Activation {

	/**
	 *
	 * call when activate is clicked. this method is used to add defaults to database
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public static function activate() {
		$defaults_settings = Helper::get_default_settings();

		if ( ! get_option( 'hp_woo_rewards_points_settings_option' ) ) {
			update_option( 'hp_woo_rewards_points_settings_option', $defaults_settings );
		}

		update_option( 'hp_woo_rewards_points_version', HOSTPLUGIN_WOO_POINTS_VERSION );
	}
}