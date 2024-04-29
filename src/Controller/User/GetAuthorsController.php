<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Couchbase\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetAuthorsController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $normalizer
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(): JsonResponse
    {
        $authors = $this->userRepository->findAuthors();

        $response = $this->normalizer->normalize($authors, null, ['groups' => 'users.pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}