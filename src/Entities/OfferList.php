<?php

namespace CalculatieTool\IntMaint\Entities;

use CalculatieTool\IntMaint\Contracts\EntityInterface;

class OfferList implements EntityInterface
{
    /**
     * The entity uri.
     *
     * @var \GuzzleHttp\Client
     */
    protected $uri = '/oauth2/rest/internal/offer_all';

    /**
     * Retrieve entity uri.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Loop over each retrieved user.
     *
     * @param  Clojure  $callback
     */
    public function handle($response, $callback)
    {
        foreach ($response as $offer) {
            $callback($offer);
        }
    }
}
