<?php

namespace MailerLiteForms\Views;

class Iframe
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($url)
    {

        $this->view($url);
    }

    /**
     * Output view
     *
     * @access      private
     * @since       1.5.0
     */
    private function view($url)
    {
        
        ?>

        <div style='position: relative;'>
            <div style='position: absolute;top:0;left:0;width:100%;height:100%;'></div>
            <iframe style='z-index:1;' src='<?php echo $url ?>'
                    onload="mlResizeIframe(this)"></iframe>
        </div>

        <?php
    }
}