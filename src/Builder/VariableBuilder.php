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
		foreach ($configReader->getVariables() as $name => $data)
		{
			$variableTemplate = sprintf('%sdata/variable/%s.json', ROOT_DIR, $name);
			if (!file_exists($variableTemplate))
			{
				throw new \Exception(sprintf('variable file not found "%s.json"', $name));
			}

			$variable = json_decode(file_get_contents($variableTemplate), true);
			$variable['variableId'] = $variableId++;

			$variables[] = $variable;
		}

		return $variables;
	}

}