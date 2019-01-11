<?php

namespace App\Controller;

use App\Entity\PullRequest;
use App\Form\PullRequestType;
use App\Repository\PullRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/pull/request")
 */
class PullRequestController extends AbstractController
{
    /**
     * @Route("/", name="pull_request_index", methods={"GET"})
     */
    public function index(PullRequestRepository $pullRequestRepository): Response
    {
        return $this->render('pull_request/index.html.twig', [
            'pull_requests' => $pullRequestRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pull_request_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pullRequest = new PullRequest();
        $form = $this->createForm(PullRequestType::class, $pullRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pullRequest);
            $entityManager->flush();

            return $this->redirectToRoute('pull_request_index');
        }

        return $this->render('pull_request/new.html.twig', [
            'pull_request' => $pullRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pull_request_show", methods={"GET"})
     */
    public function show(PullRequest $pullRequest): Response
    {
        return $this->render('pull_request/show.html.twig', [
            'pull_request' => $pullRequest,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pull_request_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PullRequest $pullRequest): Response
    {
        $form = $this->createForm(PullRequestType::class, $pullRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pull_request_index', [
                'id' => $pullRequest->getId(),
            ]);
        }

        return $this->render('pull_request/edit.html.twig', [
            'pull_request' => $pullRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pull_request_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PullRequest $pullRequest): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pullRequest->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pullRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pull_request_index');
    }

    /**
     * @Route("/{id}/{transactionName}/next_flow", name="pull_request_flow")
     */
    public function nextWorkflow(PullRequest $pullRequest, Registry $workflows, $transactionName): Response {
        $workflow = $workflows->get($pullRequest, 'pull_request');

        if ($workflow->can($pullRequest, $transactionName)) {
            try {
                $workflow->apply($pullRequest, $transactionName);
                $this->getDoctrine()->getManager()->merge($pullRequest);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'changes submitted');
            } catch (TransitionException $exception) {
                //
            }

            return $this->redirectToRoute('pull_request_index');
        }
    }
}
