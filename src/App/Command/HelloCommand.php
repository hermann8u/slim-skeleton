<?php

namespace App\Command;

use App\Entity\User;
use Core\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:hello')
            ->setTitle('The Famous Hello World command')
            ->setDescription('Say "Hello World" !')
            ->setHelp('This command say Hello world')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dd($this->manager->getRepository(User::class)->findOneBy([
            'email' => 'florian.hermann94@gmail.com'
        ]));
        $this->logger->debug('Hello world');
        $this->io->block('Hello world');
    }
}