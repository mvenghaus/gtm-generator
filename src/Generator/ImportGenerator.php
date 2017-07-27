<?php

namespace Generator;

use Builder\TagBuilder;
use Builder\TriggerBuilder;
use Builder\VariableBuilder;
use Reader\ConfigReader;

class ImportGenerator
{
	/** @var TriggerBuilder */
	private $triggerBuilder;
	/** @var TagBuilder */
	private $tagBuilder;
	/** @var VariableBuilder */
	private $variableBuilder;

	/**
	 * @param TriggerBuilder $triggerBuilder
	 * @param TagBuilder $tagBuilder
	 * @param VariableBuilder $variableBuilder
	 */
	public function __construct(TriggerBuilder $triggerBuilder,
	                            TagBuilder $tagBuilder,
	                            VariableBuilder $variableBuilder)
	{
		$this->triggerBuilder = $triggerBuilder;
		$this->tagBuilder = $tagBuilder;
		$this->variableBuilder = $variableBuilder;
	}

	/**
	 * @param string $configFile
	 * @param string $outputFile
	 */
	public function generate($configFile, $outputFile)
	{
		$configReader = new ConfigReader($configFile);

		try {

			$data = $this->loadBaseData();
			$data['containerVersion']['trigger'] = $this->triggerBuilder->build($configReader);
			$data['containerVersion']['tag'] = $this->tagBuilder->build($configReader);
			$data['containerVersion']['variable'] = $this->variableBuilder->build($configReader);

			$jsonData = json_encode($data, JSON_PRETTY_PRINT);

			$jsonData = $this->replaceBaseVariable($jsonData, $configReader);

			file_put_contents($outputFile, $jsonData);

			echo $jsonData;

		} catch (\Exception $e)
		{
			echo $e->getMessage();
		}
	}

	/**
	 * @return array
	 */
	private function loadBaseData()
	{
		return json_decode(file_get_contents(ROOT_DIR . 'data/base.json'), true);
	}

	private function replaceBaseVariable($jsonData, ConfigReader $configReader)
	{
		return strtr($jsonData, [
			'"publicId": ""' => '"publicId": "0"',
			'"accountId": ""' => '"accountId": "0"',
			'"containerId": ""' => '"containerId": "0"'
		]);

	}

}