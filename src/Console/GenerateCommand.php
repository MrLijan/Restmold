<?php

namespace Mrlijan\Restmold\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Mrlijan\Restmold\Factory\RestModelFactory;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restmold:generate {className}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run command to generate new api-model boilerplate';

    protected Filesystem $files;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Filesystem $filesystem, RestModelFactory $modelFactory)
    {
        $this->files = $filesystem;

        $className = $this->singularClassName($this->argument('className'));
        $filePath = $this->getFilePath($className);

        if ($this->isAlreadyExists($filePath)) {
            $this->info("File : {$filePath} already exists");
            return;
        }

        $this->createDirectory(dirname($filePath));
        $this->createFile($filePath, $modelFactory->generate($className));

        $this->info("File : {$filePath} created");
    }

    /**
     * Pluralize class name
     * @param string $name
     * @return string
     */
    private function singularClassName(string $name): string
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Return the full path of the new file's target
     */
    private function getFilePath(string $className)
    {
        return base_path('app/ApiModels') . '/' . $className . 'Model.php';
    }

    /**
     * Return if the file already exists
     * @return bool
     */
    private function isAlreadyExists(string $path): bool
    {
        return $this->files->exists($path);
    }

    /**
     * Create new directory if needed
     */
    private function createDirectory(string $path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Create the new file and places at the directory
     * @param string $path
     * @param string $stub
     */
    private function createFile(string $path, string $stub)
    {
        return $this->files->put($path, $stub);
    }
}
