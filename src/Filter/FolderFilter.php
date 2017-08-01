<?php

namespace Filter;

use Resolver\FolderResolver;

class FolderFilter
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

	public function filter($content)
	{
		preg_match_all('/<<FOLDER ([^>]+)>>/is', $content, $results);
		if (isset($results[1]))
		{
			foreach ($results[1] as $folderName)
			{
				$folderId = $this->folderResolver->createOrGetId($folderName);

				$content = str_replace(sprintf('<<FOLDER %s>>', $folderName), $folderId, $content);
			}
		}

		return $content;
	}


}