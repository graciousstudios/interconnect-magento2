<?php

namespace Gracious\Interconnect\Http\Request;

use Gracious\Interconnect\Foundation\Environment;
use Gracious\Interconnect\Helper\Config;
use Gracious\Interconnect\Reporting\Logger;
use Gracious\Interconnect\System\Exception as InterconnectException;
use Zend\Http\Client as ZendHttpClient;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;

class Client
{
    const ENDPOINT_CUSTOMER = 'customer/register';
    const ENDPOINT_NEWSLETTER_SUBSCRIBER = 'newsletter/subscribe/popup';
    const ENDPOINT_ORDER = 'order/process';
    const ENDPOINT_QUOTE = 'quote/process';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Gracious\Interconnect\Http\Request\Client
     */
    private $client;

    /**
     * Client constructor.
     * @param Config $config
     * @param Logger $logger
     * @param ZendHttpClient $client
     */
    public function __construct(Config $config, Logger $logger, ZendHttpClient $client)
    {
        $this->helperConfig = $config;
        $this->logger = $logger;
        $this->client = $client;
        $this->client->setBaseUrl($config->getInterconnectServiceBaseUrl());
    }

    /**
     * @param string $baseUrl
     * @return Client
     */
    protected function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param array $data
     * @param string $endPoint
     * @throws InterconnectException
     * @throws \Zend\Http\Client\Exception\RuntimeException
     */
    public function sendData(array $data, $endPoint)
    {
        if (null === $this->baseUrl) {
            throw new InterconnectException('Unable to make request: base url not set');
        }

        if (Environment::isInDeveloperMode()) {
            $curlAdapter = new Curl();
            $curlAdapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
            $curlAdapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
            $this->client->setAdapter($curlAdapter);
        }

        $metaData = Environment::getInstance();
        $json = json_encode($data);

        if (Environment::isInDeveloperMode()) {
            $this->logger->info(__METHOD__ . ':: Posting to \'' . $this->baseUrl . '/' . $endPoint . '\'. Data = ' . $json);
        }


        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => 'application/json',
            'X-Secret' => $this->helperConfig->getApiKey(),
            'X-ModuleType' => $metaData->getModuleType(),
            'X-ModuleVersion' => $metaData->getModuleVersion(),
            'X-AppHandle' => 'magento2',
            'X-AppVersion' => $metaData->getMagentoVersion(),
            'X-Domain' => $metaData->getDomain()
        ]);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST)
            ->setUri($this->baseUrl . '/' . $endPoint)
            ->setHeaders($headers)
            ->setContent($json);

        $this->processResponse(
            $this->client->send($request)
        );
    }

    /**
     * @param Response $response
     * @throws InterconnectException
     */
    protected function processResponse(Response $response)
    {
        $request = $this->client->getRequest();

        if ($response->isOk()) {
            $this->logger->error('Response status = ' . $response->getStatusCode() . ', response = ' . (string) $response);
            throw new InterconnectException('Error making request to \'' . $request->getUriString() . '\' with http status code :' . $response->getStatusCode() . ' and response ' . (string) $response);
        }

        if (Environment::isInDeveloperMode()) {
            $this->logger->info('Data sent to: ' . $request->getUriString() . '. All done here...');
        }
    }
}