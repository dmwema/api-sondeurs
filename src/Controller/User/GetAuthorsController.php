<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user === null) {
            throw new UnauthorizedHttpException("Vous devez vous connecter");
        }
        $authors = $this->userRepository->findAuthors($user);
        $response = $this->normalizer->normalize($authors, null, ['groups' => 'users.pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}