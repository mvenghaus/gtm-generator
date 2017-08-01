<?php

namespace Builder;

use Reader\ConfigReader;
use Resolver\FolderResolver;

class FolderBuilder
{
	/** @var FolderResolver */
	private $folderResolver;

	/**
	 * @param FolderResolver $folderResolver
	 */
	public function __construct(FolderResolver $folderResolver)
	{
		$this->folderResolver = $folderResolver;
	}

	public function build(ConfigReader $configReader)
	{
		$folders = [];

		foreach ($this->folderResolver->getAll() as $key => $name)
		{
			$folders[] = [
				'accountId' => '',
				'containerId' => '',
				'folderId' => ($key + 1),
				'name' => $name,
				'fingerprint' => ''
			];
		}

		return $folders;
	}

}