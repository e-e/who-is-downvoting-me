<?php

namespace App;

use App\Exceptions\MissingCommandException;
use App\Providers\Provider;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Class App
 * @package App
 */
class App {
    
    private $baseDir;
    private $commands;
    private $argv;
    private $registry = [];
    
    private $logger;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->commands = require_once __DIR__ . "/commands.php";
        $this->argv = $_SERVER['argv'];
    }
    
    public function run()
    {
        try {
            $this->logger = $this->gimme(LoggerInterface::class);
            
            $command = $this->getCommand();
            $commandClass = $this->commands[$command];
            
            $this->logger->info('Running command', [
                'command' => $command,
            ]);
            
            $runner = $this->gimme($commandClass);
            $runner->run($this);
        } catch (MissingCommandException $e) {
            $this->logger->warning("MissingCommandException", [
                'error' => (string)$e,
            ]);
        } catch (\ReflectionException $e) {
            $this->logger->warning("ReflectionException", [
                'error' => (string)$e,
            ]);
        }
    }

    /**
     * @param string $path
     */
    public function setBaseDir(string $path)
    {
        $this->baseDir = $path;
    }

    public function getBaseDir() : string
    {
        return $this->baseDir;
    }
    
    /**
     * @param string $key
     * @param string $classPathOrFunction
     */
    public function register(string $key, $classPathOrFunction)
    {
        $this->registry[$key] = $classPathOrFunction;
    }

    /**
     * @param string $providerClass
     */
    public function provider(string $providerClass)
    {
        /** @var Provider $provider */
        $provider = new $providerClass;
        $provider->provide($this);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \ReflectionException
     */
    public function gimme(string $key)
    {
        $class = $key;
        
        if (array_key_exists($key, $this->registry)) {
            $class = $this->registry[$key];
        }
        
        if (is_callable($class) or $class instanceof \Closure) {
            return $class($this);
        }
        
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        
        try {
            $params = $constructor->getParameters();
        } catch (\Error $e) {
            $this->logger->error('Gimme error', [
                'error' => (string)$e,
            ]);
            exit();
        }
        
        $instanciatedParameters = [];
        
        foreach ($params as $param) {
            
            $paramClass = $param->getClass();
            
            if (is_null($paramClass)) {
                $this->logger->notice("Parameter class is null", [
                    "parameter" => $param->getName(),
                    "parent_class" => $class,
                ]);
                continue;
            }
            
            $paramClassName = $paramClass->getName();
            
            $instanciatedParameters[] = $this->gimme($paramClassName);
        }
        
        return new $class(...$instanciatedParameters);
    }

    /**
     * @return string
     * @throws MissingCommandException
     */
    private function getCommand() : string
    {
        if (count($this->argv) < 2) {
            throw new MissingCommandException("Command is required");
        }

        return trim($this->argv[1]);
    }
}