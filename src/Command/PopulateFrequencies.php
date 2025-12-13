<?php

namespace App\Command;

use App\Entity\Frequency;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:populate-frequencies', description: 'Populates the frequencies database with sample data')]
class PopulateFrequencies extends Command
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int

    {
        echo "Populating frequencies...\n";
        $frequencies = [
            "Une fois par jour",
            "Deux fois par jour",
            "Trois fois par jour",
            "Quatre fois par jour",
            "Toutes les 4 heures",
            "Toutes les 6 heures",
            "Toutes les 8 heures",
            "Toutes les 12 heures",
            "Le matin",
            "Le matin et le soir",
            "Le soir",
            "Avant le repas",
            "Après le repas",
            "Au besoin / si nécessaire",
            "En continu"
        ];

        foreach ($frequencies as $f) {
            $frequency = new Frequency();
            $frequency->setName($f);
            $this->em->persist($frequency);
            $output->writeln("Added product: " . $f);
        }
        $this->em->flush();
        return Command::SUCCESS;

    }
}
