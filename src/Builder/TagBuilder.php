<?php

namespace Builder;

use Reader\ConfigReader;
use Resolver\TriggerResolver;

class TagBuilder
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
		$tags = [];

		$tagId = 1;
		foreach ($configReader->getTags() as $name => $data)
		{
			$tagTemplate = sprintf('%sdata/tag/%s.json', ROOT_DIR, $name);
			if (!file_exists($tagTemplate))
			{
				throw new \Exception(sprintf('tag file not found "%s.json"', $name));
			}

			$tagContent = file_get_contents($tagTemplate);

			$tagContent = $this->replaceTriggerPlaceholder($tagContent);
			$tagContent = $this->replaceDataVariables($tagContent, $data);

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

	private function replaceTriggerPlaceholder($tagContent)
	{
		preg_match_all('/<<TRIGGER ([^>]+)>>/is', $tagContent, $results);
		if (isset($results[1]))
		{
			foreach ($results[1] as $triggerName)
			{
				$triggerId = $this->triggerResolver->resolveIdByName($triggerName);

				$tagContent = str_replace(sprintf('<<TRIGGER %s>>', $triggerName), $triggerId, $tagContent);
			}
		}

		return $tagContent;
	}

	private function replaceDataVariables($tagContent, $data)
	{
		foreach ($data as $key => $value)
		{
			$tagContent = str_replace(sprintf('{{%s}}', $key), $value, $tagContent);
		}

		return $tagContent;
	}

}