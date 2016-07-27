<?php

namespace CalculatieTool\IntMaint;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class MaintenanceManager implements IntMaintInterface
{
    /**
     * The base host.
     *
     * @var string
     */
    protected $endpoint = 'http://localhost';

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
        return $this->endpoint . '/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserListByToken($access_token)
    {
        $response = $this->getHttpClient()->get(
            $this->endpoint . '/oauth2/rest/internal/user_all?access_token=' . $access_token
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
            // 'client_id' => $this->clientId,
            'client_id' => 'd9f9518225123fe381af72e78645222f64510518',
            // 'client_secret' => $this->clientSecret,
            'client_secret' => '34961f8264be5cf743550371393610cf6dd196af',
            'grant_type' => 'client_credentials',
            // 'redirect_uri' => $this->redirectUrl,
            'redirect_uri' => 'http://localhost:8080/login',
        ];
    }

    /**
     * Get the access token response for the given code.
     *
     * @return array
     */
    public function getAccessTokenResponse()
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
     * Request access token.
     *
     * @return this.
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
            throw new \Exception("Invalid server response");

        if (!array_key_exists('expires_in', $response))
            throw new \Exception("Invalid server response");

        if (!array_key_exists('token_type', $response))
            throw new \Exception("Invalid server response");

        if (strtolower($response['token_type']) != 'bearer')
            throw new \Exception("Invalid server response ");
    }

    /**
     * Loop over each retrieved user.
     *
     * @param  Clojure  $callback
     */
    public function user($callback)
    {
        $userlist = $this->getUserListByToken(
            $this->getAccessToken()
        );

        foreach ($userlist as $user) {
            $callback($user);
        }
    }
}