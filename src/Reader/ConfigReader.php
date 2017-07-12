<?php

namespace Reader;

class ConfigReader
{

	private $data = [];

	public function __construct($file)
	{
		$this->data = json_decode(file_get_contents($file), true);
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
		return (isset($this->data['tag']) ? $this->data['tag'] : []);
	}

	public function getTrigger()
	{
		return (isset($this->data['trigger']) ? $this->data['trigger'] : []);
	}

	public function getVariables()
	{
		return (isset($this->data['variable']) ? $this->data['variable'] : []);
	}

}