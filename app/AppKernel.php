<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Thiktak\CoreBundle\ThiktakCoreBundle(),
            new Jordo\UserBundle\JordoUserBundle(),
            new Thiktak\UserBundle\ThiktakUserBundle(),
            new Jordo\ProjectBundle\JordoProjectBundle(),
            new Jordo\ContactBundle\JordoContactBundle(),
            new Jordo\CalendarBundle\JordoCalendarBundle(),
            new Thiktak\CommentBundle\ThiktakCommentBundle(),
            new Jordo\StatsBundle\JordoStatsBundle(),
            new Jordo\NotifyBundle\JordoNotifyBundle(),
            new Jordo\PrintBundle\JordoPrintBundle(),
            new Jordo\GanttBundle\JordoGanttBundle(),
            new Jordo\ReportBundle\JordoReportBundle(),
            new Jordo\DocumentBundle\JordoDocumentBundle(),
            new Jordo\SearchBundle\JordoSearchBundle(),
            new Thiktak\SearchBundle\ThiktakSearchBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
