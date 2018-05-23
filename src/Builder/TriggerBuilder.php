<?php

namespace Builder;

use Filter\FolderFilter;
use Filter\VariableFilter;
use Reader\ConfigReader;
use Resolver\TriggerResolver;

class TriggerBuilder
{
    /** @var TriggerResolver */
    private $triggerResolver;
    /** @var VariableFilter */
    private $variableFilter;
    /** @var FolderFilter */
    private $folderFilter;

    /**
     * @param TriggerResolver $triggerResolver
     * @param VariableFilter $variableFilter
     * @param FolderFilter $folderFilter
     */
    public function __construct(TriggerResolver $triggerResolver,
                                VariableFilter $variableFilter,
                                FolderFilter $folderFilter)
    {
        $this->triggerResolver = $triggerResolver;
        $this->variableFilter = $variableFilter;
        $this->folderFilter = $folderFilter;
    }

    public function build(ConfigReader $configReader)
    {
        $triggers = [];

        $triggerId = 1;
        foreach ($this->getTrigger($configReader) as $triggerFile => $data)
        {
            $triggerContent = file_get_contents($triggerFile);

            $triggerContent = $this->variableFilter->filter($triggerContent, $data);
            $triggerContent = $this->folderFilter->filter($triggerContent);

            $trigger = json_decode($triggerContent, true);
            $trigger['triggerId'] = $triggerId++;

            $this->triggerResolver->add($trigger['triggerId'], $trigger['name']);
            $triggers[] = $trigger;

            // create cookie opt event trigger
            if ($trigger['type'] == 'PAGEVIEW')
            {
                $trigger['triggerId'] = $triggerId++;
                $trigger['type'] = 'CUSTOM_EVENT';
                $trigger['name'] .= ' - Cookie OptIn Event';
                $trigger['customEventFilter'] = [
                    [
                        'type' => "EQUALS",
                        'parameter' => [
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg1',
                                'value' => 'cookieOptIn'
                            ]
                        ]
                    ]
                ];

                $this->triggerResolver->add($trigger['triggerId'], $trigger['name']);

                $triggers[] = $trigger;
            }
        }

        foreach ($this->getCustomTrigger($configReader) as $triggerKey => $data)
        {

            list($triggerFile, $triggerName) = explode('#', $triggerKey);

            $triggerContent = file_get_contents($triggerFile);

            $vars = $data['vars'] ?? [];
            $vars['Trigger Name'] = $triggerName;

            $triggerContent = $this->variableFilter->filter($triggerContent, $vars);
            $triggerContent = $this->folderFilter->filter($triggerContent);

            $trigger = json_decode($triggerContent, true);
            $trigger['triggerId'] = $triggerId++;

            $this->triggerResolver->add($trigger['triggerId'], $triggerName);

            $triggers[] = $trigger;
        }

        return $triggers;
    }

    public function getTrigger(ConfigReader $configReader)
    {
        $trigger = [];

        foreach ($configReader->getTrigger() as $name => $data)
        {
            $triggerFile = sprintf('%sdata/trigger/%s.json', ROOT_DIR, $name);
            if (!file_exists($triggerFile))
            {
                throw new \Exception(sprintf('trigger file not found "%s"', $triggerFile));
            }

            $trigger[$triggerFile] = $data;
        }

        return $trigger;
    }

    public function getCustomTrigger(ConfigReader $configReader)
    {
        $trigger = [];

        foreach ($configReader->getCustomTrigger() as $data)
        {
            $template = $data['template'] ?? null;
            $triggerFile = sprintf('%sdata/triggerTemplates/%s.json', ROOT_DIR, $template);
            if (!file_exists($triggerFile))
            {
                throw new \Exception(sprintf('custom trigger file not found "%s"', $triggerFile));
            }

            $key = sprintf('%s#%s', $triggerFile, $data['name']);

            $trigger[$key] = $data;
        }

        return $trigger;
    }

}