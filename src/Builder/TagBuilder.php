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
		return ['abc' => 'def'];
	}

}