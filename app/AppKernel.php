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
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // fos user bundle
            new FOS\UserBundle\FOSUserBundle(),

            // pdf bundle
            new Ensepar\Html2pdfBundle\EnseparHtml2pdfBundle(),
            
            // utility bundles
            new Catalyst\GridBundle\CatalystGridBundle(),
            new Catalyst\LogBundle\CatalystLogBundle(),
            new Catalyst\GalleryBundle\CatalystGalleryBundle(),
            new Catalyst\ChartBundle\CatalystChartBundle(),

            // core modules
            new Catalyst\MenuBundle\CatalystMenuBundle(),
            new Catalyst\ConfigurationBundle\CatalystConfigurationBundle(),
            new Catalyst\UserBundle\CatalystUserBundle(),
            new Catalyst\AdminBundle\CatalystAdminBundle(),
            new Catalyst\TemplateBundle\CatalystTemplateBundle(),
            new Catalyst\DashboardBundle\CatalystDashboardBundle(),
            new Catalyst\PdfBundle\CatalystPdfBundle(),


            // erp modules
            new Catalyst\InventoryBundle\CatalystInventoryBundle(),
            new Catalyst\ManufacturingBundle\CatalystManufacturingBundle(),
            new Catalyst\PurchasingBundle\CatalystPurchasingBundle(),
            new Catalyst\SalesBundle\CatalystSalesBundle(),
            new Catalyst\ServiceBundle\CatalystServiceBundle(),
            new Catalyst\CoreBundle\CatalystCoreBundle(),
            new Catalyst\ContactBundle\CatalystContactBundle(),
            new Catalyst\MediaBundle\CatalystMediaBundle(),
            new Catalyst\AccountingBundle\CatalystAccountingBundle(),
            new Catalyst\NotesBundle\CatalystNotesBundle(),

            //fareast modules,
            new Fareast\TemplateBundle\FareastTemplateBundle(),
            new Fareast\AccountingBundle\FareastAccountingBundle(),
            new Fareast\SalesBundle\FareastSalesBundle(),
            new Fareast\ReceivingBundle\FareastReceivingBundle(),
            new Fareast\InventoryBundle\FareastInventoryBundle(),
            new Fareast\PurchasingBundle\FareastPurchasingBundle(),
            new Fareast\SettingsBundle\FareastSettingsBundle(),
            new Fareast\ManufacturingBundle\FareastManufacturingBundle(),
            new Fareast\ReportBundle\FareastReportBundle(),
            new Fareast\PdfBundle\FareastPdfBundle(),
            new Fareast\DashboardBundle\FareastDashboardBundle(),
            new Fareast\FixtureBundle\FareastFixtureBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
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
