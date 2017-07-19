<?php

namespace Builder;

use Reader\ConfigReader;
use Resolver\TriggerResolver;

class TriggerBuilder
{
	/** @var TriggerResolver */
	private $triggerResolver;

	/**
	 * @param TriggerResolver $triggerResolver
	 */
	public function __construct(TriggerResolver $triggerResolver)
	{
		$this->triggerResolver = $triggerResolver;
	}

	public function build(ConfigReader $configReader)
	{
		$triggers = [];

		$triggerId = 1;
		foreach ($configReader->getTrigger() as $name)
		{
			$triggerTemplate = sprintf('%sdata/trigger/%s.json', ROOT_DIR, $name);
			if (!file_exists($triggerTemplate))
			{
				throw new \Exception(sprintf('trigger file not found "%s.json"', $name));
			}

			$trigger = json_decode(file_get_contents($triggerTemplate), true);
			$trigger['triggerId'] = $triggerId++;

			$this->triggerResolver->add($trigger['triggerId'], $trigger['name']);

			$triggers[] = $trigger;
		}

		return $triggers;
	}

}