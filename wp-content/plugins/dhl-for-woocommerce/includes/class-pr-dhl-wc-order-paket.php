<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce DHL Shipping Order.
 *
 * @package  PR_DHL_WC_Order
 * @category Shipping
 * @author   Shadi Manna
 */

if ( ! class_exists( 'PR_DHL_WC_Order_Paket' ) ) :

class PR_DHL_WC_Order_Paket extends PR_DHL_WC_Order {

	protected $carrier = 'DHL Paket';

	const DHL_PICKUP_PRODUCT = '08';

	public function init_hooks(){

		parent::init_hooks();

		// add 'Label Created' orders page column header
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_label_column_header' ), 30 );

		// add 'Label Created' orders page column content
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_label_column_content' ) );

		add_action( 'pr_shipping_dhl_label_created', array( $this, 'change_order_status' ), 10, 1 );
		add_action( 'woocommerce_email_order_details', array( $this, 'add_tracking_info'), 10, 4 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'create_label_on_status_changed' ), 10, 4 );

		// add 'DHL Request Pickup' to Order actions
		add_action( 'handle_bulk_actions-edit-shop_order', array($this, 'process_bulk_actions_pickup_request'), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_modal_window_assets'));
		add_action( 'manage_posts_extra_tablenav', array( $this, 'bulk_actions_fields_pickup_request'));
		add_action( 'admin_footer', array( $this, 'modal_content_fields_pickup_request'));

	}

	public function additional_meta_box_fields( $order_id, $is_disabled, $dhl_label_items, $dhl_obj ) {

		$order 				= wc_get_order( $order_id );
		$base_country_code 	= PR_DHL()->get_base_country();

		$this->add_package_fields( $order_id, $is_disabled, $dhl_label_items, $dhl_obj );

		// Preferred options for Germany only
		if( ( $base_country_code == 'DE' ) && ( $this->is_shipping_domestic( $order_id ) ) ) {

			if( $this->is_cod_payment_method( $order_id ) ) {

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_cod_value',
					'class'          	=> 'wc_input_decimal',
					'label'       		=> __( 'COD Amount:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_cod_value'] ) ? $dhl_label_items['pr_dhl_cod_value'] : $order->get_total(),
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );
			}

			if( ! empty( $this->shipping_dhl_settings['dhl_participation_return'] ) ) {

				echo '<hr/>';

				woocommerce_wp_checkbox( array(
					'id'          		=> 'pr_dhl_return_address_enabled',
					'label'       		=> __( 'Create return label: ', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_address_enabled'] ) ? $dhl_label_items['pr_dhl_return_address_enabled'] : $this->shipping_dhl_settings['dhl_default_return_address_enabled'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_name',
					'label'       		=> __( 'Name:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_name'] ) ? $dhl_label_items['pr_dhl_return_name'] : $this->shipping_dhl_settings['dhl_return_name'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_company',
					'label'       		=> __( 'Company:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_company'] ) ? $dhl_label_items['pr_dhl_return_company'] : $this->shipping_dhl_settings['dhl_return_company'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_address',
					'label'       		=> __( 'Street Address:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_address'] ) ? $dhl_label_items['pr_dhl_return_address'] : $this->shipping_dhl_settings['dhl_return_address'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_address_no',
					'label'       		=> __( 'Street Address Number:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_address_no'] ) ? $dhl_label_items['pr_dhl_return_address_no'] : $this->shipping_dhl_settings['dhl_return_address_no'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_address_city',
					'label'       		=> __( 'City:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_address_city'] ) ? $dhl_label_items['pr_dhl_return_address_city'] : $this->shipping_dhl_settings['dhl_return_address_city'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_address_state',
					'label'       		=> __( 'State:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_address_state'] ) ? $dhl_label_items['pr_dhl_return_address_state'] : $this->shipping_dhl_settings['dhl_return_address_state'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_address_zip',
					'label'       		=> __( 'Postcode:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['dhl_return_address_zip'] ) ? $dhl_label_items['dhl_return_address_zip'] : $this->shipping_dhl_settings['dhl_return_address_zip'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_phone',
					'label'       		=> __( 'Phone:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_phone'] ) ? $dhl_label_items['pr_dhl_return_phone'] : $this->shipping_dhl_settings['dhl_return_phone'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_return_email',
					'label'       		=> __( 'Email:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_return_email'] ) ? $dhl_label_items['pr_dhl_return_email'] : $this->shipping_dhl_settings['dhl_return_email'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				echo '<hr/>';
			}

			try {
				$shipping_address = $order->get_address( 'shipping' );

				$preferred_day_time = PR_DHL()->get_dhl_preferred_day_time( $shipping_address['postcode'] );

				if ( $preferred_day_time ) {
					$preferred_days = $preferred_day_time['preferred_day'];
					$preferred_days = array_keys($preferred_days);
					$preferred_days = array_combine($preferred_days, $preferred_days);
				}
			} catch (Exception $e) {
				// catch exception
			}

			$preferred_days[0] = __( 'none', 'dhl-for-woocommerce' );

			woocommerce_wp_select( array(
				'id'          		=> 'pr_dhl_preferred_day',
				'label'       		=> __( 'Preferred Day:', 'dhl-for-woocommerce' ),
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_preferred_day'] ) ? $dhl_label_items['pr_dhl_preferred_day'] : '',
				'options'			=> $preferred_days,
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			if( isset( $dhl_label_items['pr_dhl_preferred_location'] ) ) {

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_preferred_location',
					'label'       		=> __( 'Preferred Location (80 characters max): ', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> $dhl_label_items['pr_dhl_preferred_location'],
					'custom_attributes'	=> array( $is_disabled => $is_disabled, 'maxlength' => '80' )
				) );
			}

			if( isset( $dhl_label_items['pr_dhl_preferred_neighbour_name'] )  && isset( $dhl_label_items['pr_dhl_preferred_neighbour_address'] ) ) {

				$neighbor_info = $dhl_label_items['pr_dhl_preferred_neighbour_name'] . ', ' . $dhl_label_items['pr_dhl_preferred_neighbour_address'];
				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_preferred_neighbor',
					'label'       		=> __( 'Preferred Neighbor (80 characters max): ', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> $neighbor_info,
					'custom_attributes'	=> array( $is_disabled => $is_disabled, 'maxlength' => '80' )
				) );
			}

			// Visual age, need 16 or 18, drop down
			$visual_age = $dhl_obj->get_dhl_visual_age();
			woocommerce_wp_select( array(
				'id'          		=> 'pr_dhl_age_visual',
				'label'       		=> __( 'Visual Age Check:', 'dhl-for-woocommerce' ),
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_age_visual'] ) ? $dhl_label_items['pr_dhl_age_visual'] : $this->shipping_dhl_settings['dhl_default_age_visual'],
				'options'			=> $visual_age,
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );
/*
			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_personally',
				'label'       		=> __( 'Personally: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_personally'] ) ? $dhl_label_items['pr_dhl_personally'] : '',
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );
*/
			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_no_neighbor',
				'label'       		=> __( 'No Neighbour Delivery: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_no_neighbor'] ) ? $dhl_label_items['pr_dhl_no_neighbor'] : $this->shipping_dhl_settings['dhl_default_no_neighbor'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_named_person',
				'label'       		=> __( 'Named Person Only: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_named_person'] ) ? $dhl_label_items['pr_dhl_named_person'] : $this->shipping_dhl_settings['dhl_default_named_person'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			$this->crossborder_and_domestic_fields( $dhl_label_items, $is_disabled );

			echo '<hr/>';

			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_identcheck',
				'label'       		=> __( 'Ident-Check: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_identcheck'] ) ? $dhl_label_items['pr_dhl_identcheck'] : $this->shipping_dhl_settings['dhl_default_identcheck'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );
/*
			woocommerce_wp_text_input( array(
				'id'          		=> 'pr_dhl_identcheck_fname',
				'label'       		=> __( 'Identity Check - First Name: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_identcheck_fname'] ) ? $dhl_label_items['pr_dhl_identcheck_fname'] : '',
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			woocommerce_wp_text_input( array(
				'id'          		=> 'pr_dhl_identcheck_lname',
				'label'       		=> __( 'Identity Check - Last Name: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_identcheck_lname'] ) ? $dhl_label_items['pr_dhl_identcheck_lname'] : '',
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );
*/
			woocommerce_wp_text_input( array(
				'id'          		=> 'pr_dhl_identcheck_dob',
				'label'       		=> __( 'Ident-Check - Date of Birth: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_identcheck_dob'] ) ? $dhl_label_items['pr_dhl_identcheck_dob'] : $this->shipping_dhl_settings['dhl_default_identcheck_age'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled ),
				'class'				=> 'short date-picker'
			) );

			// $visual_age = $dhl_obj->get_dhl_visual_age();
			woocommerce_wp_select( array(
				'id'          		=> 'pr_dhl_identcheck_age',
				'label'       		=> __( 'Ident-Check - Minimum Age: ', 'dhl-for-woocommerce' ),
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_identcheck_age'] ) ? $dhl_label_items['pr_dhl_identcheck_age'] : '',
				'options'			=> $visual_age,
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			echo '<hr/>';

			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_is_codeable',
				'label'       		=> __( 'Print Only If Codeable: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_is_codeable'] ) ? $dhl_label_items['pr_dhl_is_codeable'] : $this->shipping_dhl_settings['dhl_default_is_codeable'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

			echo '<hr/>';

			woocommerce_wp_checkbox( array(
				'id'          		=> 'pr_dhl_routing',
				'label'       		=> __( 'Parcel Outlet Routing: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> isset( $dhl_label_items['pr_dhl_routing'] ) ? $dhl_label_items['pr_dhl_routing'] : $this->shipping_dhl_settings['dhl_default_routing'],
				'custom_attributes'	=> array( $is_disabled => $is_disabled )
			) );

            woocommerce_wp_text_input( array(
                'id'          		=> 'pr_dhl_routing_email',
                'label'       		=> __( 'Parcel Outlet Routing - Email: ', 'dhl-for-woocommerce' ),
                'placeholder' 		=> '',
                'description'		=> '',
                'value'       		=> isset( $dhl_label_items['pr_dhl_routing_email'] ) ? $dhl_label_items['pr_dhl_routing_email'] : $this->get_default_dhl_rounting_email( $order_id ),
                'custom_attributes'	=> array( $is_disabled => $is_disabled ),
            ) );

		} else { // Non-domestic shipment
			// Outside EU
			if( $this->is_crossborder_shipment( $order_id ) ) {

				// Duties drop down
				$duties_opt = $dhl_obj->get_dhl_duties();
				woocommerce_wp_select( array(
					'id'          		=> 'pr_dhl_duties',
					'label'       		=> __( 'Duties:', 'dhl-for-woocommerce' ),
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_duties'] ) ? $dhl_label_items['pr_dhl_duties'] : '',
					'options'			=> $duties_opt,
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );

				woocommerce_wp_text_input( array(
					'id'          		=> 'pr_dhl_invoice_num',
					'class'          	=> '',
					'label'       		=> __( 'Invoice Number:', 'dhl-for-woocommerce' ),
					'placeholder' 		=> '',
					'description'		=> '',
					'value'       		=> isset( $dhl_label_items['pr_dhl_invoice_num'] ) ? $dhl_label_items['pr_dhl_invoice_num'] : $order_id,
					'custom_attributes'	=> array( $is_disabled => $is_disabled )
				) );
			}

			$this->crossborder_and_domestic_fields( $dhl_label_items, $is_disabled );
		}

	}

	public function crossborder_and_domestic_fields( $dhl_label_items, $is_disabled ){

		woocommerce_wp_hidden_input( array(
			'id'          		=> 'pr_dhl_email_notification',
			'label'       		=> __( 'Email Notification:', 'dhl-for-woocommerce' ),
			'placeholder' 		=> '',
			'description'		=> '',
			'value'       		=> isset( $dhl_label_items['pr_dhl_email_notification'] ) ? $dhl_label_items['pr_dhl_email_notification'] : false,
		) );

		woocommerce_wp_checkbox( array(
			'id'          		=> 'pr_dhl_additional_insurance',
			'label'       		=> __( 'Additional Insurance:', 'dhl-for-woocommerce' ),
			'placeholder' 		=> '',
			'description'		=> '',
			'value'       		=> isset( $dhl_label_items['pr_dhl_additional_insurance'] ) ? $dhl_label_items['pr_dhl_additional_insurance'] : $this->shipping_dhl_settings['dhl_default_additional_insurance'],
			'custom_attributes'	=> array( $is_disabled => $is_disabled )
		) );

		woocommerce_wp_checkbox( array(
			'id'          		=> 'pr_dhl_premium',
			'label'       		=> __( 'Premium: ', 'dhl-for-woocommerce' ),
			'placeholder' 		=> '',
			'description'		=> '',
			'value'       		=> isset( $dhl_label_items['pr_dhl_premium'] ) ? $dhl_label_items['pr_dhl_premium'] : $this->shipping_dhl_settings['dhl_default_premium'],
			'custom_attributes'	=> array( $is_disabled => $is_disabled )
		) );

		woocommerce_wp_checkbox( array(
			'id'          		=> 'pr_dhl_bulky_goods',
			'label'       		=> __( 'Bulky Goods: ', 'dhl-for-woocommerce' ),
			'placeholder' 		=> '',
			'description'		=> '',
			'value'       		=> isset( $dhl_label_items['pr_dhl_bulky_goods'] ) ? $dhl_label_items['pr_dhl_bulky_goods'] : $this->shipping_dhl_settings['dhl_default_bulky_goods'],
			'custom_attributes'	=> array( $is_disabled => $is_disabled )
		) );
	}

	protected function add_package_fields( $order_id, $is_disabled, $dhl_label_items, $dhl_obj ) {

		echo '<hr style="clear:both;">';

		$weight_uom = get_option( 'woocommerce_weight_unit' );
		$dim_uom = get_option( 'woocommerce_dimension_unit' );

		$total_packages = isset( $dhl_label_items['pr_dhl_total_packages'] ) ? $dhl_label_items['pr_dhl_total_packages'] : '1';

		$packages_enabled = isset( $dhl_label_items['pr_dhl_multi_packages_enabled'] ) ? $dhl_label_items['pr_dhl_multi_packages_enabled'] : '';

		$numbers = array();
		for ( $i = 1; $i <= 50; $i++ ) $numbers[$i] = $i;

		woocommerce_wp_checkbox( array(
			'id'          		=> 'pr_dhl_multi_packages_enabled',
			'label'       		=> __( 'Send multiple packages: ', 'dhl-for-woocommerce' ),
			'placeholder' 		=> '',
			'description'		=> '',
			'value'       		=> $packages_enabled,
			'custom_attributes'	=> array( $is_disabled => $is_disabled )
		) );

		woocommerce_wp_select( array(
			'id'	          	=> 'pr_dhl_total_packages',
			'name'          	=> 'pr_dhl_total_packages',
			'label'       		=>  __( 'Total Packages:', 'dhl-for-woocommerce' ),
			'value'				=> $total_packages,
			'options'			=> $numbers,
			'custom_attributes'	=> array( $is_disabled => $is_disabled, 'data-current' => $total_packages,  "autocomplete" => "off" ),
			'wrapper_class'		=> 'dhl-total-packages'
		) );

		echo '<div class="total_packages_container" style="margin-bottom:15px;">
				<div class="package_header">
					<div class="package_header_field first">Package</div>
					<div class="package_header_field">Weight</div>
					<div class="package_header_field">Length</div>
					<div class="package_header_field">Width</div>
					<div class="package_header_field">Height</div>
				</div>';

		if ( empty( $packages_enabled ) ) {
			echo '	<div class="package_item">
						<div class="package_item_field package_number first"><input type="text" name="pr_dhl_packages_number[]" data-sequence="1" value="1" maxlength="70" /></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_weight[]" placeholder="'.$weight_uom.'" /></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_length[]" placeholder="'.$dim_uom.'" /></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_width[]" placeholder="'.$dim_uom.'" /></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_height[]" placeholder="'.$dim_uom.'" /></div>
					</div>';
		} else {
			for ($i=0, $seq=1; $i<intval($total_packages); $i++, $seq++) {
				$number = !empty($dhl_label_items['pr_dhl_packages_number'][$i]) ? $dhl_label_items['pr_dhl_packages_number'][$i] : $seq;
				$weight = !empty($dhl_label_items['pr_dhl_packages_weight'][$i]) ? $dhl_label_items['pr_dhl_packages_weight'][$i] : '';
				$length = !empty($dhl_label_items['pr_dhl_packages_length'][$i]) ? $dhl_label_items['pr_dhl_packages_length'][$i] : '';
				$width = !empty($dhl_label_items['pr_dhl_packages_width'][$i]) ? $dhl_label_items['pr_dhl_packages_width'][$i] : '';
				$height = !empty($dhl_label_items['pr_dhl_packages_height'][$i]) ? $dhl_label_items['pr_dhl_packages_height'][$i] : '';

				echo '	<div class="package_item">
						<div class="package_item_field package_number first"><input type="text" name="pr_dhl_packages_number[]" data-sequence="'.$seq.'" value="'.$number.'" maxlength="70" autocomplete="off" disabled /></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_weight[]" value="'.$weight.'" placeholder="'.$weight_uom.'" autocomplete="off" '. $is_disabled .'/></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_length[]" value="'.$length.'" placeholder="'.$dim_uom.'" autocomplete="off" '. $is_disabled .'/></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_width[]" value="'.$width.'" placeholder="'.$dim_uom.'" autocomplete="off" '. $is_disabled .'/></div>
						<div class="package_item_field clearable"><input type="text" name="pr_dhl_packages_height[]" value="'.$height.'" placeholder="'.$dim_uom.'" autocomplete="off" '. $is_disabled .'/></div>
					</div>';
			}
		}

		echo '</div>';
		// echo '<hr style="clear:both;">';
	}
	/**
	 * Order Tracking Save
	 *
	 * Function for saving tracking items
	 */
	public function get_additional_meta_ids( ) {
		return array( 'pr_dhl_cod_value', 'pr_dhl_preferred_day', 'pr_dhl_preferred_location', 'pr_dhl_preferred_neighbor', 'pr_dhl_duties', 'pr_dhl_age_visual', 'pr_dhl_email_notification', 'pr_dhl_additional_insurance', 'pr_dhl_personally', 'pr_dhl_no_neighbor', 'pr_dhl_named_person', 'pr_dhl_premium', 'pr_dhl_bulky_goods', 'pr_dhl_is_codeable', 'pr_dhl_identcheck', 'pr_dhl_identcheck_dob', 'pr_dhl_identcheck_age', 'pr_dhl_return_address_enabled', 'pr_dhl_return_name', 'pr_dhl_return_company', 'pr_dhl_return_address','pr_dhl_return_address_no', 'pr_dhl_return_address_city', 'pr_dhl_return_address_state', 'pr_dhl_return_address_zip', 'pr_dhl_return_phone', 'pr_dhl_return_email', 'pr_dhl_routing', 'pr_dhl_routing_email', 'pr_dhl_total_packages', 'pr_dhl_multi_packages_enabled', 'pr_dhl_packages_number', 'pr_dhl_packages_weight', 'pr_dhl_packages_length', 'pr_dhl_packages_width', 'pr_dhl_packages_height', 'pr_dhl_invoice_num' );
	}

	protected function get_tracking_url() {
		return PR_DHL_PAKET_TRACKING_URL;
	}

	protected function get_label_args_settings( $order_id, $dhl_label_items ) {

		$order = wc_get_order( $order_id );
		$billing_address = $order->get_address( );
		$shipping_address = $order->get_address( 'shipping' );

		$shipping_address_email = '';
		// If shipping email doesn't exist, try to get billing email
		if( ! isset( $shipping_address['email'] ) && isset( $billing_address['email'] ) ) {
			$shipping_address_email = $billing_address['email'];
		} else {
            $shipping_address_email = $shipping_address['email'];
        }

		// Get services etc.
		$meta_box_ids = $this->get_additional_meta_ids();

		foreach ($meta_box_ids as $value) {
			$api_key = str_replace('pr_dhl_', '', $value);
			if ( isset( $dhl_label_items[ $value ] ) ) {
				$args['order_details'][ $api_key ] = $dhl_label_items[ $value ];
			}
		}

		// Get settings
		// $this->shipping_dhl_settings = PR_DHL()->get_shipping_dhl_settings();

		$setting_ids = array( 'dhl_api_user','dhl_api_pwd', 'dhl_sandbox', 'dhl_api_sandbox_user', 'dhl_api_sandbox_pwd', 'dhl_add_logo', 'dhl_shipper_reference', 'dhl_account_num', 'dhl_shipper_name', 'dhl_shipper_company', 'dhl_shipper_address','dhl_shipper_address_no', 'dhl_shipper_address_city', 'dhl_shipper_address_state', 'dhl_shipper_address_zip', 'dhl_shipper_phone', 'dhl_shipper_email', 'dhl_shipper_reference', 'dhl_bank_holder', 'dhl_bank_name', 'dhl_bank_iban', 'dhl_bank_bic', 'dhl_bank_ref', 'dhl_bank_ref_2', 'dhl_participation_return', 'dhl_email_notification', 'dhl_phone_notification' );

		foreach ($setting_ids as $value) {
			$api_key = str_replace('dhl_', '', $value);
			if ( isset( $this->shipping_dhl_settings[ $value ] ) ) {
				$args['dhl_settings'][ $api_key ] = htmlspecialchars_decode( $this->shipping_dhl_settings[ $value ] );

				if( stripos($value, 'bank_ref') !== false ) {

					$args['dhl_settings'][ $api_key ] = str_replace( '{order_id}', $order_id, $args['dhl_settings'][ $api_key ] );

					$args['dhl_settings'][ $api_key ] = str_replace( '{email}', $shipping_address_email, $args['dhl_settings'][ $api_key ] );
				}
			}
		}

		$args['dhl_settings'][ 'shipper_country' ] = PR_DHL()->get_base_country();
		$args['dhl_settings'][ 'return_country' ] = PR_DHL()->get_base_country();
		$args['dhl_settings'][ 'participation' ] = $this->shipping_dhl_settings[ 'dhl_participation_' . $dhl_label_items['pr_dhl_product'] ];
		$args['dhl_settings'][ 'label_format' ] = $this->shipping_dhl_settings['dhl_label_format'];

		return $args;
	}

	protected function delete_label_args( $order_id ) {
		$args = $this->get_dhl_label_tracking( $order_id );

		$args['api_user'] 	= $this->shipping_dhl_settings['dhl_api_user'];
		$args['api_pwd'] 	= $this->shipping_dhl_settings['dhl_api_pwd'];
		$args['sandbox'] 	= $this->shipping_dhl_settings['dhl_sandbox'];

		// If there are multiple tracking numbers, get the first one to search for the string in order notes
		if (is_array( $args['tracking_number'] ) ) {
			$args['tracking_number'] = $args['tracking_number'][0];
		}

		return $args;
	}

	protected function save_default_dhl_label_items( $order_id ) {

	    parent::save_default_dhl_label_items( $order_id );

        $base_country_code 	= PR_DHL()->get_base_country();
	    // Services and COD only for Germany
        if( ( $base_country_code == 'DE' ) && ( $this->is_shipping_domestic( $order_id ) ) ) {
            $dhl_label_items = $this->get_dhl_label_items($order_id);

			$settings_default_ids = array(
                'pr_dhl_is_codeable',
                'pr_dhl_return_address_enabled',
				'pr_dhl_return_name',
				'pr_dhl_return_company',
				'pr_dhl_return_address',
				'pr_dhl_return_address_no',
				'pr_dhl_return_address_city',
				'pr_dhl_return_address_state',
				'pr_dhl_return_address_zip',
				'pr_dhl_return_phone',
				'pr_dhl_return_email',
                'pr_dhl_age_visual',
                'pr_dhl_additional_insurance',
                'pr_dhl_no_neighbor',
                'pr_dhl_named_person',
                'pr_dhl_premium',
                'pr_dhl_bulky_goods',
                'pr_dhl_identcheck',
                'pr_dhl_identcheck_age',
                'pr_dhl_identcheck_dob',
                'pr_dhl_routing'
            );

            foreach ($settings_default_ids as $default_id) {
                $id_name = str_replace("pr_dhl_", '', $default_id);

                if ( !isset($dhl_label_items[$default_id]) ) {
                    $dhl_label_items[$default_id] = isset( $this->shipping_dhl_settings['dhl_default_' . $id_name] ) ? $this->shipping_dhl_settings['dhl_default_' . $id_name] : '';
					//Check alternate setting id format if not found in dhl_default prefix id
					if ( !isset( $this->shipping_dhl_settings['dhl_default_' . $id_name] ) ) {
						$dhl_label_items[$default_id] = isset( $this->shipping_dhl_settings['dhl_' . $id_name] ) ? $this->shipping_dhl_settings['dhl_' . $id_name] : '';
					}
                }
            }

            $order = wc_get_order($order_id);
            if ($this->is_cod_payment_method($order_id) && empty($dhl_label_items['pr_dhl_cod_value'])) {
                $dhl_label_items['pr_dhl_cod_value'] = $order->get_total();
            }

            $this->save_dhl_label_items($order_id, $dhl_label_items);
        }

	}

	protected function get_default_dhl_rounting_email( $order_id ) {
        $order = wc_get_order( $order_id );
        $billing_address = $order->get_address();
		return $billing_address['email'];
	}

	public function get_bulk_actions() {

		$shop_manager_actions = array();

		$shop_manager_actions = array(
			'pr_dhl_create_labels'      => __( 'DHL Create Labels', 'dhl-for-woocommerce' ),
			'pr_dhl_request_pickup'      => __( 'DHL Request Pickup', 'dhl-for-woocommerce' )
		);

		return $shop_manager_actions;
	}

	public function validate_bulk_actions( $action, $order_ids ) {

		$orders_count 	= count( $order_ids );

		if( 'pr_dhl_create_labels' === $action || 'pr_dhl_request_pickup' === $action ){

			if ( $orders_count < 1 ) {

				return __( 'No orders selected for the DHL bulk action, please select orders before performing the DHL action.', 'dhl-for-woocommerce' );

			}

		}

		return '';
	}

	protected function is_cod_payment_method( $order_id ) {
		$base_country_code 	= PR_DHL()->get_base_country();

		if( ( $base_country_code == 'DE' ) && ( $this->is_shipping_domestic( $order_id ) ) ) {
			return parent::is_cod_payment_method( $order_id );
		} else {
		    return false;
        }
	}

	public function change_order_status( $order_id ){

		if( isset( $this->shipping_dhl_settings['dhl_change_order_status_completed'] ) && ( $this->shipping_dhl_settings['dhl_change_order_status_completed'] == 'yes' ) ) {
			$order = wc_get_order( $order_id );
			$order->update_status('completed');

		}
	}

	public function add_tracking_info( $order, $sent_to_admin, $plain_text, $email ){

		if( $email->id != 'customer_completed_order' ){
			return;
		}

		if( isset( $this->shipping_dhl_settings['dhl_add_tracking_info_completed'] ) && ( $this->shipping_dhl_settings['dhl_add_tracking_info_completed'] == 'yes' ) ) {

            if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
                $order_id = $order->get_id();
            } else {
                $order_id = $order->id;
            }

            $tracking_note = $this->get_tracking_note( $order_id );

            if( ! empty( $tracking_note ) ) {
			    echo '<p>' . $tracking_note . '</p>';
            }
		}

	}

	public function create_label_on_status_changed($order_id, $status_from, $status_to, $order ){

		$status_setting = str_replace('wc-', '', $this->shipping_dhl_settings['dhl_create_label_on_status'] );
		if( $status_setting == $status_to ){
			$this->process_bulk_actions( 'pr_dhl_create_labels', array( $order_id ), 1 );
		}
	}

	protected function get_tracking_link( $order_id ) {
		$label_tracking_info = $this->get_dhl_label_tracking( $order_id );
		if( empty( $label_tracking_info['tracking_number'] ) ) {
			return '';
		}

		$tracking_number = $label_tracking_info['tracking_number'];

		$tracking_link_str = '';
		if (is_array( $tracking_number ) ) {
			foreach ($tracking_number as $key => $value) {
				$tracking_link[ $key ] = sprintf( __( '<a href="%s%s" target="_blank">%s</a>', 'dhl-for-woocommerce' ), $this->get_tracking_url(), $value, $value);
			}

			$tracking_link_str = implode('<br/>', $tracking_link);
		} else {
			$tracking_link_str = parent::get_tracking_link( $order_id );
		}

		return $tracking_link_str;
	}

	public function add_order_label_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {
			$new_columns[ $column_name ] = $column_info;

			if ( 'order_total' === $column_name ) {
				$new_columns['dhl_label_created']      = __( 'DHL Label Created', 'dhl-for-woocommerce' );
				$new_columns['dhl_tracking_number']    = __( 'DHL Tracking Number', 'dhl-for-woocommerce' );
			}
		}

		return $new_columns;
	}

	public function add_order_label_column_content( $column ) {
		global $post;

		$order_id = $post->ID;

		if ( $order_id ) {
			if( 'dhl_label_created' === $column ) {
				echo $this->get_print_status( $order_id );
			}

			if( 'dhl_tracking_number' === $column ) {
				$tracking_link = $this->get_tracking_link( $order_id );
				echo empty($tracking_link) ? '<strong>&ndash;</strong>' : $tracking_link;
			}

		}
	}

	private function get_print_status( $order_id ) {
		$label_tracking_info = $this->get_dhl_label_tracking( $order_id );

		if( empty( $label_tracking_info ) ) {
			return '<strong>&ndash;</strong>';
		} else {
			return '&#10004';
		}
	}


	public function process_order_action_request_pickup( $order_id, $pickup_type, $pickup_date, $transportation_type) {

		$array_messages = array();

		$order = wc_get_order( $order_id );

		$pickup_business_hours = [];
		$pickup_business_hours[0]['start'] = $this->shipping_dhl_settings['dhl_business_hours_1_start'];
		$pickup_business_hours[0]['end'] = $this->shipping_dhl_settings['dhl_business_hours_1_end'];
		$pickup_business_hours[1]['start'] = $this->shipping_dhl_settings['dhl_business_hours_2_start'];
		$pickup_business_hours[1]['end'] = $this->shipping_dhl_settings['dhl_business_hours_2_end'];

		// Gather args for DHL API call
		$args = $this->get_label_args( $order_id );

		// Allow third parties to modify the args to the DHL APIs
		$args = apply_filters('pr_shipping_dhl_label_args', $args, $order_id );

		$args['dhl_pickup_type'] = $pickup_type;
		$args['dhl_pickup_date'] = $pickup_date;

		$args['dhl_pickup_business_hours'] = $pickup_business_hours;
		//$args['dhl_pickup_transportation_type'] = $transportation_type; // Disabled, use bulky_goods to determine transportation type (see Pickup_Request_info.php)

		//$args['order_details']['bulky_goods'] = 'yes'; // TEST bulky goods

		//Get label (s)
		$label_tracking_info = $this->get_dhl_label_tracking( $order_id );
		$tracking_number = ( isset($label_tracking_info['tracking_number']) ) ? $label_tracking_info['tracking_number'] : '';
		$tracking_numbers = [];

		$tracking_link_str = '';
		if ( is_array( $tracking_number ) ) {
			foreach ($tracking_number as $key => $value) {
				$tracking_numbers[] = $value;
			}
		} else {
			$tracking_numbers[] = $tracking_number;
		}

		$args['dhl_pickup_label_tracking'] = $tracking_numbers;
		$args['dhl_pickup_billing_number'] = $args['dhl_settings']['account_num'].self::DHL_PICKUP_PRODUCT.$args['dhl_settings']['participation'];

		// Allow third parties to modify the args to the DHL APIs
		$args = apply_filters('pr_shipping_dhl_paket_pickup_args', $args, $order_id );

		try {

			$base_country_code 	= PR_DHL()->get_base_country();
			$pickup_rest = new PR_DHL_API_REST_Paket( $base_country_code );
			$pickup_response = $pickup_rest->request_dhl_pickup( $args );

			//Error?
			if ( isset($pickup_response->orderNumber) ) {

				$response_pickup_order_number = isset($pickup_response->orderNumber) ? $pickup_response->orderNumber : '';
				$response_pickup_date = isset($pickup_response->pickupDate) ? $pickup_response->pickupDate : '';
				$response_pickup_free_of_charge = isset($pickup_response->freeOfCharge) ? $pickup_response->freeOfCharge : '';
				$response_pickup_type = isset($pickup_response->pickupType) ? $pickup_response->pickupType : '';

				// add the order note
				$message = sprintf( __( 'DHL pickup scheduled for %s', 'dhl-for-woocommerce' ), $response_pickup_date );
				$order->add_order_note( $message );

			    // add the flag
			   	update_post_meta( $order_id, '_pr_dhl_pickup_order_number', $pickup_order_number  );
				update_post_meta( $order_id, '_pr_dhl_pickup_date', $pickup_date  );

				array_push($array_messages, array(
					'message' => sprintf( __( 'Order #%s: DHL Pickup Request created', 'dhl-for-woocommerce'), $order->get_order_number() ),
					'type' => 'success',
				));

			} else {
				//Errors
				if ( isset($pickup_response[0]->code) ) {
					$pickup_response_admin_notice = $pickup_response[0]->message;
				} else {
					$pickup_response_admin_notice = __( 'Error message detail is not exist!', 'dhl-for-woocommerce' );
				}

				array_push($array_messages, array(
					'message' => sprintf( __( 'Order #%s: %s', 'dhl-for-woocommerce'), $order->get_order_number(), $pickup_response_admin_notice ),
					'type' => 'error',
				));
			}

		} catch (Exception $e) {
			array_push($array_messages, array(
				'message' => sprintf( __( 'Order #%s: %s', 'dhl-for-woocommerce'), $order->get_order_number(), $e->getMessage() ),
				'type' => 'error',
			));
		}

		return $array_messages;

	}

	public function process_bulk_actions_pickup_request( $redirect_url, $action, $post_ids ) {
		if ( $action == 'pr_dhl_request_pickup' ) {

			$pickup_type = isset($_GET['pr_dhl_request_pickup']) ? sanitize_text_field($_GET['pr_dhl_request_pickup']) : '';
			$pickup_date = isset($_GET['pr_dhl_request_pickup_date']) ? sanitize_text_field($_GET['pr_dhl_request_pickup_date']) : '';
			$transportation_type = isset($_GET['pr_dhl_request_transportation_type']) ? sanitize_text_field($_GET['pr_dhl_request_transportation_type']) : '';

			$array_messages = get_option( '_pr_dhl_bulk_action_confirmation' );
	    	if ( empty( $array_messages ) || !is_array( $array_messages ) ) {
	    		$array_messages = array( 'msg_user_id' => get_current_user_id() );
			}

			$message = $this->validate_bulk_actions( $action, $post_ids );
			if ( ! empty( $message ) ) {
				array_push($array_messages, array(
					'message' => $e->getMessage(),
					'type' => 'error',
				));
			} else {

				try {

					foreach ($post_ids as $order_id) {
						$new_array_messages = $this->process_order_action_request_pickup( $order_id, $pickup_type, $pickup_date, $transportation_type);
						$array_messages = array_merge($array_messages, $new_array_messages);
					}

				} catch (Exception $e) {
					array_push($array_messages, array(
						'message' => $e->getMessage(),
						'type' => 'error',
					));
				}
			}


			update_option( '_pr_dhl_bulk_action_confirmation', $array_messages );

			$redirect_url = add_query_arg('dhl_request_pickup', count($post_ids), $redirect_url);
		}
		return $redirect_url;
	}

	public function enqueue_modal_window_assets() {
		global $pagenow, $typenow;

		if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
			// Enqueue the assets
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');

			wp_enqueue_script(
				'wc-shipment-dhl-paket-pickup-bulk-js',
				PR_DHL_PLUGIN_DIR_URL . '/assets/js/pr-dhl-paket-pickup-bulk.js',
				array(),
				PR_DHL_VERSION,
				true
			);
		}
	}

	public function bulk_actions_fields_pickup_request() {
		global $pagenow, $typenow;

		if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {

			//Hidden inputs
			woocommerce_wp_hidden_input( array(
				'id'          		=> 'pr_dhl_request_pickup',
				'name'          		=> 'pr_dhl_request_pickup',
				'value'       		=> 'asap',
			));
			woocommerce_wp_hidden_input( array(
				'id'          		=> 'pr_dhl_request_pickup_date',
				'name'          		=> 'pr_dhl_request_pickup_date',
				'value'       		=> date('Y-m-d', strtotime('+1 day')),
			));
			woocommerce_wp_hidden_input( array(
				'id'          		=> 'pr_dhl_request_transportation_type',
				'name'          		=> 'pr_dhl_request_transportation_type',
				'value'       		=> 'PAKET',
			));
		}
	}

	public function modal_content_fields_pickup_request() {
		global $pagenow, $typenow;

		if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
		?>
		<div id="dhl-paket-pickup-modal" style="display:none;">

			<?php
			echo '<div id="dhl-paket-action-request-pickup">';

			echo '<h3>'.__( 'Schedule a DHL Pickup Request.', 'dhl-for-woocommerce' ).'</h3>';
			echo '<b>'.__( 'Your Shipper address and business hours from Settings will be used for the pickup.', 'dhl-for-woocommerce' ).'</b><br>';
			echo '<hr>';

			/*
			$transport_options = [
				'PAKET' => 'PAKET',
				'SPERRGUT' => 'SPERRGUT'
			];

			woocommerce_wp_select( array(
				'id'          		=> 'pr_dhl_request_pickup_transportation_type',
				'label'       		=> __( 'Transportation Type:', 'dhl-for-woocommerce' ),
				'description'		=> '',
				'value'       		=> 'PAKET',
				'options'			=> $transport_options,
			) );

			echo '<hr><br>';
			*/

			woocommerce_wp_radio( array(
				'id'          		=> 'pr_dhl_request_pickup_modal',
				'label'       		=> __( 'Request Pickup: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> 'asap',
				'class'				=> 'short',
				'options'			=> array( 'asap' => __( 'Pickup ASAP', 'dhl-for-woocommerce' ), 'date' => __( 'Pickup Date', 'dhl-for-woocommerce' ) )
			) );

			echo '<div class="pr_dhl_request_pickup_date_field" style="display: none;">';

			woocommerce_wp_text_input( array(
				'id'          		=> 'pr_dhl_request_pickup_date_modal',
				'label'       		=> __( 'Pickup Date: ', 'dhl-for-woocommerce' ),
				'placeholder' 		=> '',
				'description'		=> '',
				'value'       		=> date('Y-m-d', strtotime('+1 day')),
				'custom_attributes'	=> array( 'min' => date('Y-m-d'), 'max' => date('Y-m-d', strtotime('+30 days')) ),
				'class'				=> 'short',
				'type'	=> 'date'
			) );

			echo '</div>';
			echo '<br><button type="button" class="button button-primary" id="pr_dhl_pickup_proceed">'.__( 'Submit', 'dhl-for-woocommerce' ).'</button>';

			echo '</div>';
			?>
		</div>
		<?php
		}
	}

}

endif;
