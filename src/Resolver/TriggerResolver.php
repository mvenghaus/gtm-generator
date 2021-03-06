<?php

namespace Resolver;

class TriggerResolver
{

	private $idToName = [];
	private $nameToId = [];

	public function add($id, $name)
	{
		$this->idToName[$id] = $name;
		$this->nameToId[$name] = $id;

		return $this;
	}

	public function resolveNameById($id, $default = null)
	{
		return (isset($this->idToName[$id]) ? $this->idToName[$id] : $default);
	}

	public function resolveIdByName($name, $default = null)
	{
		return (isset($this->nameToId[$name]) ? $this->nameToId[$name] : $default);
	}

}