<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package     product-variation-swatches-for-woocommerce
 * @subpackage  product-variation-swatches-for-woocommerce/public
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Public')):
 
class THWVSF_Public {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_action('after_setup_theme', array($this, 'define_public_hooks'));
	}

	public function enqueue_styles_and_scripts() {
		global $wp_scripts;
		
		$is_quick_view = THWVSF_Utils::is_quick_view_plugin_active();

		if(is_product() ||( $is_quick_view && (is_shop() || is_archive())) || apply_filters('thwvsf_enqueue_public_scripts', false)){
			//$debug_mode = apply_filters('thwvsf_debug_mode', false);
			$suffix = '';
			
			$jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.12.1';
			
			$this->enqueue_styles($suffix, $jquery_version,$is_quick_view);
			$this->enqueue_scripts($suffix, $jquery_version,$is_quick_view);
		}
	}

	private function enqueue_styles($suffix, $jquery_version, $is_quick_view) {

		$settings = THWVSF_Utils::get_advanced_swatches_settings();
		$disable_style_sheet = '';

		if($settings && is_array($settings)){

			$global_settings = isset($settings['swatch_global_settings']) ? $settings['swatch_global_settings'] : $settings;
			$disable_style_sheet = isset($global_settings['disable_style_sheet']) ? $global_settings['disable_style_sheet'] : '';
		}	

		if($disable_style_sheet === 'yes'){

			return;
		}
		wp_enqueue_style('thwvsf-public-style', THWVSF_ASSETS_URL_PUBLIC . 'css/thwvsf-public.min.css', $this->version);
		$design_type_css = array();
		if($settings && is_array($settings)){

			$global_settings   = isset($settings['swatch_global_settings']) ? $settings['swatch_global_settings'] : $settings;
			$design_type_css[] = $this->get_global_settings_css($global_settings,$is_quick_view);

			if(isset($settings['swatch_design_default']) ){

				foreach ($settings as $design_key => $design_values) {

					if($design_key != 'swatch_global_settings'){

						$design_type_css[] = $this->get_design_type_css($design_values, $design_key, $is_quick_view);
					}
				}

			}else{

				$attr_design_mapping = THWVSF_Utils::get_design_swatches_settings();
				
				if($attr_design_mapping && is_array($attr_design_mapping)){

					$default_designs = THWVSF_Admin_Utils::$sample_design_labels;

					foreach ($default_designs as $design_key => $design_name) {

						$design_type_css[]   = $this->get_design_type_css($settings, $design_key, $is_quick_view);
					}

				}else{

					$design_type_css[] = $this->get_design_type_css($settings,'swatch_design_default',$is_quick_view);
				}
			}

			if(is_array($design_type_css)){

				foreach ($design_type_css as $style) {
			 		wp_add_inline_style('thwvsf-public-style', $style );
			 	}
			}else{

				wp_add_inline_style('thwvsf-public-style', $design_type_css);
			} 
		}
	}

	private function get_design_type_css($settings, $design_key, $is_quick_view){

		$icon_width = isset($settings['icon_width']) ? $settings['icon_width'] : '45px';
		$icon_width = is_numeric($icon_width)? $icon_width.'px' : $icon_width;
		
		$icon_height = isset($settings['icon_height']) ? $settings['icon_height'] : '45px';
		$icon_height = is_numeric($icon_height)? $icon_height.'px' : $icon_height;
		// Label Settings
		$label_icon_height = isset($settings['icon_label_height']) ? $settings['icon_label_height'] : '45px';
		$label_icon_height = is_numeric($label_icon_height )? $label_icon_height .'px' : $label_icon_height ;

		$label_icon_width = isset($settings['icon_label_width']) ? $settings['icon_label_width'] : '45px';
		$label_icon_width = is_numeric($label_icon_width )? $label_icon_width.'px' : $label_icon_width ;

		$label_background_color = isset($settings['label_background_color']) ? $settings['label_background_color'] : '#fff';
		$label_text_color       = isset($settings['label_text_color']) ? $settings['label_text_color'] : '#000';

		$label_size = isset($settings['label_size']) ? $settings['label_size'] : '';
		$label_size = is_numeric($label_size )? $label_size.'px' : $label_size ;
		
		$icon_shape = isset($settings['icon_shape']) ? $settings['icon_shape'] : 'square';
		$icon_roundness = $icon_shape == 'square' ? '2px' : '50px';

		$icon_border_color  = isset($settings['icon_border_color']) ? $settings['icon_border_color'] : '#d1d7da';

		$icon_border_color_selected = isset($settings['icon_border_color_selected']) ? $settings['icon_border_color_selected'] : '#8b98a6';
		$icon_border_color_hover = isset($settings['icon_border_color_hover']) ? $settings['icon_border_color_hover'] : '#b7bfc6';

		$tt_text_background_color = isset($settings['tooltip_text_background_color']) ? $settings['tooltip_text_background_color']: '#000000';
		$tt_text_color = isset($settings['tooltip_text_color']) ? $settings['tooltip_text_color']: '#ffffff';
		
		$label_selection_style = isset($settings['label_selection_style']) && $settings['label_selection_style'] ? $settings['label_selection_style'] : 'border';
		$selection_style = isset($settings['common_selection_style']) && $settings['common_selection_style'] ? $settings['common_selection_style'] : 'border';

		$custom_css = "
       		.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.attr_{$design_key} {
               	width: {$icon_width}; 
               	border-radius: {$icon_roundness}; 
               	height:  {$icon_height};
               	box-shadow: 0 0 0 1px {$icon_border_color}; 
           	}
           	.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.thwvsf-label-li.attr_{$design_key}{
               	width: {$label_icon_width}; 
               	height:  {$label_icon_height};
               	color: {$label_text_color};
               	background-color: {$label_background_color};
           	}
           	.thwvsf-wrapper-ul .thwvsf-label-li.attr_{$design_key} .thwvsf-item-span.item-span-text{
           		font-size: {$label_size};
           	}
			.thwvsf-wrapper-ul .thwvsf-tooltip .tooltiptext.tooltip_{$design_key} {
					background-color: {$tt_text_background_color};
					color : {$tt_text_color};
			}
			.thwvsf-wrapper-ul .thwvsf-tooltip .tooltiptext.tooltip_{$design_key}::after{
					border-color: {$tt_text_background_color} transparent transparent;
			}
			.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.attr_{$design_key}.thwvsf-selected, .thwvsf-wrapper-ul .thwvsf-wrapper-item-li.attr_{$design_key}.thwvsf-selected:hover{
			    -webkit-box-shadow: 0 0 0 2px {$icon_border_color_selected};
			    box-shadow: 0 0 0 2px {$icon_border_color_selected};
			}
			.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.attr_{$design_key}:hover{
				-webkit-box-shadow: 0 0 0 3px {$icon_border_color_hover};
				box-shadow: 0 0 0 3px {$icon_border_color_hover};
			}
		";

		if($selection_style == 'border_with_tick'){
	    	$tick_color = isset($settings['tick_color']) && $settings['tick_color'] ? $settings['tick_color'] : '';
	    	$tick_size  = isset($settings['tick_size']) && $settings['tick_size'] ? $settings['tick_size'] : '';
    		$custom_css .= "
				.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.attr_{$design_key}.thwvsf-selected:after {

					content: '\\2714';
	    			display: block;
	    			position: absolute;
	    			color: $tick_color ;
	    			font-size:$tick_size;
	    			text-align:center;
				}
			";
	    }

	    $custom_css .= "
			.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.thwvsf-label-li.attr_{$design_key}.thwvsf-selected:after {
				content: '';
			}
		";

        if($label_selection_style == 'background_color'){

			$label_background_color_hover = isset($settings['label_background_color_hover']) && $settings['label_background_color_hover'] ? $settings['label_background_color_hover'] : '';
			$label_text_color_hover = isset($settings['label_text_color_hover']) && $settings['label_text_color_hover'] ? $settings['label_text_color_hover'] : '';
			$label_background_color_selection = isset($settings['label_background_color_selection']) && $settings['label_background_color_selection'] ? $settings['label_background_color_selection'] : '';
			$label_text_color_selection = isset($settings['label_text_color_selection']) && $settings['label_text_color_selection'] ? $settings['label_text_color_selection'] : '';

			$custom_css .= "
			 
				.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.thwvsf-label-li.attr_{$design_key}:hover {
					background-color : $label_background_color_hover;
					color : $label_text_color_hover;
	        		
				}
				.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.thwvsf-label-li.attr_{$design_key}.thwvsf-selected {
					background-color : $label_background_color_selection;
					color : $label_text_color_selection;
	        		
				}
			";
		}else if($label_selection_style == 'border_with_tick'){
			$tick_color = isset($settings['label_tick_color']) && $settings['label_tick_color'] ? $settings['label_tick_color'] : '';
	    	$tick_size  = isset($settings['label_tick_size']) && $settings['label_tick_size'] ? $settings['label_tick_size'] : '';
			$custom_css .= "
				.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.thwvsf-label-li.attr_{$design_key}.thwvsf-selected:after {

					content: '\\2714';
	    			display: block;
	    			position: absolute;
	    			color: $tick_color;
	    			font-size:$tick_size;
	    			text-align:center;
				}
			";
		}

	    return $custom_css;
	}

	private function get_global_settings_css($settings, $is_quick_view){

		$attr_behavior = isset($settings['behavior_for_unavailable_variation']) && $settings['behavior_for_unavailable_variation'] ? $settings['behavior_for_unavailable_variation'] : 'blur_with_cross';
		$behavior_of_out_of_stock =isset($settings['behavior_of_out_of_stock']) ? $settings['behavior_of_out_of_stock'] : 'default';
		$custom_css = '';

	        if(isset($attr_behavior)){
	        	if($attr_behavior == 'blur'){
	        		$custom_css .= ".thwvsf-wrapper-ul .thwvsf-wrapper-item-li.deactive {
	        			opacity : 0.3;	
	        		}

	        		.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.deactive::after, .thwvsf-wrapper-ul .thwvsf-wrapper-item-li.deactive::before {
	        				height: 0px;
	        		} ";
	        	}else if($attr_behavior == 'hide'){
	        		$custom_css .= ".thwvsf-wrapper-ul .thwvsf-wrapper-item-li.deactive {
	        			display: none;	
	        		}";
	        	}
	        }

	        if(isset($behavior_of_out_of_stock)){
	        	if($behavior_of_out_of_stock == 'blur'){
	        		$custom_css .= ".thwvsf-wrapper-ul .thwvsf-wrapper-item-li.out_of_stock {
	        			opacity : 0.3;	
	        		}
	        		.thwvsf-wrapper-ul .thwvsf-wrapper-item-li.out_of_stock::after, .thwvsf-wrapper-ul .thwvsf-wrapper-item-li.out_of_stock::before {
	        				height: 0px;
	        		}
	        		";
	        	}
	        }

		return $custom_css;
	}

	private function enqueue_scripts($suffix, $jquery_version, $is_quick_view) {
		$deps = array();
		$deps = array('jquery', 'wc-add-to-cart-variation');	
		if(is_plugin_active('woocommerce-product-bundles/woocommerce-product-bundles.php')){
			
			$deps = array('jquery', 'wc-add-to-cart-variation','wc-add-to-cart-bundle');
		}
		wp_register_script('thwvsf-public-script', THWVSF_ASSETS_URL_PUBLIC . 'js/thwvsf-public.min.js', $deps, $this->version, true );
								
		wp_enqueue_script('thwvsf-public-script');
		
		$settings          = THWVSF_Utils::get_advanced_swatches_settings();
		$clear_on_reselect = 'yes';
		$behavior_of_out_of_stock = 'default';
		$show_selected_variation_name = '';

		if($settings && is_array($settings)){

			$clear_on_reselect        = THWVSF_Utils:: get_global_swatches_settings('clear_select', $settings);
			$behavior_of_out_of_stock = THWVSF_Utils:: get_global_swatches_settings('behavior_of_out_of_stock', $settings);
			$show_selected_variation_name  = THWVSF_Utils:: get_global_swatches_settings('show_selected_variation_name', $settings);
		}

		$wvs_var = array(
			// 'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'is_quick_view' => $is_quick_view,
			'clear_on_reselect' => apply_filters('thwvsf_clear_on_reselect', $clear_on_reselect),
			'out_of_stock' => apply_filters('thwvsf_out_of_stock', $behavior_of_out_of_stock),
			'show_selected_variation_name' => $show_selected_variation_name,
 		);

		wp_localize_script('thwvsf-public-script', 'thwvsf_public_var', $wvs_var);
	}
	
	public function define_public_hooks(){

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'swatches_display' ), 100, 2 );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'add_class_for_attribute_type' ), 101,1);
		add_filter( 'woocommerce_reset_variations_link',array($this, 'reset_variation_link') );
		add_filter('woocommerce_ajax_variation_threshold',array($this,'change_ajax_variation_threshold'),10,2);
	}

	public function reset_variation_link($link){

		$custom_reset = apply_filters('thwvsf_reset_variations_link',false);
		if($custom_reset){
			$link = '<a class="reset_variations thwvsf-variation-link" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>';
		}
		return $link;
	}

	public function change_ajax_variation_threshold($threshold_value,$product){

		$threshold = THWVSF_Utils:: get_global_swatches_settings('ajax_variation_threshold');
		
		if($threshold  && is_numeric($threshold)){
			$threshold_value = $threshold;
		}

		return $threshold_value;
	}

	public function get_attribute_fields($attribute, $product){
		if(taxonomy_exists($attribute)){
			$attribute_taxonomies = wc_get_attribute_taxonomies();

	        foreach ($attribute_taxonomies as $tax) {
	            if('pa_'.$tax->attribute_name == $attribute){
	                return($tax->attribute_type);
	                break;
	            }
	        }
	    }else{
	    	$product_id = $product->get_id();
	    	$attribute  = sanitize_title($attribute);
			$local_attr_settings = get_post_meta($product_id,'th_custom_attribute_settings', true);
			if(is_array($local_attr_settings) && isset($local_attr_settings[$attribute])){
				$settings = $local_attr_settings[$attribute];
				$type = isset($settings['type']) ? $settings['type'] : '';
				return $type;
			}

			return '';
	    }
	}

	public function get_attributes_display_design($attribute,$product){

		if(taxonomy_exists($attribute)){

			$attr_id     = $this->get_attribute_id($attribute);
			$design_type = THWVSF_Utils:: get_design_swatches_settings($attr_id);
			return  $design_type ? $design_type : 'swatch_design_default' ;

	    }else{
	    	$product_id          = $product->get_id();
			$local_attr_settings = get_post_meta($product_id,'th_custom_attribute_settings', true);
			$attribute  = sanitize_title($attribute);
			if(is_array($local_attr_settings) && isset($local_attr_settings[$attribute])){
				$settings = $local_attr_settings[$attribute];
				$type     = isset($settings['design_type']) ? $settings['design_type'] : 'swatch_design_default' ;
				return $type ? $type : 'swatch_design_default' ;
			}

			return 'swatch_design_default';
	    }
	}

	public function get_attribute_id($taxonomy){

		$attribute_taxonomies = wc_get_attribute_taxonomies();
        foreach ($attribute_taxonomies as $tax) {
            if('pa_'.$tax->attribute_name == $taxonomy){
                return($tax->attribute_id);
                break;
            }
        }
	}

	public function add_class_for_attribute_type($args){
		$args['class'] = 'thwvs-select';
		return $args;
	}
	
	public function swatches_display($html, $args){
		
		if(apply_filters('thwvsf_enable_swatches_display', true)) {
			global $product;
			$attribute   = $args['attribute'];
			$type        = $this->get_attribute_fields($attribute, $product);
			$design_type = $this->get_attributes_display_design($attribute, $product);

			$auto_convert = '';
			$apply_auto_convert = false;
		
			if($type === 'select' || $type == null){
				$auto_convert = THWVSF_Utils::get_global_swatches_settings('auto_convert');
				if($auto_convert === 'yes'){
					
					$type = 'label';
					$apply_auto_convert = true;
				}else{
					$html = $this->wrapp_variation_in_class($html);
					return $html;
				}
			}

			$swatch_types = array('color','image','label', 'radio');

			if(in_array($type, $swatch_types)){

				$html = '';
				$attr_type_html = '';
			
				$attr_type_html .= $this->swatch_display_options_html($html, $args, $type, $design_type, $apply_auto_convert);
			
			}else{
				return $html;
			}

			$html = $attr_type_html;
			$html = $this->wrapp_variation_in_class($html);
		}
		return $html;
	}

	public function wrapp_variation_in_class($html){
		$html = '<div class="thwvsf_fields"> '. $html .' </div>';
		return $html;
	}

	public function swatch_display_options_html($html, $args, $type, $design_type, $apply_auto_convert){

		$html       = $this->default_variation_field($html,$args,$type,$design_type);
		$options    = $args['options'];
		$product    = $args['product'];
		$attribute  = $args['attribute'];
		$name       = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id         = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$product_id = $product->get_id(); 

		$is_wpml_active  = $this->is_wpml_active();
		
		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		if ( ! empty( $options ) ) {

			if($product){

				$terms = wc_get_product_terms( $product->get_id(), $attribute, array('fields' => 'all',) );
				$terms = taxonomy_exists( $attribute ) ? $terms  : $options;
				$local_attr_settings = get_post_meta($product_id ,'th_custom_attribute_settings', true);
				$local_settings = isset($local_attr_settings[$id]) ?  $local_attr_settings[$id] : '';
				
				$tt_html        = '';
				$tooltip_type   = '';
				$tt_design_type = '';
				
				$settings = THWVSF_Utils::get_advanced_swatches_settings();
				
				if(is_array($settings)){

					$tt_design_type = $design_type;
					if(isset($settings[$tt_design_type])){
						$settings_value = $settings[$tt_design_type];
						$tooltip_type = is_array($settings_value) &&  isset($settings_value['tooltip_enable']) ? $settings_value['tooltip_enable'] : '';
					}else{
						$tooltip_type =  isset($settings['tooltip_enable']) ? $settings['tooltip_enable'] : '';
					}
				}
				$design_class    = 'attr_'.$design_type;
				if($type == 'radio'){

					$html  .= $this->add_radio_display($id, $name, $attribute, $terms, $options, $design_class, $local_settings, $args);
					
				}else{

					$html .= '<ul class="thwvsf-wrapper-ul">';

					foreach ( $terms as $term ) {

						$term_status = false;
						$name = '';
						$slug = '';
						$selected ='';
						$attr_method = '';

						if(taxonomy_exists( $attribute )){
							$term_status = false;
							$attr_method = 'global';
							if ( in_array( $term->slug, $options, true ) ) {
								$term_status = true;
								$name     = apply_filters( 'woocommerce_variation_option_name', $term->name );
								$slug = $term->slug;
								$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'thwvsf-selected' : '';
								//$label = get_term_meta( $term->term_id, 'label', true );
								//$label = $label ? $label : $name;
							}

						}else{

							$term_status = true;
							$name = $term;
							$slug = $name;
							$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title($term), false ) : selected( $args['selected'], $term, false );
							$selected = $selected ? 'thwvsf-selected' : '';
							$attr_method = 'local';

						}

						$attr_class      = preg_replace('/[^A-Za-z0-9\-\_]/', '', $slug);
						$data_val        = $slug;
						$tt_design_class = 'tooltip_'.$design_type;
						$tt_html = '';

						if($tooltip_type === 'yes'){
		                 	$tt_html = '<span class="tooltiptext tooltip_'. esc_attr( $id ).' '. esc_attr($tt_design_class ).'">'. esc_html( $name) .'</span>' ;
		                }

		                if($term_status){

			                switch ($type) {
			                	case 'color':
			               		$html .=  $this->add_color_display($id,$name,$attribute,$term,$attr_class,$selected,$data_val,$tt_html,$design_class,$attr_method,$local_settings, $is_wpml_active);
			            		break;
					            case 'image':
					                $html .= $this->add_image_display($id,$name,$attribute,$term,$attr_class,$selected,$data_val,$tt_html,$design_class,$attr_method,$local_settings, $is_wpml_active);
					            break;
					            case 'label' : 
					            	$html .= $this->add_label_display($id,$name,$attribute,$term,$attr_class,$selected,$data_val,$tt_html,$design_class,$attr_method,$local_settings, $apply_auto_convert, $is_wpml_active);
					            break;
			                }
			            }

					}

					$html  .= '</ul>';
				}
			}
		}

		return $html;
	}


	public function add_color_display($id, $name, $attribute, $term, $attr_class, $selected, $data_val, $tt_html, $design_class, $attr_method, $local_settings, $is_wpml_active = false){

		if($attr_method === 'global'){

			$color = get_term_meta( $term->term_id,'product_'.$attribute, true);
			if($is_wpml_active && !$color  ){
				$default_lang = $this->get_default_language();
				$dterm_id     = icl_object_id($term->term_id, $attribute, true, $default_lang);
				$color        = get_term_meta( $dterm_id,'product_'.$attribute, true );
			}
		}else{

			$term_settings = isset($local_settings[$name]) ? $local_settings[$name] : '' ;
			$color         = !empty($term_settings) &&  isset($term_settings['term_value']) ? $term_settings['term_value'] : '';
		}
       		
		$html = '
			<li class="thwvsf-wrapper-item-li thwvsf-color-li thwvsf-div thwvsf-checkbox attribute_'.esc_attr($id).' '. esc_attr($attr_class).' '.esc_attr($selected).' '.esc_attr($design_class).' thwvsf-tooltip" data-attribute_name="attribute_'.esc_attr($id).'" data-value="'.esc_attr($data_val).'" title="'.esc_attr($name).'">'.$tt_html.
				'<span class="thwvsf-item-span thwvsf-item-span-color" style="background-color:'.esc_attr( $color).';"> </span>
			</li>';

		return $html;
	}

	public function add_image_display($id, $name, $attribute, $term, $attr_class, $selected, $data_val, $tt_html, $design_class, $attr_method, $local_settings, $is_wpml_active = false){

		if($attr_method == 'global'){
			$value = get_term_meta( $term->term_id,'product_'.$attribute, true );

			if($is_wpml_active && !$value ){
				$default_lang = $this->get_default_language();
				$dterm_id     = icl_object_id($term->term_id, $attribute, true, $default_lang);
				$value        = get_term_meta( $dterm_id,'product_'.$attribute, true );
			}
			$image = $value ? wp_get_attachment_image_src( $value ) : '';
	    	$image = $image ? $image[0] : THWVSF_URL . 'admin/assets/images/placeholder.png';
	    }else{

	    	$term_settings = isset($local_settings[$name]) ? $local_settings[$name] : '' ;
	    	$value = isset($term_settings['term_value']) ? $term_settings['term_value'] : '';
        	$image = $value ? wp_get_attachment_image_src( $value ) : '';
	        $image = $image ? $image[0] : THWVSF_URL . 'admin/assets/images/placeholder.png';
	    }

	    $html = '<li class="thwvsf-wrapper-item-li thwvsf-image-li thwvsf-div thwvsf-checkbox attribute_'.esc_attr( $id ).' '. esc_attr( $attr_class ).' '. esc_attr( $design_class ).' '.esc_attr( $selected).' thwvsf-tooltip" data-attribute_name="attribute_'.esc_attr( $id).'" data-value="'.esc_attr( $data_val).'" title="'.esc_attr( $name).'" >'.$tt_html.'<img class="swatch-preview swatch-image "  src="'.esc_url($image).' " width="44px" height="44px" alt="'.esc_attr($name).'"></li>';	

		return $html;
	}

	public function add_label_display($id, $name, $attribute, $term, $attr_class, $selected, $data_val, $tt_html, $design_class, $attr_method, $local_settings, $apply_auto_convert, $is_wpml_active = false){

		$value = '';
		if($apply_auto_convert){
			$value = $name;
		}else{
			if($attr_method == 'global'){
				$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$value = get_term_meta( $term->term_id,'product_'.$attribute, true );
				if($is_wpml_active && !$value){
					$default_lang = $this->get_default_language();
					$dterm_id     = icl_object_id($term->term_id, $attribute, true, $default_lang);
					$value        = get_term_meta( $dterm_id,'product_'.$attribute, true );
				}
				$value  = empty($value) ? $name : $value;	

			}else{

				$term_settings = isset($local_settings[$name]) ? $local_settings[$name] : '';
				$value = $term_settings && isset($term_settings['term_value']) ? $term_settings['term_value'] : '';
				$value = empty($value) && isset($term_settings['name']) ? $term_settings['name'] : $value;
				$value = empty($value) &&  $term  ? $term : $value;
			}
		}

		$html = '<li class="thwvsf-wrapper-item-li thwvsf-label-li thwvsf-div thwvsf-checkbox attribute_'.esc_attr($id).' '. esc_attr($attr_class).' '.esc_attr($design_class).' '.esc_attr($selected).' thwvsf-tooltip" data-attribute_name="attribute_'.esc_attr($id).'" data-value="'.esc_attr($data_val).'" title="'.esc_attr($name).'">
				'.$tt_html.'
			<span class=" thwvsf-item-span item-span-text ">'.esc_html($value).'</span>	
			</li>';

		return $html; 
	}

	public function add_radio_display($id, $name, $attribute, $terms, $options, $design_class, $local_settings, $args){

		//$attr_method = taxonomy_exists( $attribute ) ? 'global'  : 'local';
		$html  = '';
		$html .= '<div class="thwvsf-rad-li attribute_'. $id .' '.$design_class.' ">';

		if($terms){

			foreach ( $terms as $term ) {
				$name = '';
				$slug = '';
				$label = '';
				$selected = '';

				if(taxonomy_exists( $attribute )){
					if ( in_array( $term->slug, $options, true ) ) {
						$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'thwvs-selected' : '';
						$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
						$label    = get_term_meta( $term->term_id, 'label', true );
						$label    = $label ? $label : $name;
						$slug     = $term->slug;
					}
				}else{
					$selected  = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title($term), false ) : selected( $args['selected'], $term, false );
					$selected  = $selected ? 'thwvs-selected' : '';
					if($local_settings && $local_settings != ''){
						
						$name  = ($term);
    					$slug  = $name;
    					$label = $name;
					} 
				}

				$attr_class   = preg_replace('/[^A-Za-z0-9\-\_]/', '', $slug);

				$checked = $selected == 'thwvs-selected' ? 'checked="checked"' : '';
				$html  .='
					<label class="th-label-radio th-container attribute_'.esc_attr($id).' '. esc_attr($attr_class) .' '. esc_attr($selected) .' '.esc_attr($design_class).'">
						<span class="th-radio-name">
							<span class="variation-name">'.esc_html($name).'</span>
						</span>
						<input type="radio" class="thwvsf-rad"   name="attribute_'.esc_attr($id).'"  value="'.esc_attr($slug).'"  data-attribute_name="attribute_'.esc_attr($id).'" data-value="'.esc_attr($slug).'" '.$checked.'> 
						<span class="checkmark"></span>
					</label>'
				;
			}

			$html  .= '</div>';
		}

		return $html;
	}

	public function default_variation_field($html,$args,$attr,$design_type){
		$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		) );

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-design_type="'.esc_attr($design_type).'" style="display:none" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '" >';
		$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
		
		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array(
					'fields' => 'all',
				) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
				}
			}
		}

		$html .= '</select>';
		return $html;
	}

	public function is_wpml_active(){
		global $sitepress;
		return function_exists('icl_object_id') && is_object($sitepress);
	}

	public function get_default_language(){
		global $sitepress;
		global $icl_adjust_id_url_filter_off;
		$orig_flag_value = $icl_adjust_id_url_filter_off;
		$icl_adjust_id_url_filter_off = true;
		$default_lang = $sitepress->get_default_language();
		return $default_lang;
	}

}

endif;

