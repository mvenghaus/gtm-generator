<?php

chdir(__DIR__ . '/../');

foreach (glob('projects/*.json') as $file)
{
	if (preg_match('/import/', $file)) continue;

	system(sprintf('php gtm.php generate %s %s', $file, str_replace('.json', '.import.json', $file)));
}