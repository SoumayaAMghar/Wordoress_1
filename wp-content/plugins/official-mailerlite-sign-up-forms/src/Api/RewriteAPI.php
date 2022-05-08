<?php
namespace MailerLiteForms\Api;

use MailerLiteForms\Models\MailerLiteAccount;
use MailerLiteForms\Models\MailerLiteField;
use MailerLiteForms\Models\MailerLiteGroup;
use MailerLiteForms\Models\MailerLiteWebForm;

class RewriteAPI
{
    private $url = 'https://connect.mailerlite.com/api';
    private $client;
    private $response;
    private $response_code;

    /**
     * RewriteAPI constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($api_key)
    {
        $this->client = new Client($this->url, [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Validate API Key
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function validateKey()
    {
        $response = $this->client->remote_get('/account');

        $response = self::parseResponse($response);

        $account  = new MailerLiteAccount();

        if ( isset($response->data) ) {

            $account->id = $response->data->id;
            $account->subdomain = '';
        }

        return $account;
    }

    /**
     * Get groups
     *
     * @access      public
     * @return      MailerLiteGroup[]
     * @since       1.5.0
     */
    public function getGroups($params)
    {
        $response = $this->client->remote_get('/groups', $params);
        $response = self::parseResponse($response);

        $groups = [];

        foreach ($response->data as $record) {

            $group = new MailerLiteGroup();
            $group->id = $record->id;
            $group->name = $record->name;
            $group->total = $record->active_count;
            $group->opened = $record->open_rate->float;
            $group->clicked = $record->click_rate->float;
            $group->date_created = $record->created_at;

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Check if more groups need to be loaded
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function checkMoreGroups($limit, $offset)
    {

        $response = $this->client->remote_get('/groups', [
            'limit' => $limit,
            'page' => $offset
        ]);

        $response = self::parseResponse($response);

        return count( $response->data ) > 0;
    }

    /**
     * Get more groups
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getMoreGroups($limit, $offset)
    {

        $response = $this->client->remote_get('/groups', [
            'limit' => $limit,
            'page' => $offset
        ]);

        $response = self::parseResponse($response);

        return $response->data;
    }

    /**
     * Search groups
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function searchGroups($name)
    {
        $response = $this->client->remote_get('/groups?page=1&limit=10&filter[name]='.$name);

        return self::parseResponse($response);
    }

    /**
     * Add subscriber
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function addSubscriber($subscriber, $resubscribe)
    {

        $response = $this->client->remote_post('/subscribers', [
            'email' => $subscriber,
            'resubscribe' => $resubscribe,
            'ip_address' => $_SERVER["REMOTE_ADDR"]
        ]);

        return self::parseResponse($response);
    }

    /**
     * Add subscriber to group (by id)
     *
     * @access      public
     * @return      bool
     * @since       1.5.0
     */
    public function addSubscriberToGroup($subscriber, $group_id, $resubscribe = 0)
    {

        $subscriber['groups'] = [ $group_id ];
        $subscriber['ip_address'] = $_SERVER['REMOTE_ADDR'];

        $response = $this->client->remote_post('/subscribers', $subscriber);
        $response = self::parseResponse($response);

        if( isset( $response->errors ) )
            return false;

        return true;
    }

    /**
     * Get Double opt-in setting
     *
     * @access      public
     * @return      bool
     * @since       1.5.0
     */
    public function getDoubleOptin()
    {
        $response = $this->client->remote_get('/subscribe-settings/double-optin');

        if( isset( $response ) ) {
            $response = self::parseResponse($response);

            if (isset($response->double_optin) && $response->double_optin) {

                return true;
            }
        }

        return false;
    }

    /**
     * Toggle Double opt-in setting
     *
     * @access      public
     * @return      bool
     * @since       1.5.0
     */
    public function setDoubleOptin()
    {
        $this->client->remote_post('/subscribe-settings/double-optin/toggle');

        return true;
    }

    /**
     * Get Embedded forms
     *
     * @access      public
     * @return      MailerLiteWebForm[]
     * @since       1.5.0
     */
    public function getEmbeddedForms($params)
    {
        $response = $this->client->remote_get('/forms/embedded', $params);
        $response = self::parseResponse($response);

        $forms = [];

        foreach ($response->data as $form) {
            $embedded = new MailerLiteWebForm();
            $embedded->id = $form->id;
            $embedded->name = $form->name;
            $embedded->code = $form->slug;
            $embedded->type = $form->type;

            $forms[] = $embedded;
        }

        return $forms;
    }

    /**
     * Get Fields
     *
     * @access      public
     * @param       $params
     * @return      MailerLiteField[]
     * @since       1.5.0
     */
    public function getFields($params): array
    {
        $response = $this->client->remote_get('/fields', $params);
        $response = self::parseResponse($response);

        $fields = [];

        foreach ($response->data as $field) {
            $ml_field = new MailerLiteField();
            $ml_field->id = $field->id;
            $ml_field->title = $field->name;
            $ml_field->key = $field->key;
            $ml_field->type = $field->type;

            $fields[] = $ml_field;
        }

        return $fields;
    }

    /**
     * Get raw response body
     *
     * @access      public
     * @return      string
     * @since       1.5.0
     */
    public function getResponseBody()
    {
        return $this->response;
    }

    /**
     * Get response code
     *
     * @access      public
     * @return      int
     * @since       1.5.0
     */
    public function responseCode()
    {
        return $this->response_code;
    }

    /**
     * Get response code and body
     *
     * @access      private
     * @return      mixed
     * @since       1.5.0
     */
    private function parseResponse($response)
    {
        $this->response = wp_remote_retrieve_body($response);
        $this->response_code = wp_remote_retrieve_response_code($response);

        if (!is_wp_error($this->response)) {
            $response = json_decode($this->response);

            if (json_last_error() == JSON_ERROR_NONE) {

                if (!isset($response->error)) {
                    return $response;
                }
            }
        } else {

            $errors = $this->response->get_error_messages();
            $this->response = 'WP Error: ';

            foreach ( $errors as $code => $msg ) {

                $this->response .= $code . ': ' . $msg . '. ';
            }
        }

        return false;
    }
}