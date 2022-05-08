<?php
namespace MailerLiteForms\Api;

use MailerLiteForms\Api\Client;
use MailerLiteForms\Models\MailerLiteAccount;
use MailerLiteForms\Models\MailerLiteWebForm;

class MailerLiteAPI
{
    private $url = 'https://api.mailerlite.com/api/v2';
    private $client;
    private $response;
    private $response_code;

    /**
     * MailerLiteAPI constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($api_key)
    {
        $this->client = new Client($this->url, [
            'X-MailerLite-ApiKey' => $api_key,
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

        $response = $this->client->remote_get('/');
        $response = self::parseResponse($response);

        $account = false;

        if ( $this->response_code === 200 ) {

            $account  = new MailerLiteAccount();
            $account->id = $response->account->id;
            $account->subdomain = $response->account->subdomain;
        }


        return $account;
    }

    /**
     * Get groups
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getGroups($params)
    {
        $response = $this->client->remote_get('/groups', $params);

        return self::parseResponse($response);
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
            'offset' => ( $offset - 1 ) * $limit
        ]);

        return count( self::parseResponse($response) ) > 0;
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
            'offset' => ( $offset - 1 ) * $limit
        ]);

        return self::parseResponse($response);
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
        $response = $this->client->remote_post('/groups/search', ['group_name' => $name]);

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
            'email' => $subscriber['email'],
            'fields' => $subscriber['fields'],
            'resubscribe' => $resubscribe
        ]);

        return self::parseResponse($response);
    }

    /**
     * Add subscriber to group (by id)
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function addSubscriberToGroup($subscriber, $group_id, $resubscribe = 0)
    {

        $response = $this->client->remote_post('/groups/' . $group_id . '/subscribers', [
            'email' => $subscriber['email'],
            'fields' => $subscriber['fields'],
            'resubscribe' => $resubscribe
        ]);

        return self::parseResponse($response);
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
        $response = $this->client->remote_get('/settings/double_optin');
        $response = self::parseResponse($response);

        if ( isset( $response->enabled ) && $response->enabled ) {

            return true;
        }

        return false;
    }

    /**
     * Enable/Disable Double opt-in setting
     *
     * @access      public
     * @return      bool
     * @since       1.5.0
     */
    public function setDoubleOptin($enable)
    {

        $response = $this->client->remote_post('/settings/double_optin', ['enable' => $enable]);
        $response = self::parseResponse($response);

        if ( isset( $response->enabled ) && $response->enabled ) {

            return true;
        }

        return false;
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
        $response = $this->client->remote_get('/webforms', $params);
        $response = self::parseResponse($response);

        $forms = [];

        foreach ($response as $form) {
            $embedded = new MailerLiteWebForm();
            $embedded->id = $form->id;
            $embedded->name = $form->name;
            $embedded->code = $form->code;
            $embedded->type = $form->type;

            $forms[] = $embedded;
        }

        return $forms;
    }

    /**
     * Get Fields
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getFields($params)
    {
        $response = $this->client->remote_get('/fields', $params);

        return self::parseResponse($response);
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
        }

        return false;
    }
}