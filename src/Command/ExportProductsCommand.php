<?php

    namespace App\Command;

    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use App\Entity\Product;


    class ExportProductsCommand extends ContainerAwareCommand
    {
        const ROOT_DIR = '/var/www/symfony/';

        protected function configure()
        {
            $this
                ->setName('app:export-products')
                ->setDescription('Creates file with products list')
                ->setHelp('This command allows you to create a file with products...')
                ->addArgument('filename', InputArgument::REQUIRED, 'Choose file to save products')
                ->addArgument('productIds', InputArgument::IS_ARRAY, 'Select products to exports')
            ;
        }

        /**
         * @param InputInterface $input
         * @param OutputInterface $output
         * @return int|null|void
         */
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $filename = $input->getArgument('filename');
            $productIds = $input->getArgument('productIds');
            $productRepo = $this->getContainer()->get('doctrine')->getEntityManager()->getRepository(Product::class);

            $products = null == $productIds ? $productRepo->findAll() : $productRepo->findBy(['id' => $productIds]);

            $handler = fopen(self::ROOT_DIR . $filename . '.csv', 'w+');
            fputcsv($handler, ['Id', 'Name', 'Price'], ';');

            foreach ($products as $product) {
                fputcsv($handler, [$product->getId(), $product->getName(), $product->getPrice()],';', chr(127));
            }

            fclose($handler);
            $output->writeln('Success! Exports complited!');
        }
    }