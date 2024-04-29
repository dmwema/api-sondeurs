<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Lesson;
use App\Repository\Lesson\LessonRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FindSimilarController extends AbstractController
{
    public function __construct(
        private readonly LessonRepository $lessonRepository,
        private readonly PaginatorInterface $paginator,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Lesson $data, Request $request): JsonResponse
    {
        $category = $data->getCategory();
        $similar = $this->lessonRepository->findSimilar($data, $this->paginator, $request->query->getInt('page', 1));

        $response = $this->normalizer->normalize($similar->getItems(), null, ['groups' => 'lesson.pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}