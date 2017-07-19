<?php

namespace Builder;

use Reader\ConfigReader;
use Filter\VariableFilter;

class VariableBuilder
{
	/** @var VariableFilter */
	private $variableFilter;

	/**
	 * @param VariableFilter $variableFilter
	 */
	public function __construct(VariableFilter $variableFilter)
	{
		$this->variableFilter = $variableFilter;
	}

	public function build(ConfigReader $configReader)
	{
		$variables = [];

		$variableId = 1;
		foreach ($this->getVariables($configReader) as $name => $variable)
		{
			$variable['variableId'] = $variableId++;

			$variables[] = $variable;
		}

		return $variables;
	}

	/**
	 * @param ConfigReader $configReader
	 * @return array
	 * @throws \Exception
	 */
	private function getVariables(ConfigReader $configReader)
	{
		$variables = [];
		foreach ($configReader->getVariables() as $name)
		{
			$file = sprintf('%sdata/variable/%s.json', ROOT_DIR, $name);

			$variables[$name] = $this->loadVariable($file);
		}

		foreach ($configReader->getTags() as $tagName => $tagData)
		{
			foreach (glob(sprintf('%sdata/tag/%s/variable/*.json', ROOT_DIR, $tagName)) as $file)
			{
				$name = str_replace('.json', '', basename($file));
				$content = $this->loadVariable($file, $tagData);

				$variables[$name] = $content;
			}
		}

		return $variables;
	}


	private function loadVariable($file, $variables = [])
	{
		if (!file_exists($file))
		{
			throw new \Exception(sprintf('variable file not found "%s"', $file));
		}

		$content = file_get_contents($file);
		$content = $this->variableFilter->filter($content, $variables);

		return  json_decode($content, true);
	}

}