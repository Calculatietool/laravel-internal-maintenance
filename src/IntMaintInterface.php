<?php

namespace CalculatieTool\IntMaint;

interface IntMaintInterface
{
    /**
     * Create a request.
     *
     * @return string|void Returns a QrCode string depending on the format, or saves to a file.
     */
    public function request();
}
