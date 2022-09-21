<?php

namespace MrLijan\Restmold\Factory;

class RestModelFactory
{
    /**
     * The name of the new model class
     * @var string
     */
    private string $className;

    public function construct(string $className = '')
    {
        $this->className = $className;
    }

    /**
     * Return the new Stub
     * @param string $className
     * @return string
     */
    public function generate(string $className): string
    {
        // 1. set the className
        $this->className = $className;

        // 2. get the stub path
        return $this->replaceVarsInStub($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Return stub path
     * @return string
     */
    private function getStubPath(): string
    {
        return __DIR__ . "/stubs/RestModel.stub";
    }

    /**
     * Return stub variables
     * @return array
     */
    private function getStubVariables(): array
    {
        return [
            '{{NAMESPACE}}' => 'App\\ApiModels',
            '{{CLASSNAME}}' => $this->className,
            '{{EXTENDS}}' => 'RestModel'
        ];
    }

    /**
     * Replace variables in stub
     * @param string $stubPath
     * @param array $variables
     * @return string
     */
    private function replaceVarsInStub(string $stubPath, array $variables = []): string
    {
        $stubContent = file_get_contents($stubPath);
        return str_replace(array_keys($variables), array_values($variables), $stubContent);
    }
}
