<?php

namespace CalculatieTool\IntMaint\Entities;

use CalculatieTool\IntMaint\Contracts\EntityInterface;

class UserList implements EntityInterface
{
    /**
     * The entity uri.
     *
     * @var \GuzzleHttp\Client
     */
    protected $uri = '/oauth2/rest/internal/user_all';

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
        foreach ($response as $user) {
            $callback($user);
        }
    }
}
