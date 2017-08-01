<?php

namespace Builder;

use Filter\FolderFilter;
use Reader\ConfigReader;
use Filter\TriggerFilter;
use Filter\VariableFilter;

class TagBuilder
{
	/** @var VariableFilter */
	private $variableFilter;
	/** @var TriggerFilter */
	private $triggerFilter;
	/** @var FolderFilter */
	private $folderFilter;

	/**
	 * @param VariableFilter $variableFilter
	 * @param TriggerFilter $triggerFilter
	 * @param FolderFilter $folderFilter
	 */
	public function __construct(VariableFilter $variableFilter,
	                            TriggerFilter $triggerFilter,
	                            FolderFilter $folderFilter)
	{
		$this->variableFilter = $variableFilter;
		$this->triggerFilter = $triggerFilter;
		$this->folderFilter = $folderFilter;
	}

	public function build(ConfigReader $configReader)
	{
		$tags = [];

		$tagId = 1;
		foreach ($this->getTags($configReader) as $tagFile => $data)
		{
			$tagContent = file_get_contents($tagFile);

			$tagContent = $this->triggerFilter->filter($tagContent);
			$tagContent = $this->variableFilter->filter($tagContent, $data);
			$tagContent = $this->folderFilter->filter($tagContent);

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

	private function getTags(ConfigReader $configReader)
	{
		$tags = [];

		foreach ($configReader->getTags() as $name => $data)
		{
			$tagFile = sprintf('%sdata/tag/%s/%s.json', ROOT_DIR, $name, $name);
			if (!file_exists($tagFile))
			{
				throw new \Exception(sprintf('tag file not found "%s.json"', $name));
			}

			$tags[$tagFile] = $data;
		}

		foreach ($configReader->getCustomTags() as $name => $data)
		{
			$tagFile = sprintf('%scustom/tag/%s/%s.json', ROOT_DIR, $name, $name);
			if (!file_exists($tagFile))
			{
				throw new \Exception(sprintf('tag file not found "%s.json"', $name));
			}

			$tags[$tagFile] = $data;
		}

		return $tags;
	}

}