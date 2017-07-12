<?php

namespace Reader;

class ConfigReader
{

	private $data = [];

	public function __construct($file)
	{
		$this->data = json_decode(file_get_contents($file), true);
	}

	public function getPublicId()
	{
		return (isset($this->data['publicId']) ? $this->data['publicId'] : '');
	}

	public function getAccountId()
	{
		return (isset($this->data['accountId']) ? $this->data['accountId'] : '');
	}

	public function getContainerId()
	{
		return (isset($this->data['containerId']) ? $this->data['containerId'] : '');
	}

	public function getTags()
	{
		if (!isset($this->data['tag']))
		{
			return [];
		}

		$tag = $this->data['tag'];
		if (is_string($tag) && $tag === '*')
		{
			$tag = [];
			foreach (glob(ROOT_DIR . 'data/tag/*.json') as $file)
			{
				$tag[str_replace('.json', '', basename($file))] = [];
			}
		}

		return $tag;
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
				$trigger[str_replace('.json', '', basename($file))] = [];
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
				$variable[str_replace('.json', '', basename($file))] = [];
			}
		}

		return $variable;
	}

}