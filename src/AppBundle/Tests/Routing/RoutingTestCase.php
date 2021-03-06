<?php

namespace AppBundle\Tests\Routing;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class RoutingTestCase extends WebTestCase
{
    /** @var Client client */
    protected $client;

    /** @var  RouterInterface */
    protected $router;

    /** @var ContainerInterface */
    protected $container;

    /**
     * @dataProvider uriProvider
     * @link https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.data-providers
     */
    public function testPageIsSuccessful($uri)
    {
        $this->client->request('GET', $uri);

        $this->logInFile($uri,$this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Test route: ' . $uri);
    }

    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));

        $this->router = self::$kernel->getContainer()->get('router');

        $this->container = self::$kernel->getContainer();
    }

    public function uriProvider()
    {
        $this->setUp();

        $uris = array();

        /** @var Route $route */
        foreach($this->getControllerRoutes() as $routeName => $route)
        {
            $uris[] = array($this->replaceParamteterInPath($routeName,$route));
        }

        return $uris;
    }

    protected function replaceParamteterInPath($routeName,Route $route)
    {
        $path = $route->getPath();

        if(preg_match('/{\w*}+/' ,$path))
        {
            $parameters = $this->getParameters();

            $parameterForRoute = $parameters[$routeName];

            foreach($parameterForRoute as $parameter=>$value)
            {
                $path = str_replace('{'.$parameter.'}',$value,$path);
            }
        }


        return $path;
    }


    /**
     * @return array
     */
    abstract protected function getParameters();

    /**
     *
     * cette fonction retourne la class du controller
     * à tester.
     *
     * @return string
     */
    abstract public function getControllerClass();

    /**
     * get route and route name of a controller
     *
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getControllerRoutes()
    {
        $controllerClass = $this->getControllerClass();

        if (!class_exists($controllerClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $controllerClass));
        }

        $class = new \ReflectionClass($controllerClass);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $controllerClass));
        }

        $routes = array();
        foreach ($class->getMethods() as $method) {

            /** @var Route $route */
            foreach($this->router->getRouteCollection() as $routeName => $route)
            {
                $routeController = $route->getDefault('_controller');

                if($routeController == $controllerClass.'::'.$method->getName())
                {
                    $routes[$routeName] = $route;
                }
            }
        }
        return $routes;
    }

    /**
     * Save each tested page in a html file.
     * Make it easyer when you want to check errors...
     *
     * @param $uri
     * @param $message
     */
    public function logInFile($uri,$message)
    {
        $file = str_replace ("/" ,  "_" ,  $uri  ).'.html';
        $logDir = self::$kernel->getContainer()->getParameter('kernel.logs_dir').'/tests/page_tests/';
        $logFile = $logDir.$file;
        $fs = new Filesystem();
        $fs->dumpFile($logFile,$message);
    }


}