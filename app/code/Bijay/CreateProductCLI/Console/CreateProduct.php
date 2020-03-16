<?php

namespace Bijay\CreateProductCLI\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bijay\CreateProductCLI\Helper\Product;

class CreateProduct extends Command {
    protected $productHelper;

    public function __construct(Product $productHelper) {
        $this->productHelper = $productHelper;
        parent::__construct();
    }

    protected function configure() {
        $this ->setName('bijay:product:create')
              ->setDescription('Create new products')
              ->setDefinition($this->getOptionsList());
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('<info>Creating Products</info>');
        $this->productHelper->setData($input);
        $this->productHelper->execute();
        $output->writeln('<info>Product is created.</info>');
    }

    protected function getOptionsList() {
        return [
            new InputOption(product::ARG_COUNT, null, InputOption::VALUE_REQUIRED, '(Required) Count is required'),
        ];
    }
}