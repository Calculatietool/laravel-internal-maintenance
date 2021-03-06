<?php

namespace CalculatieTool\IntMaint;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use CalculatieTool\IntMaint\Contracts\IntMaintInterface;
use CalculatieTool\IntMaint\Exception\InvalidServerResponseException;

abstract class AbstractManager implements IntMaintInterface
{
    /**
     * The base host.
     *
     * @var string
     */
    protected $endpoint = 'https://app.calculatietool.com';

    /**
     * The HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Oauth access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Token expires in.
     *
     * @var int
     */
    protected $expiresIn;

    /**
     * Get endpoint from config if set.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        $config_endpoint = config('services.calculatietool.endpoint');
        if (!is_null($config_endpoint)) {
            return $config_endpoint;
        }

        return $this->endpoint;
    }

    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getEndpoint() . '/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityByToken($uri, $access_token)
    {
        $response = $this->getHttpClient()->get(
            $this->getEndpoint() . $uri . '?access_token=' . $access_token
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields()
    {
        return [
            'client_id' => config('services.calculatietool.client_id'),
            'client_secret' => config('services.calculatietool.client_secret'),
            'redirect_uri' => config('services.calculatietool.redirect'),
            'grant_type' => 'client_credentials',
        ];
    }

    /**
     * Get the access token response for the given code.
     *
     * @return array
     */
    protected function getAccessTokenResponse()
    {
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => $this->getTokenFields(),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the access token.
     *
     * @return string
     */
    protected function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        $this->validateTokenResponse(
            $token = $this->getAccessTokenResponse()
        );
        
        $this->accessToken = $token['access_token'];
        $this->expiresIn = $token['expires_in'];

        return $this;
    }

    /**
     * Validate returning token.
     */
    private function validateTokenResponse(Array $response)
    {
        if (!array_key_exists('access_token', $response))
            throw new \InvalidServerResponseException();

        if (!array_key_exists('expires_in', $response))
            throw new \InvalidServerResponseException();

        if (!array_key_exists('token_type', $response))
            throw new \InvalidServerResponseException();

        if (strtolower($response['token_type']) != 'bearer')
            throw new \InvalidServerResponseException();
    }
}