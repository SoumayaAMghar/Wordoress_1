<?php

namespace MailerLiteForms\Views;

class InvalidForm
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($form_id)
    {

        $this->view($form_id);
    }

    /**
     * Output view
     *
     * @access      private
     * @since       1.5.0
     */
    private function view($form_id)
    {

        ?>
        <div id="mailerlite-form_<?php echo $form_id; ?>" data-temp-id="<?php echo uniqid(); ?>">
            <div class="mailerlite-form">
                <p>The form you have selected does not exist.</p>
            </div>
        </div>
        <?php
    }
}