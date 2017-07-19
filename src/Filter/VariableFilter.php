<?php

namespace Filter;

class VariableFilter
{

	public function filter($content, $variables)
	{
		foreach ($variables as $key => $value)
		{
			$content = str_replace(sprintf('{{%s}}', $key), $value, $content);
		}

		return $content;
	}

}