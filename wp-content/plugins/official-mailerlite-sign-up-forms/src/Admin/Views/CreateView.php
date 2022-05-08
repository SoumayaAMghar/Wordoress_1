<?php

namespace MailerLiteForms\Admin\Views;

use MailerLiteForms\Models\MailerLiteWebForm;

class CreateView
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($webforms)
    {
        $this->view($webforms);
    }

    /**
     * Output view
     *
     * @access      private
     * @return      void
     * @since       1.5.0
     */
    private function view($webforms)
    {

        ?>

        <div class="wrap columns-2 dd-wrap">
            <h1><?php echo __( 'Create new signup form', 'mailerlite' ); ?></h1>
            <h2 class="title"><?php echo __( 'Form type', 'mailerlite' ); ?></h2>

            <div class="metabox-holder has-right-sidebar">
                <?php new SidebarView(); ?>
                <div id="post-body">
                    <div id="post-body-content">

                        <form action="<?php echo admin_url( 'admin.php?page=mailerlite_main&view=create&noheader=true' ); ?>"
                              method="post">
                            <div class="inside">

                                <div class="mailerlite-list">

                                    <div class="plugin-card">
                                        <div class="plugin-card-top">

                                            <label for="form_type_custom" class="selectit">
                                                <input id="form_type_custom" type="radio" name="form_type" value="1"
                                                       onclick="jQuery('#expl').addClass('hidden')"
                                                       checked="checked">
                                                <?php echo __( 'Custom signup form', 'mailerlite' ); ?>
                                                <p>
                                                    <img class="mailerlite-icon"
                                                         src="<?php echo MAILERLITE_PLUGIN_URL ?>/assets/image/custom_form.png">
                                                </p>
                                                <p class="description">
                                                    <?php _e( 'Create a custom form with different fields and interest groups directly in WordPress.',
                                                        'mailerlite' ); ?>
                                                </p>
                                            </label>

                                        </div>
                                    </div>
                                    <div class="plugin-card">
                                        <div class="plugin-card-top">

                                            <?php
                                            $embed_button_webforms = [];

                                            /** @var MailerLiteWebForm[] $webforms */
                                            if ( count( $webforms ) ) {
                                                foreach ( $webforms as $webform ) {
                                                    if ( ! in_array( $webform->type, [ 'embed', 'embedded', 'button' ] ) ) {
                                                        continue;
                                                    }

                                                    $embed_button_webforms[] = $webform;
                                                }
                                            }
                                            ?>
                                            <label for="form_type_webform" class="selectit<?php echo count( $embed_button_webforms ) == 0 ? ' ml_unavailable' : '' ?>">
                                                <input id="form_type_webform" type="radio" name="form_type"
                                                       onclick="jQuery('#expl').removeClass('hidden')"
                                                       value="2"<?php echo count( $embed_button_webforms ) == 0 ? ' disabled="disabled"' : ''; ?>>
                                                <?php echo __( 'Forms created in MailerLite', 'mailerlite' ); ?>
                                                <p>
                                                    <img class="mailerlite-icon"
                                                         src="<?php echo MAILERLITE_PLUGIN_URL ?>/assets/image/mailerlite_form.png">
                                                </p>
                                                <p class="description">
                                                    <?php _e( 'Add signup forms from your MailerLite account.',
                                                        'mailerlite' ); ?>
                                                </p>
                                            </label>

                                        </div>
                                    </div>

                                    <div class="clear"></div>

                                </div>

                                <p id="expl" class="hidden info notice notice-info">
                                    <?php echo __( 'Explanation about forms', 'mailerlite' ); ?>
                                </p>

                                <div class="submit">
                                    <input class="button-primary"
                                           value="<?php echo __( 'Create form', 'mailerlite' ); ?>" name="create_signup_form"
                                           type="submit">
                                    <a class="button-secondary"
                                       href="<?php echo admin_url( 'admin.php?page=mailerlite_main' ); ?>"><?php echo __( 'Back',
                                            'mailerlite' ); ?></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}