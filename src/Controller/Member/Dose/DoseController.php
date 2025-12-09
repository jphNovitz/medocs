<?php
/**
 * @author novitz jean-philippe <bonjour@jphiweb.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Dose;

use App\Entity\Dose;
use App\Entity\Frequency;
use App\Entity\Moment;
use App\Form\DeleteFormType;
use App\Form\DoseType;
use App\Repository\DoseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/member/dose')]
class DoseController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {}

    #[Route('/api/new', name: 'member_dose_api_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $dose = new Dose();
        if ($data["dose[frequency]"] == "autre") {
            $frequency = new Frequency();
            $frequency->setName($data["dose[frequencyNew][name]"]);
            $this->em->persist($frequency);
            $dose->setFrequency($frequency);
        } else {
            $frequency = $this->em->getRepository(Frequency::class)->find($data["dose[frequency]"]);
            $dose->setFrequency($frequency);
        }

        if ($data["dose[moment]"] == "autre") {
            $moment = new Moment();
            $moment->setName($data["dose[momentNew][name]"]);
            $this->em->persist($moment);
            $dose->setMoment($moment);
        } else {
            $moment = $this->em->getRepository(Moment::class)->find($data["dose[moment]"]);
            $dose->setMoment($moment);
        }

        $this->em->persist($dose);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Données enregistrées avec succès.',
            'data' => [
                'id' => $dose->getId(),
                'name' => $dose->getName()
            ]
        ], 200);

    }



}
