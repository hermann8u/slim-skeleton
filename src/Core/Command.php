<?php

namespace Core;

use Core\Database\Manager;
use Interop\Container\Exception\ContainerException;
use Monolog\Logger;
use Slim\Container;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Command extends SymfonyCommand
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var \Slim\Container
     */
    private $container;

    /**
     * Set the container
     *
     * @param Container $container
     *
     * @return Command
     *
     * @throws ContainerException
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        $this->logger = $this->get('logger');
        $this->manager = $this->get('manager');

        return $this;
    }

    /**
     * Get a service with the container
     *
     * @param string $service
     *
     * @return mixed
     *
     * @throws ContainerException
     */
    public function get(string $service)
    {
        if ($this->container) {
            return $this->container->get($service);
        }

        return null;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Command
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Override Symfony console run command to add log and some styling.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Command ' . $this->getName() . ' starts !');
        
        $result = parent::run($input, $output);

        $this->logger->info('Command ' . $this->getName() . ' finished !');
        $this->io->title(str_repeat(' ', strlen($this->title)));

        return $result;
    }

    /**
     * Initialize SymfonyStyle and display command title
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        if (!$this->title) {
            $this->title = $this->get('settings')['app']['name'];
        }

        $this->io->title($this->title);
    }
}