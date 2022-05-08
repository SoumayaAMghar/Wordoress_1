<?php
namespace MailerLiteForms\Admin\Views;

use MailerLiteForms\Helper;

class SidebarView
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct()
    {

        $this->view();
    }

    /**
     * Output view
     *
     * @access      private
     * @return      void
     * @since       1.5.0
     */
    private function view()
    {

        ?>
        <div id="side-info-column" class="inner-sidebar">

            <div class="postbox">
                <h3><?php _e( 'Need help?', 'mailerlite' ); ?></h3>

                <div class="inside">
                    <p><?php _e( 'Have any questions? Stuck on something or found bug? Feel free to contact us!',
                            'mailerlite' ); ?></p>

                    <p>
                        <a href="mailto:info@mailerlite.com?subject=MailerLite - Signup forms (official) for WordPress&body=<?php echo ( new Helper() )->getEmailBody(); ?>">info@mailerlite.com</a>
                    </p>
                </div>
            </div>

        </div>
        <?php
    }
}