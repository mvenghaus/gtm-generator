<?php

namespace Builder;

use Filter\FolderFilter;
use Filter\VariableFilter;
use Reader\ConfigReader;
use Resolver\TriggerResolver;

class TriggerBuilder
{
	/** @var TriggerResolver */
	private $triggerResolver;
	/** @var VariableFilter */
	private $variableFilter;
	/** @var FolderFilter */
	private $folderFilter;

	/**
	 * @param TriggerResolver $triggerResolver
	 * @param VariableFilter $variableFilter
	 * @param FolderFilter $folderFilter
	 */
	public function __construct(TriggerResolver $triggerResolver,
	                            VariableFilter $variableFilter,
								FolderFilter $folderFilter)
	{
		$this->triggerResolver = $triggerResolver;
		$this->variableFilter = $variableFilter;
		$this->folderFilter = $folderFilter;
	}

	public function build(ConfigReader $configReader)
	{
		$triggers = [];

		$triggerId = 1;
		foreach ($this->getTrigger($configReader) as $triggerFile => $data)
		{
			$triggerContent = file_get_contents($triggerFile);

			$triggerContent = $this->variableFilter->filter($triggerContent, $data);
			$triggerContent = $this->folderFilter->filter($triggerContent);

			$trigger = json_decode($triggerContent, true);
			$trigger['triggerId'] = $triggerId++;

			$this->triggerResolver->add($trigger['triggerId'], $trigger['name']);

			$triggers[] = $trigger;
		}

		return $triggers;
	}

	public function getTrigger(ConfigReader $configReader)
	{
		$trigger = [];

		foreach ($configReader->getTrigger() as $name => $data)
		{
			$triggerFile = sprintf('%sdata/trigger/%s.json', ROOT_DIR, $name);
			if (!file_exists($triggerFile))
			{
				throw new \Exception(sprintf('trigger file not found "%s.json"', $name));
			}

			$trigger[$triggerFile] = $data;
		}

		foreach ($configReader->getCustomTrigger() as $name => $data)
		{
			$triggerFile = sprintf('%scustom/trigger/%s.json', ROOT_DIR, $name);
			if (!file_exists($triggerFile))
			{
				throw new \Exception(sprintf('trigger file not found "%s.json"', $name));
			}

			$trigger[$triggerFile] = $data;
		}

		return $trigger;
	}

}