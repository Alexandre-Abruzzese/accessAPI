<?php


namespace App\Controller;


use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TaskController extends AbstractController
{
    /**
     * @Route("/api/allTask", methods="GET")
     */
    public function getAllTask()
    {
        $i = 0;
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findAll();
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['deadline']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        while ($i < count($jsonContent))
        {
            $jsonDateContent = $serializer->normalize(
                $data[$i]->getDeadline(),
                null,
                array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
                ));
            $jsonContent[$i]['deadline'] = $jsonDateContent;
            $i++;
        }
        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/getTheThreeLastTask", methods="GET")
     */
    public function getTheThreeLastTask()
    {
        $i = 0;
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findTheThreeLastTask();
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['deadline']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        while ($i < count($jsonContent))
        {
            $jsonDateContent = $serializer->normalize(
                $data[$i]->getDeadline(),
                null,
                array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
                ));
            $jsonContent[$i]['deadline'] = $jsonDateContent;
            $i++;
        }

        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/getOneTask/{id}", methods="GET")
     */
    public function getOneTaskByID($id)
    {
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);

        $data = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($id);
        $jsonContent = $serializer->normalize($data, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['deadline']]);
        $serializer = new Serializer(array(new DateTimeNormalizer()));
        $jsonDateContent = $serializer->normalize(
            $data->getDeadline(),
            null,
            array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
            ));
        $jsonContent['deadline'] = $jsonDateContent;

        return new JsonResponse($jsonContent, 200);
    }

    /**
     * @Route("/api/addOneTask", methods="POST")
     */
    public function addOneTask(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $newTask = new Task();

        $newTask->setTitle($request->request->get('title'));
        $newTask->setDescription($request->request->get('description'));
        $newTask->setPriority($request->request->get('priority'));
        $newTask->setAssign($request->request->get('assign'));
        $newTask->setEstimate($request->request->get('estimate'));
        $newTask->setClient($request->request->get('client'));
        $newTask->setProject($request->request->get('project'));
        $newTask->setAttach($request->request->get('attach'));
        $newTask->setDeadline(new \DateTime($request->request->get('deadline')));
        $em->persist($newTask);
        $em->flush();

        return new Response("Votre tâche a bien été créer.", 200);
    }

    /**
     * @Route("/api/updateOneTache/{id}", methods="PUT")
     */
    public function updateOneTask(Request $request, $id)
    {
        $i = 0;
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        $datas = $request->getContent();

        if(empty($task))
        {
            return new JsonResponse(['message' => 'Tâche non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $jsonContent = $serializer->deserialize($datas, Task::class,'json');
        $em->persist($jsonContent);
        $em->flush();

        return new Response("Votre tâche a bien été mis à jour.", 200);
    }

    /**
     * @Route("/api/removeOneTask/{id}", methods="DELETE")
     */
    public function removeOneTask($id)
    {
        $em = $this->getDoctrine()->getManager();

        $delTask = $em->getRepository(Task::class)
            ->find($id);
        $em->remove($delTask);
        $em->flush();

        return new Response("Votre tâche a bien été supprimé.", 200);
    }
}