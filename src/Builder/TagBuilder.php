<?php

namespace Builder;

use Filter\FolderFilter;
use Reader\ConfigReader;
use Filter\TriggerFilter;
use Filter\VariableFilter;
use Resolver\TriggerResolver;

class TagBuilder
{
    /** @var VariableFilter */
    private $variableFilter;
    /** @var TriggerFilter */
    private $triggerFilter;
    /** @var FolderFilter */
    private $folderFilter;
    /** @var TriggerResolver */
    private $triggerResolver;

    /**
     * @param VariableFilter $variableFilter
     * @param TriggerFilter $triggerFilter
     * @param FolderFilter $folderFilter
     * @param TriggerResolver $triggerResolver
     */
    public function __construct(VariableFilter $variableFilter,
                                TriggerFilter $triggerFilter,
                                FolderFilter $folderFilter,
                                TriggerResolver $triggerResolver)
    {
        $this->variableFilter = $variableFilter;
        $this->triggerFilter = $triggerFilter;
        $this->folderFilter = $folderFilter;
        $this->triggerResolver = $triggerResolver;
    }

    public function build(ConfigReader $configReader)
    {
        $tags = [];

        $tagId = 1;
        foreach ($this->getTags($configReader) as $tagFile => $data)
        {

            $tagsContent = file_get_contents($tagFile);

            list($tagGroup) = explode('.', basename($tagFile));

            $tagList = json_decode($tagsContent, true);
            if (isset($tagList['accountId']))
            {
                $tagList = [$tagList];
            }
            foreach ($tagList as $tag)
            {
                try
                {
                    $tag = $this->handleOptInTags($tagGroup, $tag, $configReader);

                    $tagContent = json_encode($tag);

                    $tagContent = $this->triggerFilter->filter($tagContent);
                    $tagContent = $this->variableFilter->filter($tagContent, $data);
                    $tagContent = $this->folderFilter->filter($tagContent);

                    $tag = json_decode($tagContent, true);

                    $tag['tagId'] = $tagId++;

                    $tags[] = $tag;
                } catch (\Exception $e)
                {
                    echo $tag['name'] . ' ignored -> ' . $e->getMessage() . PHP_EOL;
                }
            }
        }

        return $tags;
    }

    private function getTags(ConfigReader $configReader)
    {
        $tags = [];

        foreach ($configReader->getTags() as $name => $data)
        {
            $tagFile = sprintf('%sdata/tag/%s/%s.json', ROOT_DIR, $name, $name);
            if (!file_exists($tagFile))
            {
                throw new \Exception(sprintf('tag file not found "%s.json"', $name));
            }

            $tags[$tagFile] = $data;
        }

        return $tags;
    }

    private function handleOptInTags($tagGroup, $tag, ConfigReader $configReader)
    {
        $optInTags = $configReader->getOptInTags();
        if (in_array($tagGroup, $optInTags))
        {
            $triggerName = strtr(current($tag['firingTriggerId']), ['<<TRIGGER ' => '', '>>' => '']) . ' - Cookie OptIn Event';

            $triggerId = $this->triggerResolver->resolveIdByName($triggerName);
            if ($triggerId)
            {
                $tag['firingTriggerId'][] = '<<TRIGGER ' . $triggerName . '>>';
            }

            if (!isset($tag['blockingTriggerId'])) $tag['blockingTriggerId'] = [];
            $tag['blockingTriggerId'][] = '<<TRIGGER Cookie Optin Missing>>';
        }

        return $tag;
    }

}