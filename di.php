<?php

namespace DI;

use Resolver\TriggerResolver;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([

	TriggerResolver::class => function ()
	{
		$triggerResolver = new TriggerResolver();

		return $triggerResolver;
	}

]);

return $containerBuilder->build();
