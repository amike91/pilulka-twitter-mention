<?php

namespace App\Services;

use App\Exceptions\TwitterApiException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Takes care of everything we need from Twitter API v2.
 *
 */
class Twitter {
    protected HttpClient $httpClient;

    protected string $bearerToken   = '';
    protected string $baseApiUrl    = 'https://api.twitter.com/2';

    public function __construct() {
        $this->httpClient       = new HttpClient;

        $this->setBaseApiUrl(config('twitter.base_api_url'));
        $this->setBearerToken(config('twitter.bearer_token'));
    }

    public function getBaseApiUrl() : string {
        return $this->baseApiUrl;
    }

    /**
     * Sets the base URL of the Twitter API. No trailing slash.
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseApiUrl(string $baseUrl) : self {
        $this->baseApiUrl       = $baseUrl;
        return $this;
    }

    public function getBearerToken() : string {
        return $this->bearerToken;
    }

    public function setBearerToken(string $bearerToken) : self {
        $this->bearerToken      = $bearerToken;
        return $this;
    }


    /**
     * @throws GuzzleException
     */
    private function sendApiRequest(string $endpoint, string $method, array $queryParams = []) : ResponseInterface {
        $url        = $this->getBaseApiUrl() . $endpoint;
        $query      = http_build_query($queryParams);

        $url        .= "?" . $query;

        return $this->httpClient->request($method, $url, [
            'headers'   => [
                'Authorization'     => 'Bearer ' . $this->getBearerToken()
            ]
        ]);
    }

    /**
     * Gets the tweets from the last 7 days given the Twitter API query.
     *
     * @link https://developer.twitter.com/en/docs/twitter-api/tweets/search/integrate/build-a-query#build
     * @throws GuzzleException|TwitterApiException
     */
    public function getRecentTweets(string $query) {
        $endpoint           = '/tweets/search/recent';
        $params             = [
            'query'             => $query,
            'max_results'       => 100,
            'tweet.fields'      => 'created_at',
            'expansions'        => 'author_id',
            'user.fields'       => 'created_at,name,profile_image_url,verified',
        ];

        try {
            $result = $this->sendApiRequest($endpoint, 'GET', $params);
        } catch (\Exception $exception) {
            throw new TwitterApiException($exception->getMessage());
        }

        return json_decode($result->getBody(), true);
    }
}
