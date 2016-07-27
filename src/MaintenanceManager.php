<?php

namespace CalculatieTool\IntMaint;

use CalculatieTool\IntMaint\Contracts;

class MaintenanceManager extends AbstractManager
{
    /**
     * Creates a new datatype object and then generates a QrCode.
     *
     * @param $method
     * @param $arguments
     */
    public function get($entity, $callback)
    {
        $entity = $this->createClass($entity);
    
        $entity->handle($this->getEntityByToken(
            $entity->getUri(),
            $this->getAccessToken()
        ), $callback);
    }

    /**
     * Creates a new Entity class dynamically.
     *
     * @param string $entity
     *
     * @return CalculatieTool\IntMaint\Contracts\EntityInterface
     */
    private function createClass($entity)
    {
        $class = $this->formatClass($entity);
        if (!class_exists($class)) {
            throw new \Exception("Invalid entity");
        }

        return new $class();
    }

    /**
     * Formats the method name correctly.
     *
     * @param $entity
     *
     * @return string
     */
    private function formatClass($entity)
    {
        $entity = ucfirst(strtolower($entity));
        $class = "CalculatieTool\IntMaint\Entities\\" . $entity . "List";

        return $class;
    }
}