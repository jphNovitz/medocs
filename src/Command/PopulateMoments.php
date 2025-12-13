<?php

namespace App\Command;

use App\Entity\Moment;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:populate-moments', description: 'Populates the moments database with sample data')]
class PopulateMoments extends Command
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int

    {
        echo "Populating moments...\n";

        $moments = [
            "Au lever",
            "Le matin",
            "En matinée",
            "À midi",
            "En début d’après-midi",
            "En après-midi",
            "En fin d’après-midi",
            "Le soir",
            "En soirée",
            "Au coucher",
            "La nuit"
        ];


        foreach ($moments as $m) {
            $moment = new Moment();
            $moment->setName($m);
            $this->em->persist($moment);
            $output->writeln("Added product: " . $m);
        }
        $this->em->flush();
        return Command::SUCCESS;

    }
}
