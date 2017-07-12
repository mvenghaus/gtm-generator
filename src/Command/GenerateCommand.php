<?php

namespace Command;

use Generator\ImportGenerator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
	/** @var */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;

		return parent::__construct();
	}

	protected function configure()
	{
		$this
			->setName('generate')
			->setDescription('generate import file from json')
			->addArgument('config-file', InputArgument::REQUIRED, 'json config file')
			->addArgument('output-file', InputArgument::REQUIRED, 'output file');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->container->get(ImportGenerator::class)->generate(
			$input->getArgument('config-file'),
			$input->getArgument('output-file')
		);
	}
}