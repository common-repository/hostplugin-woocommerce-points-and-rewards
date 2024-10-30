<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Hostplugin - Woocommerce Rewards & Points Settings', 'hostplugin-woocommerce-points-reward' ); ?></h1>
	<?php settings_errors('hp_woo_rewards_points_settings_error'); ?>

	<form method="post" action="options.php">
		<?php 

			settings_fields("hp_woo_rewards_points_settings_option");
            // all the add_settings_field callbacks is displayed here
            do_settings_sections("hp_woo_rewards_points_settings");
            // Add the submit button to serialize the options
            submit_button(__('Save Changes', 'hostplugin-woocommerce-points-reward'), 'primary', 'submit', false);  
            echo "&nbsp;";
            submit_button(__('Reset to Defaults', 'hostplugin-woocommerce-points-reward'), 'secondary', 'hp_woo_reset_settings', false);         
		?>
	</form>
</div>

<?php
	
	function hp_woo_rewards_points_section_callback() { 
		//
	}

	function hp_woo_premium_feature_only($args) {
		if (isset($args['premium']) && $args['premium'] == true && HOSTPLUGIN_WOO_POINTS_LICENSE != 'Premium') {
			
			echo '<span class="premium"> <a href="'. admin_url( 'admin.php?page=hp_woo_rewards_points_dashboard&tab=premium').'">('. __('Premium Feature', 'hostplugin-woocommerce-points-reward') .')</a></span>';		
		}
	}

	function hp_woo_rewards_points_checkbox($args) { 
		$checked = $args['value'] == 'on' ? 'checked': '';
	    echo '<input type="checkbox" name="hp_woo_rewards_points_settings_option['. $args['label_for'].']" '. $checked .'></input>';
	    hp_woo_premium_feature_only($args);
	}		

	function hp_woo_rewards_points_number_input($args) { 
		if (isset($args['money']) && $args['money'] == true)
			echo get_woocommerce_currency_symbol();
	    echo '<input type="number" class="short" min="1" step="1" name="hp_woo_rewards_points_settings_option['. $args['label_for'].']" value="' . esc_attr($args['value']) . '"></input>';
	    hp_woo_premium_feature_only($args);
	}		

	function hp_woo_rewards_points_text_input($args) { 
	    echo '<input type="text" class="medium" required name="hp_woo_rewards_points_settings_option['. $args['label_for'].']" value="' . esc_attr($args['value']) . '"></input>';
	    hp_woo_premium_feature_only($args);
	}	

	function hp_woo_rewards_points_textarea($args) { 
	    echo '<textarea rows="3" cols="60" name="hp_woo_rewards_points_settings_option['. $args['label_for'].']" id="hp_woo_rewards_'.$args['label_for'].'">'.esc_attr($args['value']).'</textarea>';
	    hp_woo_premium_feature_only($args);
	}		

	function hp_woo_rewards_points_field_points_conversion($args) { 
		echo esc_html_e('Spend ', 'hostplugin-woocommerce-points-reward');
		echo ' '.get_woocommerce_currency_symbol().' ';

		$dollar_spent = array('label_for' => 'dollar_spent', 'value' => $args['dollar_spent']);
		hp_woo_rewards_points_number_input($dollar_spent);

	    echo esc_html_e( ' and earn ', 'hostplugin-woocommerce-points-reward' );
	    $points_earned = array('label_for' => 'points_earned', 'value' => $args['points_earned']);
	    hp_woo_rewards_points_number_input($points_earned);
	    echo esc_html_e( ' Point(s) ', 'hostplugin-woocommerce-points-reward' ); 
	}
	
	function hp_woo_rewards_points_field_redeem_points($args) {
		$redeem_points = array('label_for' => 'redeem_points', 'value' => $args['redeem_points']);
		hp_woo_rewards_points_number_input($redeem_points);
		echo esc_html_e( ' Point(s) ', 'hostplugin-woocommerce-points-reward' ); 
		echo ' = '.get_woocommerce_currency_symbol().' ';
		$redeem_dollar = array('label_for' => 'redeem_dollar', 'value' => $args['redeem_dollar']);
		hp_woo_rewards_points_number_input($redeem_dollar);
	}

	function hp_woo_rewards_points_field_max_points_discount($args) { 
		$max_points_discount = array('label_for' => 'max_points_discount', 'value' => $args['max_points_discount']);
		hp_woo_rewards_points_number_input($max_points_discount);
		$max_points_discount_type = array('label_for' => 'max_points_discount_type', 'value' => $args['max_points_discount_type'], 'options' => array('currency' => get_woocommerce_currency_symbol(), 'percentage' => __('percentage', 'hostplugin-woocommerce-points-reward')), 'class'=> 'medium');
		hp_woo_rewards_points_select($max_points_discount_type);
		hp_woo_premium_feature_only($args);
	}

	function hp_woo_rewards_points_select($args) {
		$class = (isset($args['class']) && !empty($args['class'])) ? ' class="'.$args['class'].'" ': '';
		$multiple= (isset($args['multiple']) && $args['multiple'] == true) ? ' multiple="multiple" ' : '';
		$id = (isset($args['id']) && !empty($args['id'])) ? ' id="'.$args['id'].'" ' : '';

		$multiple_fields = (isset($args['multiple']) && $args['multiple'] == true) ? '[]' : '';
		echo '<select '.$id.$class.$multiple.' name="hp_woo_rewards_points_settings_option['. $args['label_for'].']'.$multiple_fields.'">';

		foreach ($args['options'] as $key => $value) {
			if (is_array($args['value'])) {
				$selected = (in_array($key, $args['value']) ? 'selected ' : '');
			}
			else {
				$selected = ($key == $args['value'] ? 'selected ' : '');
			}

			
			echo '<option value="'. $key .'" '. $selected .'>'. esc_attr( $value, 'hostplugin-woocommerce-points-reward' ) .'</option>';
		}

		echo '</select>';
		hp_woo_premium_feature_only($args);
	}

?>