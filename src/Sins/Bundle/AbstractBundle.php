<?php

namespace SinSquare\Bundle;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractBundle implements ServiceProviderInterface
{
    protected $name;
    protected $extension;
    protected $path;
    private $namespace;

    public function register(Container $container)
    {
        if (!isset($container['bundles'])) {
            $container['bundles'] = array();
        }

        $bundles = $container['bundles'];
        $bundles[] = $this;

        $container['bundles'] = $bundles;

        $this->registerCommands($container);
        $this->registerControllers($container);
    }

    public function getNamespace()
    {
        if (null === $this->namespace) {
            $this->parseClassName();
        }

        return $this->namespace;
    }

    public function getPath()
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = dirname($reflected->getFileName());
        }

        return $this->path;
    }

    final public function getName()
    {
        if (null === $this->name) {
            $this->parseClassName();
        }

        return $this->name;
    }

    public function registerCommands(Container $application)
    {
        if (!is_dir($dir = $this->getPath().'/Command')) {
            return;
        }

        if (!isset($application['console'])) {
            return;
        }

        if (!class_exists('Symfony\Component\Finder\Finder')) {
            throw new \RuntimeException('You need the symfony/finder component to register bundle commands.');
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNamespace().'\\Command';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.str_replace('/', '\\', $relativePath);
            }
            $class = $ns.'\\'.$file->getBasename('.php');

            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                $application['console']->add($r->newInstance());
            }
        }
    }

    public function registerControllers(Container $application)
    {
        if (!is_dir($dir = $this->getPath().'/Controller')) {
            return;
        }

        if (!class_exists('Symfony\Component\Finder\Finder')) {
            throw new \RuntimeException('You need the symfony/finder component to register bundle commands.');
        }

        $finder = new Finder();
        $finder->files()->name('*Controller.php')->in($dir);

        $prefix = $this->getNamespace().'\\Controller';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.str_replace('/', '\\', $relativePath);
            }
            $baseClass = $file->getBasename('.php');
            $class = $ns.'\\'.$baseClass;

            if (!isset($application['controller.ids'])) {
                $application['controller.ids'] = array();
            }

            $r = new \ReflectionClass($class);

            if ($r->isSubclassOf(AbstractController::class) && !$r->isAbstract()) {
                if (isset($application['controller.ids'][$baseClass])) {
                    //controller basename must be unique...in the future can be @BundleName/Controller -> Silex\CallbackResolver:convertCallback must be extended
                    continue;
                } else {
                    $arr = $application['controller.ids'];
                    $arr[] = $baseClass;
                    $application['controller.ids'] = $arr;
                }

                $application[$baseClass] = function ($application) use ($r) {
                    return $r->newInstance($application);
                };
            }
        }
    }

    private function parseClassName()
    {
        $pos = strrpos(static::class, '\\');
        $this->namespace = false === $pos ? '' : substr(static::class, 0, $pos);
        if (null === $this->name) {
            $this->name = false === $pos ? static::class : substr(static::class, $pos + 1);
        }
    }
}
