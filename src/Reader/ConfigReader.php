<?php

namespace Reader;

class ConfigReader
{

	private $data = [];

	public function __construct($file)
	{
		$this->data = json_decode(file_get_contents($file), true);
	}

	public function getTags()
	{
		return (isset($this->data['tag']) ? $this->data['tag'] : []);
	}

	public function getTrigger()
	{
		if (!isset($this->data['trigger']))
		{
			return [];
		}

		$trigger = $this->data['trigger'];
		if (is_string($trigger) && $trigger === '*')
		{
			$trigger = [];
			foreach (glob(ROOT_DIR . 'data/trigger/*.json') as $file)
			{
				$trigger[basename($file)] = str_replace('.json', '', basename($file));
			}
		}

		return $trigger;
	}

	public function getVariables()
	{
		if (!isset($this->data['variable']))
		{
			return [];
		}

		$variable = $this->data['variable'];
		if (is_string($variable) && $variable === '*')
		{
			$variable = [];
			foreach (glob(ROOT_DIR . 'data/variable/*.json') as $file)
			{
				$variable[basename($file)] = str_replace('.json', '', basename($file));
			}
		}

		return $variable;
	}

}