<?php

namespace Builder;

use Reader\ConfigReader;
use Resolver\variableResolver;

class VariableBuilder
{

	public function build(ConfigReader $configReader)
	{
		$variables = [];

		$variableId = 1;
		foreach ($this->getVariables($configReader) as $file)
		{
			if (!file_exists($file))
			{
				throw new \Exception(sprintf('variable file not found "%s"', $file));
			}

			$variable = json_decode(file_get_contents($file), true);
			$variable['variableId'] = $variableId++;

			$variables[] = $variable;
		}

		return $variables;
	}

	/**
	 * @param ConfigReader $configReader
	 * @return array|mixed|string
	 */
	private function getVariables(ConfigReader $configReader)
	{
		$variables = [];
		foreach ($configReader->getVariables() as $name)
		{
			$variables[$name] = sprintf('%sdata/variable/%s.json', ROOT_DIR, $name);
		}

		foreach ($configReader->getTags() as $tagName => $tagData)
		{
			foreach (glob(sprintf('%sdata/tag/%s/variable/*.json', ROOT_DIR, $tagName)) as $file)
			{
				$variables[str_replace('.json', '', basename($file))] = $file;
			}
		}

		return $variables;
	}

}