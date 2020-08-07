<?php


namespace App\Controller;


use App\Entity\Agenda;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AgendaController extends AbstractController
{
    /**
     * @Route("/api/getAllRendezVous", methods="GET")
     */
    public function getAllRendezVous()
    {
        $i = 0;
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->findAll();
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['Date']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        while ($i < count($jsonContent))
        {
            $jsonDateContent = $serializer->normalize(
                $data[$i]->getDate(),
                null,
                array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d h:m:s'
                ));
            $jsonContent[$i]['Date'] = $jsonDateContent;
            $i++;
        }
        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/getTheThreeLastRendezVous", methods="GET")
     */
    public function getTheThreeLastRendezVous()
    {
        $i = 0;
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->findTheThreeLastRendezVous();
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['Date']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        while ($i < count($jsonContent))
        {
            $jsonDateContent = $serializer->normalize(
                $data[$i]->getDate(),
                null,
                array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d h:m:s'
                ));
            $jsonContent[$i]['Date'] = $jsonDateContent;
            $i++;
        }

        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/getOneRendezVous/{id}", methods="GET")
     */
    public function getOneRendezVousByID($id)
    {
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->find($id);
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['Date']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        $jsonDateContent = $serializer->normalize(
            $data->getDate(),
            null,
            array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d h:m:s'
            ));
        $jsonContent['Date'] = $jsonDateContent;

        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/addOneRendezVous", methods="POST")
     */
    public function addOneRendezVous(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $newAgenda = new Agenda();

        $newAgenda->setName($request->request->get('name'));
        $newAgenda->setDate(new \DateTime($request->request->get('date')));
        $newAgenda->setDuration($request->request->get('duration'));
        $newAgenda->setDescription($request->request->get('description'));
        $em->persist($newAgenda);
        $em->flush();

        return new Response("Votre rendez-vous a bien été ajouté.", 200);
    }

    //REMOVE IF FOREST WHEN POSSIBLE
    /**
     * @Route("/api/updateOneRendezVous/{id}", methods="PUT")
     */
    public function updateOneRendezVous(Request $request, $id)
    {
        $i = 0;
        $em = $this->getDoctrine()->getManager();
        $agenda = $em->getRepository(Agenda::class)->find($id);
        $datas = json_decode($request->getContent(), true);

        if(empty($agenda))
        {
            return new JsonResponse(['message' => 'Agenda non trouvé'], Response::HTTP_NOT_FOUND);
        }
        if ($datas['name'])
        {
            $agenda->setName($datas['name']);
        }
        if ($datas['date'])
        {
            $agenda->setDescription($datas['description']);
        }
        if ($datas['duration'])
        {
            $agenda->setName($datas['name']);
        }
        if ($datas['description'])
        {
            $agenda->setDescription($datas['description']);
        }
        $em->flush();

        return new Response("Votre rendez-vous a bien été mis à jour.", 200);
    }

    /**
     * @Route("/api/removeOneRendezVous/{id}", methods="DELETE")
     */
    public function removeOneRendezVous($id)
    {
        $em = $this->getDoctrine()->getManager();

        $delAgenda = $em->getRepository(Agenda::class)
            ->find($id);
        $em->remove($delAgenda);
        $em->flush();

        return new Response("Votre rendez-vous a bien été supprimé.", 200);
    }
}