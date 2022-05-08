<?php

namespace MailerLiteForms;

use MailerLiteForms\Admin\Status;

class Helper
{
    public static $languages = [
        ''   => '-Default (English)-',
        'ar' => 'Arabic',
        'bg' => 'Bulgarian',
        'ca' => 'Catalan',
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'es' => 'Spanish',
        'eu' => 'Basque',
        'fa' => 'Farsi',
        'fi' => 'Finnish',
        'fr' => 'French',
        'he' => 'Hebrew',
        'hu' => 'Hungarian',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'kk' => 'Kazakh',
        'lt' => 'Lietuvių',
        'lv' => 'Latvian',
        'nl' => 'Dutch',
        'no' => 'Norwegian',
        'pl' => 'Polish',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'si' => 'Sinhala',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'th' => 'Thai',
        'vi' => 'Vietnamese',

    ];

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct()
    {

    }

    /**
     * Get settings view for email
     *
     * @access      public
     * @return      string
     */
    public function getEmailBody()
    {

        $data = ( new Status() )->getInformation();

        $body = "\n\n\n";

        $body .= "MailerLite - Signup forms (official) information: \n\n";

        foreach ( $data as $group => $fields ) {
            $body .= sprintf( "# %s \n\n", $group );

            foreach ( $fields as $name => $value ) {
                $body .= sprintf( "%s: %s\n", $name, $value );
            }

            $body .= "\n";
        }

        $body = str_replace( "\n", '%0A', $body );

        return $body;
    }

    /**
     * Universal tracking tag
     *
     * @access      public
     * @return      void
     */
    public static function mailerlite_universal()
    {

        ?>
            <!-- MailerLite Universal -->
            <script>
                (function (m, a, i, l, e, r) {
                    m['MailerLiteObject'] = e;

                    function f() {
                        var c = {a: arguments, q: []};
                        var r = this.push(c);
                        return "number" != typeof r ? r : f.bind(c.q);
                    }

                    f.q = f.q || [];
                    m[e] = m[e] || f.bind(f.q);
                    m[e].q = m[e].q || f.q;
                    r = a.createElement(i);
                    var _ = a.getElementsByTagName(i)[0];
                    r.async = 1;
                    r.src = l + '?' + (~~(new Date().getTime() / 10000000));
                    _.parentNode.insertBefore(r, _);
                })(window, document, 'script', 'https://static.mailerlite.com/js/universal.js', 'ml');

                var ml_account = ml('accounts', '<?php echo get_option( 'account_id' ); ?>', '<?php echo get_option( 'account_subdomain' ); ?>', 'load');
            </script>
            <!-- End MailerLite Universal -->
        <?php
    }

    /**
     * Universal tracking tag for Rewrite
     *
     * @access      public
     * @return      void
     */
    public static function mailerlite_universal_rw()
    {

        $mailerlite_popups = ! ((get_option('mailerlite_popups_disabled') == '1'));

        if ( is_admin() ) {

            $mailerlite_popups = false;
        }
        ?>
        <!-- MailerLite Universal -->
        <script>
            (function(w,d,e,u,f,l,n){w[f]=w[f]||function(){(w[f].q=w[f].q||[])
                .push(arguments);},l=d.createElement(e),l.async=1,l.src=u,
                n=d.getElementsByTagName(e)[0],n.parentNode.insertBefore(l,n);})
            (window,document,'script','https://assets.mailerlite.com/js/universal.js','ml');
            ml('account', '<?php echo get_option('account_id'); ?>');
            ml('enablePopups', <?php echo $mailerlite_popups ? 'true' : 'false'; ?>);
        </script>
        <!-- End MailerLite Universal -->
        <?php
    }

    /**
     * Helper to reuse input field with default data
     *
     * @param string $post_key
     * @param string $default
     * @param bool   $sanitize
     *
     * @return string
     */
    public static function issetWithDefault( $post_key, $default = '', $sanitize = true ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            if ( $sanitize ) {
                return sanitize_text_field( $_POST[ $post_key ] );
            }

            return $_POST[ $post_key ];
        }

        return $default;
    }
    
}