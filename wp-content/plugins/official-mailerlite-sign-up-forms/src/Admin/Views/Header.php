<?php
namespace MailerLiteForms\Admin\Views;

class Header
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($mailerlite_error)
    {

        $this->view($mailerlite_error);
    }

    /**
     * Output view
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function view($mailerlite_error)
    {

        if ($mailerlite_error != '') {
        ?>

            <div class="error">
                <p><?php echo $mailerlite_error; ?></p>
            </div>

        <?php
        }
    }
}