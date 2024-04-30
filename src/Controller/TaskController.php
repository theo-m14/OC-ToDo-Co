<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list',methods:['GET'])]
    public function list(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }

    #[Route('/tasks/create', name: 'task_create',methods:['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setCreatedAt(new \DateTime());
            $task->setOwner($this->getUser());
            $task->setIsDone(false);
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit',methods:['GET','POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle',methods:['GET','POST'])]
    public function toggleTask(Task $task,EntityManagerInterface $em): Response
    {
        $task->setIsDone(!$task->getIsDone());
        $em->persist($task);
        $em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete',methods:['GET'])]
    public function deleteTask(Task $task,EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!$this->testAsRightToDelete($task,$user)){
            $this->addFlash('error', 'Vous devez être propiétaire de la tâche pour la supprimer');
            return $this->redirectToRoute('task_list');
        }

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    private function testAsRightToDelete(Task $task,User $user) : bool
    {
        if(!$task->getOwner() && !in_array('ROLE_ADMIN', $user->getRoles())){
            return false;
        }

        if($task->getOwner() && $task->getOwner() != $user){
            return false;
        }

        return true;
    }
}
