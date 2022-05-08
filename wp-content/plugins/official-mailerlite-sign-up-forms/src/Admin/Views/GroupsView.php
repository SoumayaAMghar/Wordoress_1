<?php

namespace MailerLiteForms\Admin\Views;

use MailerLiteForms\Models\MailerLiteGroup;

class GroupsView
{

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct( $groups, $form, $can_load_more_groups )
    {

        $this->view( $groups, $form, $can_load_more_groups );
    }

    /**
     * Output view
     *
     * @access      private
     * @return      void
     * @since       1.5.0
     */
    private function view( $groups, $form, $can_load_more_groups )
    {

        ?>
        <tbody>
        <?php
        /** @var MailerLiteGroup $group */
        foreach ( $groups as $group ) { ?>
            <tr>
                <th style="width:1%;"><input
                        id="list_<?php echo $group->id; ?>"
                        type="checkbox"
                        class="input_control"
                        name="form_lists[]"
                        value="<?php echo $group->id; ?>"<?php echo in_array( $group->
                    id,
                        $form->data['lists'] ) ? ' checked="checked"' : ''; ?>>
                </th>
                <td>
                    <label for="list_<?php echo $group->id; ?>"><?php echo $group->name; ?></label>
                </td>
            </tr>
        <?php } ?>
        <?php
            if ( $can_load_more_groups ) {
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
        <?php
    }
}