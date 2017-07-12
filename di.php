<?php

namespace DI;

use Resolver\TriggerResolver;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([

	TriggerResolver::class => function ()
	{
		$triggerResolver = new TriggerResolver();

		$triggerResolver->add(2147479553, 'All Pages');

		return $triggerResolver;
	}

]);

return $containerBuilder->build();
