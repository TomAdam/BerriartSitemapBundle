<?php

namespace Berriart\Bundle\SitemapBundle\Command;

/**
 * This file is part of the BerriartSitemapBundle package what is based on the
 * AvalancheSitemapBundle
 *
 * (c) Bulat Shakirzyanov <avalanche123.com>
 * (c) Alberto Varela <alberto@berriart.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class PopulateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('berriart:sitemap:populate')
            ->setDescription('Populate url database, using url providers.')
            ->addOption('purge-existing', null, InputOption::VALUE_NONE, 'Purge existing sitemap entries');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sitemap = $this->getContainer()->get('berriart_sitemap');

        // purge existing entries
        if ($input->getOption('purge-existing')) {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $em
                ->createQuery('DELETE FROM Berriart\Bundle\SitemapBundle\Entity\ImageUrl')
                ->execute();
            $em
                ->createQuery('DELETE FROM Berriart\Bundle\SitemapBundle\Entity\Url')
                ->execute();
            $em->flush();
        }

        $this->getContainer()->get('berriart_sitemap.provider.chain')->populate($sitemap);

        $output->write('<info>Sitemap was sucessfully populated!</info>', true);
    }
}