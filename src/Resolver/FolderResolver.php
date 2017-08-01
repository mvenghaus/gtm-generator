<?php

namespace Resolver;

class FolderResolver
{
	private $folders = [];

	public function createOrGetId($name)
	{
		$key = array_search($name, $this->folders);
		if ($key !== false)
		{
			return ($key + 1);
		}

		$this->folders[] = $name;

		return count($this->folders);
	}

	public function getAll()
	{
		return $this->folders;
	}

}