<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:populate-products', description: 'Populates the product database with sample data')]
class PopulateProducts extends Command
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int

    {
        echo "Populating products...\n";
        $medicaments = [
            "Paracétamol",
            "Ibuprofène",
            "Aspirine",
            "Diclofénac gel",
            "Gaviscon",
            "Rennie",
            "Smecta",
            "Imodium",
            "Forlax",
            "Dulcolax",
            "Sels de réhydratation orale",
            "Biodramina",
            "Dextrométhorphane",
            "Mucosolvan",
            "Fluimucil",
            "Otrivine",
            "Spray nasal saline",
            "Cétirizine",
            "Loratadine",
            "Nicorette"
        ];


        foreach ($medicaments as $medicament) {
            $product = new Product();
            $product->setName($medicament);
            $this->em->persist($product);
            $output->writeln("Added product: " . $medicament);
        }
        $this->em->flush();
        return Command::SUCCESS;

    }
}
