<?php

    namespace App\Command;

    use App\Entity\Product;
    use League\Csv\Reader;
    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;


    class ImportProductsCommand extends ContainerAwareCommand
    {
        protected function configure()
        {
            $this
                ->setName('app:import-products')
                ->setDescription('Imports a CSV product file')
                ->setHelp('This command allows you to import a file with products...')
                ->addArgument('filename', InputArgument::REQUIRED, 'Choose file to import products')
            ;
        }


        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $filename = $input->getArgument('filename');
            $productsCsv = Reader::createFromPath($this->getContainer()->getParameter('root_directory') . DIRECTORY_SEPARATOR . $filename, 'r');
            $productsCsv->setDelimiter(';');
            $productsCsv->setHeaderOffset(0);
            $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
            $productIdsToUpdate = [];

            foreach ($productsCsv as $product) {
                $productId = $product['Id'];
                $productName = $product['Name'];
                $productPrice = $product['Price'];
                $productIds = $entityManager->getRepository(Product::class)->findProductIds();

                if (in_array($productId, $productIds)) {
                    $productIdsToUpdate[] = $productId;
                } else {
                    $product = new Product();
                    $product->setName($productName);
                    $product->setPrice($productPrice);
                    $entityManager->persist($product);
                    $entityManager->flush();
                }
            }

            $entityManager->getRepository(Product::class)->updateProductsFromCsvFile($productIdsToUpdate, $productsCsv);

            $output->writeln('Import Success!');
        }
    }