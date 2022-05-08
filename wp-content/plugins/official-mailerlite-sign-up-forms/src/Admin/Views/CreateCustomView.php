<?php

namespace MailerLiteForms\Admin\Views;

class CreateCustomView
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($form_name, $groups, $can_load_more_groups)
    {
        $this->view($form_name, $groups, $can_load_more_groups);
    }

    /**
     * Output view
     *
     * @access      private
     * @return      void
     * @since       1.5.0
     */
    private function view($form_name, $groups, $can_load_more_groups)
    {
        ?>

        <div class="wrap columns-2 dd-wrap">
            <h1><?php echo __( 'Create new signup form', 'mailerlite' ); ?></h1>
            <div class="metabox-holder has-right-sidebar">
                <?php new SidebarView(); ?>
                <div id="post-body">
                    <div id="post-body-content">

                        <form action="<?php echo admin_url( 'admin.php?page=mailerlite_main&view=create&noheader=true' ); ?>"
                              method="post" id="create_custom">
                            <input type="hidden" name="create_signup_form_now" value="1">
                            <div class="inside">

                                <input type="text" name="form_name" class="form-large" size="30" maxlength="255"
                                       value="<?php echo $form_name ?>" id="form_name"
                                       placeholder="<?php _e( 'Form name', 'mailerlite' ); ?>">

                                <h2><?php _e( 'Lists', 'mailerlite' ); ?></h2>
                                <p class="description"><?php _e( 'Select the list(s) to which people who submit this form should be subscribed.',
                                        'mailerlite' ); ?></p>
                                <table id="list-table" class="form-table">
                                    <tbody>
                                    <?php foreach ( $groups as $group ): ?>
                                        <tr>
                                            <th style="width:1%;"><input id="list_<?php echo $group->id; ?>"
                                                                         type="checkbox"
                                                                         class="input_control"
                                                                         name="form_lists[]"
                                                                         value="<?php echo $group->id; ?>">
                                            </th>
                                            <td><label
                                                    for="list_<?php echo $group->id; ?>"><?php echo $group->name; ?></label>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ( $can_load_more_groups ) {
                                        ?>
                                        <tr>
                                            <td colspan="2">
                                                <button class="button-primary load-more-groups" type="button">
                                                    Load more
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <table id="more-groups" class="form-table" data-offset="2" style="display: none;"></table>
                                <?php if ( count( $groups ) === 0 ) {
                                    ?>
                                    <div class="notice notice-error">
                                        <p>
                                            <?php _e( 'Please create an Interest Group first.', 'mailerlite' ); ?>
                                        </p>
                                    </div>
                                    <?php
                                } ?>

                                <div id="no-selections-error" class="notice notice-error" style="display: none">
                                    <p>
                                        <?php _e( 'Please select at least one Interest Group (tag) from the list.',
                                            'mailerlite' ); ?>
                                    </p>
                                </div>
                                <input type="hidden" id="selected_groups" name="selected_groups" />
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

        <script type="text/javascript">
            (function () {
                var jQuery = window.jQueryWP || window.jQuery;

                jQuery(document).ready(function ($) {
                    $('#create_custom').on('submit', function (e) {
                        var checkedLists = $("[name='form_lists[]']:checked");

                        var selected_groups = '';
                        checkedLists.each(function() {
                            group = this.value +'::'+$("label[for='list_"+ this.value + "']").text();
                            if (selected_groups != '') {
                                selected_groups = selected_groups+';*'+group;
                            } else {
                                selected_groups = group;
                            }

                        });

                        $("[name=selected_groups]").val(selected_groups);
                        if (checkedLists.length === 0) {
                            $("#no-selections-error").show();
                            e.preventDefault();
                            return false;
                        }
                    });

                    $(document).on('click', '.load-more-groups', function () {
                        $('.load-more-groups').prop('disabled', true);

                        jQuery.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data: {
                                action: 'mailerlite_get_more_groups', offset: $('#more-groups').data('offset'),
                                ml_nonce: '<?php echo wp_create_nonce( 'mailerlite_load_more_groups' );?>'
                            }
                        }).done(function (html) {
                            $('#more-groups').data('offset', parseInt($('#more-groups').data('offset')) + 1);
                            $('.load-more-groups').parent().parent().remove();
                            $('#more-groups').show().append(html);
                        });
                    });
                });
            })();
        </script>

        <?php
    }
}