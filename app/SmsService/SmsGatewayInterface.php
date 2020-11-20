<?php
/**
 * The Sms Gateway to send SMS through various providers.
 * It supports multiple sms gateways, and easily extendable to support new gateways.
 *
 * PHP version 7.1
 *
 * @category PHP/Laravel
 * @author  Mary Grace Cordoto
 */
namespace App\SmsService;

interface SmsGatewayInterface
{

    /**
     * The abstract function to send sms using provided  SMS API
     *
     * @param String $message The sms message
     * @param String $smsTo      The recipient number
     *
     * @return mixed The response from API
     */
    public function send($message, $smsTo);

    /**
     * The abstract function to get response from the API
     *
     * @return ResponseData The response object
     */
    public function getResponseData():ResponseData;
}