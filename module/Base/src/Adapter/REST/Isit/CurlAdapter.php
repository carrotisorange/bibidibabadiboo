<?php
/**
 * @copyright   Copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Adapter\REST\Isit;

use InvalidArgumentException;
use Zend\Http\Client;
use Zend\Http\Request;

class CurlAdapter
{
    protected $domain;
    protected $httpProtocol;
    protected $user;
    protected $password;
    protected $ecrashAppId;
    protected $clientHttp;
    
    public function __construct($isHttpSecure,
        $domain,
        $user,
        $password,
        $ecrashAppId,
        Client $clientHttp,
        Array $config)
    {
        if (empty($domain)) {
            throw new InvalidArgumentException('Empty domain given.');
        }
        $this->httpProtocol = $isHttpSecure ? 'https' : 'http';
        $this->domain = $domain;
        $this->user = $user;
        $this->password = $password;
        $this->ecrashAppId = $ecrashAppId;
        $this->clientHttp = $clientHttp;
        $this->config = $config;
    }
    
    public function createTicket($requestXml)
    {
        if (empty($requestXml)) {
            return false;
        }
        
        $user = urldecode($this->user);
        $password = urldecode($this->password);
        
        $url = "{$this->httpProtocol}://{$this->domain}/REST/1.0/CreateUAITicket/CreateTicket?"
            . "user=$user&pass=$password";
        
        $this->clientHttp->setUri($url);
        $response = $this->clientHttp->setRawBody($requestXml)->setEncType('text/xml')->setMethod(Request::METHOD_POST)->send();
        
        return $response->getBody();
    }
    
    public function getMessageQueue()
    {
        $user = urldecode($this->user);
        $password = urldecode($this->password);

        $url = "{$this->httpProtocol}://{$this->domain}/REST/1.0/UAI/message/"
        . "{$this->ecrashAppId}?user=$user&pass=$password";
        $this->clientHttp->setUri($url);

        $response = $this->clientHttp->setMethod(Request::METHOD_GET)->send();

        return $response->getBody();
    }

    public function acknowledgeTicket($messageId)
    {
        if (empty($messageId) || !is_numeric($messageId)) {
            return false;
        }

        $user = urldecode($this->user);
        $password = urldecode($this->password);

        $url = "{$this->httpProtocol}://{$this->domain}/REST/1.0/UAI/message/$messageId"
            . "/isdelivered/?user=$user&pass=$password";
        $this->clientHttp->setUri($url);
        $response = $this->clientHttp->setMethod(Request::METHOD_GET)->send();

        return $response->getBody();
    }

    public function sendResponse($requestXml)
    {
        if (empty($requestXml)) {
            return false;
        }

        $user = urldecode($this->user);
        $password = urldecode($this->password);

        $url = "{$this->httpProtocol}://{$this->domain}/REST/1.0/UAI/message/2"
            . "/access_request_result/{$this->ecrashAppId}?user=$user&pass=$password";
        $this->clientHttp->setUri($url);
        $response = $this->clientHttp->setRawBody($requestXml)->setEncType('text/xml')->setMethod(Request::METHOD_POST)->send();

        return $response->getBody();
    }

    public function getLastUrl()
    {
        return $this->clientHttp->getUri(true);
    }
}
