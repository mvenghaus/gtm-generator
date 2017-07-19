<?php

namespace Filter;

use Resolver\TriggerResolver;

class TriggerFilter
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

	public function filter($content)
	{
		preg_match_all('/<<TRIGGER ([^>]+)>>/is', $content, $results);
		if (isset($results[1]))
		{
			foreach ($results[1] as $triggerName)
			{
				$triggerId = $this->triggerResolver->resolveIdByName($triggerName);

				$content = str_replace(sprintf('<<TRIGGER %s>>', $triggerName), $triggerId, $content);
			}
		}

		return $content;
	}

}
