<?php
/**
 * The Customizer Controls File.
 *
 * @package Travel_Log
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'Travel_Log_Dropdown_Taxonomies_Control' ) ) :
	/**
	 * Customize Control for Taxonomy Select.
	 *
	 * @since 1.0.0
	 */
	class Travel_Log_Dropdown_Taxonomies_Control extends WP_Customize_Control {

		/**
		 * Type of control.
		 *
		 * @var string
		 */
		public $type = 'dropdown-taxonomies';

		/**
		 * Taxonomy to list.
		 *
		 * @var string
		 */
		public $taxonomy = '';

		/**
		 * Check if multiple.
		 *
		 * @var bool
		 */
		public $multiple = false;

		/**
		 * Constructor.
		 *
		 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @param string               $id      Control ID.
		 * @param array                $args    Optional. Arguments to override class property defaults.
		 */
		public function __construct( $manager, $id, $args = array() ) {

			$taxonomy = 'category';
			if ( isset( $args['taxonomy'] ) ) {
				$taxonomy_exist = taxonomy_exists( esc_attr( $args['taxonomy'] ) );
				if ( true === $taxonomy_exist ) {
					$taxonomy = esc_attr( $args['taxonomy'] );
				}
			}
			$args['taxonomy'] = $taxonomy;
			$this->taxonomy = esc_attr( $taxonomy );

			if ( isset( $args['multiple'] ) ) {
				$this->multiple = ( true === $args['multiple'] ) ? true : false;
			}

			parent::__construct( $manager, $id, $args );
		}

		/**
		 * Render content.
		 */
		public function render_content() {

			$tax_args = array(
			'hierarchical' => 0,
			'taxonomy'     => $this->taxonomy,
			);
			$all_taxonomies = get_categories( $tax_args );
			$multiple_text = ( true === $this->multiple ) ? 'multiple' : '';
			$value = $this->value();
			?>
			<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<select <?php $this->link(); ?> <?php echo esc_attr( $multiple_text ); ?>>
				<?php
				$selected = '';
				if ( is_array( $value ) ) {
					if ( in_array( '', $value ) )
						$selected = 'selected="selected"';
				} else {
					$selected = selected( $value, '', false );
				}
				printf( '<option value="%s" %s>%s</option>', '', esc_attr( $selected ), esc_html__( '&mdash; All &mdash;', 'travel-log' ) );
				?>
				<?php if ( ! empty( $all_taxonomies ) ) : ?>
					<?php foreach ( $all_taxonomies as $key => $tax ) :
						$selected = '';
						if ( is_array( $value ) ) {
							if ( in_array( $tax->term_id, $value ) )
								$selected = 'selected="selected"';
						} else {
							$selected = selected( $value, $tax->term_id, false );
						}
						printf( '<option value="%s" %s>%s</option>', esc_attr( $tax->term_id ), esc_attr( $selected ), esc_html( $tax->name ) );
						?>
					<?php endforeach; ?>
				<?php endif; ?>
				</select>
			</label>
			<?php
		}
	}

endif;

if ( ! class_exists( 'Travel_Log_Custom_Radio_Image_Control' ) ) :

	/**
	 * The radio image class.
	 *
	 * @since 1.0.0
	 */
	class Travel_Log_Custom_Radio_Image_Control extends WP_Customize_Control {

		/**
		 * Declare the control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'radio-image';

		/**
		 * Enqueue scripts and styles for the custom control.
		 *
		 * Scripts are hooked at {@see 'customize_controls_enqueue_scripts'}.
		 *
		 * Note, you can also enqueue stylesheets here as well. Stylesheets are hooked
		 * at 'customize_controls_print_styles'.
		 *
		 * @access public
		 */
		public function enqueue() {

			wp_enqueue_script( 'jquery-ui-button' );
		}

		/**
		 * Render the control to be displayed in the Customizer.
		 */
		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}

			$name = '_customize-radio-' . sanitize_title( $this->id ); ?>

			<span class="customize-control-title">
				<?php echo esc_attr( $this->label ); ?>
			</span>

			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

			<div id="input_<?php echo sanitize_title( esc_attr( $this->id ) ); ?>" class="image">
				<?php foreach ( $this->choices as $key => $value ) : ?>
				<input class="image-select" type="radio" value="<?php echo esc_attr( $key ); ?>" id="<?php echo sanitize_title( esc_attr( $this->id . $key ) ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link();
				checked( $this->value(), $key ); ?>>
					<label for="<?php echo sanitize_title( esc_attr( $this->id ) ) . esc_attr( $key ); ?>">
						<img src="<?php echo esc_html( $value['image'] ); ?>" alt="<?php echo esc_attr( $value['label'] ); ?>" title="<?php echo esc_attr( $value['label'] ); ?>">
					</label>
				</input>
			<?php endforeach; ?>
			</div>

			<?php
		}

	}

endif;

if ( ! class_exists( 'Travel_Log_Custom_Section_Separator_Control' ) ) :

	/**
	 * The radio image class.
	 *
	 * @since 1.0.0
	 */
	class Travel_Log_Custom_Section_Separator_Control extends WP_Customize_Control {

		/**
		 * Declare the control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'separator-heading';

		/**
		 * Render the control to be displayed in the Customizer.
		 */
		public function render_content() {
			?>
				<h2 class="sep-title"><?php echo esc_html( $this->label ); ?></h2>
				<hr>

			<?php
		}

	}

endif;


if ( ! function_exists( 'travel_log_customizer_content_type_choices' ) ) :
	/**
	 * [travel_log_customizer_content_type_choices description]
	 *
	 * @return [array] [available choices]
	 *
	 * @since 1.0.7
	 */
	function travel_log_customizer_content_type_choices() {

		$choices = array(

		'category' => esc_html__( 'Posts Categories', 'travel-log' ),
			);

		if ( class_exists( 'WP_Travel' ) ) :

			$travel_cat = array(

				'trip-types' => esc_html__( 'Trip Types', 'travel-log' ),
				'trip-location' => esc_html__( 'Trip Locations', 'travel-log' ),

				);

			$choices = array_merge( $choices, $travel_cat );

			endif;

			return $choices;

	}

endif;



if ( ! function_exists( 'travel_log_customizer_recommended_content_type_choices' ) ) :
	/**
	 * [travel_log_customizer_content_type_choices description]
	 *
	 * @return [array] [available choices]
	 *
	 * @since 1.0.7
	 */
	function travel_log_customizer_recommended_content_type_choices() {

		$choices = array(

		'category' => esc_html__( 'Posts Categories', 'travel-log' ),
			);

		if ( class_exists( 'WP_Travel' ) ) :

			$travel_cat = array(

				'recommended-trips' => esc_html__( 'Recommended Trips', 'travel-log' ),

				);

			$choices = array_merge( $choices, $travel_cat );

			endif;

			return $choices;

	}

endif;




if ( ! function_exists( 'travel_log_customizer_testimonials_secttion_content_type_choices' ) ) :
	/**
	 * [travel_log_customizer_testimonials_secttion_content_type_choices description]
	 *
	 * @return [array] [available choices]
	 *
	 * @since 1.0.8
	 */
	function travel_log_customizer_testimonials_secttion_content_type_choices() {

		$choices = array(

			'post' => esc_html__( 'Post category', 'travel-log' ),
		);

		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'custom-content-types' ) ) :

			$jetpack_option = array(

				'jetpack-testimonials' => esc_html__( 'Jetpack Testimonials', 'travel-log' ),

			);

			$choices = array_merge( $choices, $jetpack_option );

		endif;

		return $choices;

	}

	endif;
