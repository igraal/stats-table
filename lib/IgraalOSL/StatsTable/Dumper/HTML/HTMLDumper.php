<?php

namespace IgraalOSL\StatsTable\Dumper\HTML;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\StatsTable;
use IgraalOSL\StatsTable\Tools\ParameterBag;

class HTMLDumper extends Dumper
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct($options = array())
    {
        $options = new ParameterBag($options);

        $this->template = $options->get('template', $this->getDefaultTemplate());
        $this->twig     = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/../../Resources/views'));
    }

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function dump(StatsTable $statsTable)
    {
        return $this->twig->render($this->template, array('statsTable' => $statsTable));
    }

    protected function getDefaultTemplate()
    {
        return 'statsTable.html.twig';
    }

    public function getMimeType()
    {
        return 'text/html; charset=utf-8';
    }
}
