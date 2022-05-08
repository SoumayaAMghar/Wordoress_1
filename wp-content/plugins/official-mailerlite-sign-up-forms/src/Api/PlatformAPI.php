<?php
namespace MailerLiteForms\Api;

class PlatformAPI
{
    private $api;
    private $api_key;

    /**
     * PlatformAPI constructor
     *
     * @access      public
     * @return      void
     * @since       1.5.0
     */
    public function __construct($api_key)
    {

        $this->api_key = $api_key;

        switch ( $this->getApiType() ) {
            case ApiType::CURRENT:
                $this->api = new MailerLiteAPI($api_key);
                break;
            case ApiType::REWRITE:
                $this->api = new RewriteAPI($api_key);
                break;
            default:
                $this->api_key = '';
                break;
        }

    }

    /**
     * get API Key Type
     *
     * @access      public
     * @return      int
     * @since       1.5.0
     */
    public function getApiType()
    {

        if ( $this->api_key == '')
            return ApiType::INVALID;

        if ( $this->isValidMd5Key() ) {

            return ApiType::CURRENT;
        }else{

            return ApiType::REWRITE;
        }
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
        return $this->api->validateKey();
    }

    /**
     * Get groups
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getGroups($params = [])
    {
        return $this->api->getGroups($params);
    }

    /**
     * Check if more groups need to be loaded
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function checkMoreGroups($limit = 100, $offset = 2)
    {
        return $this->api->checkMoreGroups($limit, $offset);
    }

    /**
     * Check if more groups need to be loaded
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getMoreGroups($limit = 100, $offset = 1)
    {
        return $this->api->getMoreGroups($limit, $offset);
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
        return $this->api->searchGroups($name);
    }

    /**
     * Add subscriber
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function addSubscriber($subscriber, $resubscribe = 0)
    {
        return $this->api->addSubscriber($subscriber, $resubscribe);
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
        return $this->api->addSubscriberToGroup($subscriber, $group_id, $resubscribe);
    }

    /**
     * Get Double opt-in setting
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getDoubleOptin()
    {
        return $this->api->getDoubleOptin();
    }

    /**
     * Enable/Disable Double opt-in setting
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function setDoubleOptin($enable)
    {
        return $this->api->setDoubleOptin($enable);
    }

    /**
     * Get Embedded forms
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getEmbeddedForms($params = [])
    {
        return $this->api->getEmbeddedForms($params);
    }

    /**
     * Get Fields
     *
     * @access      public
     * @return      mixed
     * @since       1.5.0
     */
    public function getFields($params = [])
    {
        return $this->api->getFields($params);
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
        return $this->api->getResponseBody();
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
        return $this->api->responseCode();
    }

    /**
     * Checks if token is a valid md5 string
     *
     * @access      private
     * @return      bool
     * @since       1.5.0
     */
    private function isValidMd5Key() {

        return strlen($this->api_key) == 32 && ctype_xdigit($this->api_key);
    }
}