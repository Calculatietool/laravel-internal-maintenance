<?php

namespace CalculatieTool\IntMaint\Contracts;

interface EntityInterface
{
	/**
     * Retrieve entity uri.
     *
     * @return string
     */
    public function getUri();

    /**
     * Loop over each retrieved user.
     *
     * @param  Clojure  $callback
     */
    public function handle($response, $callback);
}
