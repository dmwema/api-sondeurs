<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Auth\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly  AuthService $authService,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly JWTTokenManagerInterface $jWTTokenManager,
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @throws \JsonException
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $success = false;
        $user = null;
        $token = null;

        $requiredKeys = [
            "email"       => "L'adresse E-mail est obligatoire.",
            "password"    => "Le mot de passe est obligatoire.",
            "address"    => "Le mot de passe est obligatoire.",
            "phone_number"    => "Le mot de passe est obligatoire.",
            "firstname"    => "Le prenom est obligatoire.",
            "lastname" => "Le nom est obligatoire.",
        ];

        $checkedFields = $this->authService->checkRequiredFields($requiredKeys, $content);
        $message   = $checkedFields['message'];
        $errorType = $checkedFields['errorType'];

        if ($message === null) {
            $email = $content['email'];
            $password = $content['password'];
            $firstname = $content['firstname'];
            $lastname = $content['lastname'];
            $address = $content['address'];
            $phoneNumber = $content['phone_number'];

            $userExists = $this->userRepository->findOneByEmail($email) !== null;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Adresse email invalide.";
                $errorType = $this->authService::$emailError;
            } elseif ($userExists) {
                $message = "Un compte existe deja avec cette meme adresse E-mail.";
                $errorType = $this->authService::$emailError;
            } else {
                $user = (new User())
                    ->setEmail($email)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setAddress($address)
                    ->setRole('user')
                ;

                $hashedPassword = $this->passwordEncoder->hashPassword($user, $password);
                $user->setpassword($hashedPassword);

                $this->userRepository->save($user, true);
                $token = $this->jWTTokenManager->create($user);
                $success = true;
                $message = "Inscription effectuee avec succès";
            }
        }

        $return = [
            'success' => $success,
            'message' => $message,
            'errorType' => $errorType,
            'token' => $token,
            'user' => $user === null ? null : $this->userRepository->find($user->getId()),
        ];

        $response = $this->normalizer->normalize($return, null, ['groups' => 'users.pRead']);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}