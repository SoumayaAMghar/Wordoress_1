<?php
//about theme info
add_action( 'admin_menu', 'palm_healing_lite_abouttheme' );
function palm_healing_lite_abouttheme() {    	
	add_theme_page( esc_html__('About Theme', 'palm-healing-lite'), esc_html__('About Theme', 'palm-healing-lite'), 'edit_theme_options', 'palm_healing_lite_guide', 'palm_healing_lite_mostrar_guide');   
} 
//guidline for about theme
function palm_healing_lite_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
?>
<div class="wrapper-info">
	<div class="col-left">
   		   <div class="col-left-area">
			  <?php esc_html_e('Theme Information', 'palm-healing-lite'); ?>
		   </div>
          <p><?php esc_html_e('Palm Healing Lite is a massage beauty salon and spa related website template. It can be used for health care, manicure, pedicure, health clubs, essential oils business, after-cure, facial, fat farm, hot spring, ayurveda, hairdresser girly, barber, therapist, treatment, depression, stress, nail salon, hair, Hydro cure, medical parlor, wellness center, aroma therapy, physiotherapy, reiki, meditation, yoga, fitness among other businesses. Coded using Elementor is compatible with a lot of popular plugins for shop, SEO, contact forms and others.','palm-healing-lite'); ?></p>
          <a href="<?php echo esc_url(PALM_HEALING_LITE_SKTTHEMES_PRO_THEME_URL); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/free-vs-pro.png" alt="" /></a>
	</div><!-- .col-left -->
	<div class="col-right">			
			<div class="centerbold">
				<hr />
				<a href="<?php echo esc_url(PALM_HEALING_LITE_SKTTHEMES_LIVE_DEMO); ?>" target="_blank"><?php esc_html_e('Live Demo', 'palm-healing-lite'); ?></a> | 
				<a href="<?php echo esc_url(PALM_HEALING_LITE_SKTTHEMES_PRO_THEME_URL); ?>"><?php esc_html_e('Buy Pro', 'palm-healing-lite'); ?></a> | 
				<a href="<?php echo esc_url(PALM_HEALING_LITE_SKTTHEMES_THEME_DOC); ?>" target="_blank"><?php esc_html_e('Documentation', 'palm-healing-lite'); ?></a>
                <div class="space5"></div>
				<hr />                
                <a href="<?php echo esc_url(PALM_HEALING_LITE_SKTTHEMES_THEMES); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/sktskill.jpg" alt="" /></a>
			</div>		
	</div><!-- .col-right -->
</div><!-- .wrapper-info -->
<?php } ?>