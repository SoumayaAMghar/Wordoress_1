<?php
namespace MailerLiteForms\Admin\Views;

use MailerLiteForms\Api\ApiType;
use MailerLiteForms\Api\PlatformAPI;
use MailerLiteForms\Controllers\AdminController;

class SettingsView
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($api_key)
    {

        $this->view($api_key);
    }

    /**
     * Output view
     *
     * @access      private
     * @return      void
     * @since       1.5.0
     */
    private function view($api_key)
    {

        ?>
        <div class="wrap columns-2 dd-wrap">
            <h1><?php echo __( 'Plugin settings', 'mailerlite' ); ?></h1>

            <div class="metabox-holder has-right-sidebar">
                <?php new SidebarView(); ?>
                <div id="post-body">
                    <div id="post-body-content" class="mailerlite-activate">

                        <table class="form-table">
                            <tr>
                                <th valign="top">
                                    <label for="mailerlite-api-key"><?php echo __( 'Enter an API key',
                                            'mailerlite' ); ?></label>
                                </th>
                                <td>

                                    <form action="" method="post" id="enter-mailerlite-key">

                                        <input type="text" name="mailerlite_key" class="regular-text" placeholder="API-key"
                                               value="<?php if ( $api_key != "") { echo "....".substr($api_key, -4); } ?>" id="mailerlite-api-key"/>

                                        <input type="submit" name="submit" id="submit" class="button button-primary"
                                               value="<?php  if ( $api_key != "") { echo __( 'Update this key', 'mailerlite' ); } else { echo __( 'Save this key', 'mailerlite' ); } ?>">
                                        <input type="hidden" name="action" value="enter-mailerlite-key">

                                    </form>


                                    <p class="description"><?php

                                        switch ((new PlatformAPI( $api_key ))->getApiType()) {
                                            case ApiType::CURRENT:
                                                echo __( "MailerLite v2 API", 'mailerlite' );
                                                break;
                                            case ApiType::REWRITE:
                                                echo __( "MailerLite API (Rewrite)", 'mailerlite' );
                                                break;
                                            default:
                                                echo __( "Don't know where to find it?", 'mailerlite' );
                                                ?>
                                                <a
                                                        href="https://www.mailerlite.com/help/where-to-find-the-mailerlite-api-key-and-documentation"
                                                        target="_blank"><?php echo __( "Check it here!", 'mailerlite' ); ?></a>
                                        <?php
                                                break;
                                        }
                                        ?>
                                        </p>
                                </td>
                            </tr>

                            <?php if ( AdminController::apiKey() != '' ) : ?>
                            <tr>
                                <th valign="middle">
                                    <label><?php echo __( 'MailerLite Popups', 'mailerlite' ); ?></label>
                                </th>
                                <td>
                                    <form action="" method="post" id="mailerlite-popups">

                                        <p class="<?php if ( get_option( 'mailerlite_popups_disabled' ) ) : ?>gray<?php else: ?>success<?php endif; ?> popups">
                                            <?php if ( ! get_option( 'mailerlite_popups_disabled' ) ) : ?><?php echo __( 'Enabled',
                                                'mailerlite' ); ?><?php else: ?><?php echo __( 'Disabled',
                                                'mailerlite' ); ?><?php endif; ?>
                                        </p>

                                        <input type="submit" name="submit" id="submit" class="button button-primary"
                                               value="<?php if ( ! get_option( 'mailerlite_popups_disabled' ) ) : ?><?php echo __( 'Disable',
                                                   'mailerlite' ); ?><?php else: ?><?php echo __( 'Enable',
                                                   'mailerlite' ); ?><?php endif; ?>">
                                        <input type="hidden" name="action" value="enter-popup-forms">

                                    </form>


                                    <p class="description">
                                        <?php echo __( 'Enable or disable popup subscribe forms created within MailerLite.',
                                            'mailerlite' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php if ( AdminController::apiKey() != '' ) : ?>
                            <tr>
                                <th valign="middle">
                                    <label><?php echo __( 'Double opt-in', 'mailerlite' ); ?></label>
                                </th>
                                <td>
                                    <form action="" method="post" id="mailerlite-popups">

                                        <p class="<?php if ( get_option( 'mailerlite_double_optin_disabled' ) ) : ?>gray<?php else: ?>success<?php endif; ?> popups">
                                            <?php if ( ! get_option( 'mailerlite_double_optin_disabled' ) ) : ?><?php echo __( 'Enabled',
                                                'mailerlite' ); ?><?php else: ?><?php echo __( 'Disabled',
                                                'mailerlite' ); ?><?php endif; ?>
                                        </p>

                                        <input type="submit" name="submit" id="submit" class="button button-primary"
                                               value="<?php if ( ! get_option( 'mailerlite_double_optin_disabled' ) ) : ?><?php echo __( 'Disable',
                                                   'mailerlite' ); ?><?php else: ?><?php echo __( 'Enable',
                                                   'mailerlite' ); ?><?php endif; ?>"
                                               <?php if ( ! get_option( 'mailerlite_double_optin_disabled' ) ) { ?>onclick="return confirm('<?php _e( 'Are you sure you want to disable double opt-in?',
                                                   'mailerlite' ); ?>');"<?php } ?>>
                                        <input type="hidden" name="action" value="toggle-double-opt-in">
                                    </form>

                                    <p class="description">
                                        <?php echo __( 'Enable double opt-in for custom signup forms if you want to send confirmation emails to subscribers.',
                                            'mailerlite' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>

                        <br>
                        <hr>

                        <?php if ( $api_key === "" ) : ?>
                        <h2 class="title"><?php echo __( "Don't have an account?", 'mailerlite' ); ?></h2>

                        <a href="https://www.mailerlite.com/signup" target="_blank"
                           class="button button-secondary"><?php echo __( 'Register!', 'mailerlite' ); ?></a>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}