<?php

namespace MailerLiteForms\Views;

use MailerLiteForms\Api\ApiType;

class EmbeddedForm
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($form_data, $platform)
    {

        $this->view($form_data, $platform);
    }

    /**
     * Output view
     *
     * @access      private
     * @since       1.5.0
     */
    private function view($form_data, $platform)
    {

        if (get_option('account_id') && get_option('account_subdomain') && $platform == ApiType::CURRENT) {

            ?>
                <div class="ml-form-embed"
                     data-account="<?php echo get_option('account_id') . ':' . get_option('account_subdomain'); ?>"
                     data-form="<?php echo $form_data['id'] . ':' . $form_data['code']; ?>">
                </div>
            <?php
        }

        if (get_option('account_id') && $platform == ApiType::REWRITE) {
            ?>
                <div class="ml-embedded" data-form="<?php echo $form_data['code']; ?>"></div>
            <?php
        }

        if (!get_option('account_id') && !get_option('account_subdomain') && $platform == ApiType::CURRENT) {
            ?>
                <script type="text/javascript" src="https://static.mailerlite.com/data/webforms/<?php echo $form_data['id']; ?>/<?php echo $form_data['code']; ?>.js?v=<?php echo time(); ?>"></script>
            <?php
        }
    }
}