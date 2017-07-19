<?php

namespace Builder;

use Reader\ConfigReader;
use Filter\TriggerFilter;
use Filter\VariableFilter;

class TagBuilder
{
	/** @var VariableFilter */
	private $variableFilter;
	/** @var TriggerFilter */
	private $triggerFilter;

	/**
	 * @param VariableFilter $variableFilter
	 * @param TriggerFilter $triggerFilter
	 */
	public function __construct(VariableFilter $variableFilter,
	                            TriggerFilter $triggerFilter)
	{
		$this->variableFilter = $variableFilter;
		$this->triggerFilter = $triggerFilter;
	}

	public function build(ConfigReader $configReader)
	{
		$tags = [];

		$tagId = 1;
		foreach ($configReader->getTags() as $name => $data)
		{
			$tagTemplate = sprintf('%sdata/tag/%s/%s.json', ROOT_DIR, $name, $name);
			if (!file_exists($tagTemplate))
			{
				throw new \Exception(sprintf('tag file not found "%s.json"', $name));
			}

			$tagContent = file_get_contents($tagTemplate);

			$tagContent = $this->triggerFilter->filter($tagContent);
			$tagContent = $this->variableFilter->filter($tagContent, $data);

			$tagList = json_decode($tagContent, true);
			if (isset($tagList['accountId']))
			{
				$tagList = [$tagList];
			}
			foreach ($tagList as $tag)
			{
				$tag['tagId'] = $tagId++;

				$tags[] = $tag;
			}
		}

		return $tags;
	}

}