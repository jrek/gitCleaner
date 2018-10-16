<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Twig_Loader_Filesystem;
use Twig_Environment;
use App\Helpers\ConfigHelper;


class SharedService extends ContainerBuilder
{
    public function __construct(array $configFilePath = [],ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
        $this->services = [
            'request' => Request::createFromGlobals()
        ];

        $this->addConfigHelper($configFilePath);
        $this->addResponse();
        $this->addTwig();
        $this->addJiraHelper();
    }

    private function addResponse()
    {
        $this->register('response', Response::class)
            ->addArgument('Content')
            ->addArgument(Response::HTTP_OK)
            ->addArgument([]);
    }

    private function addTwig()
    {
        $config = $this->get('ConfigHelper');

        $this->register('Twig_Loader', Twig_Loader_Filesystem::class)
            ->addArgument($config->get('templatesPath'));
        $this->register('Twig', Twig_Environment::class)
            ->addArgument(new Reference('Twig_Loader'))
            ->addArgument([
                'auto_reload' => true,
                'cache' => APP_PATH.'/tmp/twig'
            ]);
    }

    private function addJiraHelper()
    {
        $config = $this->get('ConfigHelper')->get('jira');

        $this->register('JiraHelper', \App\Helpers\JiraHelper::class)
            ->addArgument($config['host'])
            ->addArgument($config['user'])
            ->addArgument($config['password']);
    }

    private function addConfigHelper(array $configFilePath)
    {
        $this->register('ConfigHelper', ConfigHelper::class)
            ->addArgument($configFilePath['default'])
            ->addArgument($configFilePath['config']);
        $this->get('ConfigHelper')->loadConfigs();
    }
}
