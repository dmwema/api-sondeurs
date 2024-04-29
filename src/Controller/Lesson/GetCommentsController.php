<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Lesson;
use App\Repository\Lesson\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetCommentsController extends AbstractController
{
    public function __construct(
        CommentRepository $commentRepository,
    ) {}

    public function __invoke(Lesson $lesson): JsonResponse
    {
        $comments = $lesson->getComments();
        return new JsonResponse($comments, Response::HTTP_OK);
    }
}