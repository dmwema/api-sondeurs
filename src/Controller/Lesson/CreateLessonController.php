<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Lesson;
use App\Entity\User;
use App\Repository\Lesson\CategoryRepository;
use App\Repository\Lesson\LessonRepository;
use App\Repository\UserRepository;
use App\Services\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateLessonController extends AbstractController
{
    public function __construct(
        private readonly FileUploadService $fileUploader,
        private readonly LessonRepository $lessonRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly UserRepository $userRepository,
    ){}

    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
//        if ($user === null) {
//            throw $this->createAccessDeniedException();
//        }

        $title = $request->get('title');
        $description = $request->get('description');
        $categoryId = $request->get('category');

        $image = $request->files->get('image');
        $audio = $request->files->get('audio');

        $imagePath = $this->fileUploader->uploadFile($image);
        $audioPath = $this->fileUploader->uploadFile($audio, '/lessons/audios');

        $lesson = null;

        if ($audioPath && $imagePath) {
            $category = $this->categoryRepository->find((int)$categoryId);
            $lesson = (new Lesson())
                ->setTitle($title)
                ->setDescription($description)
                ->setCategory($category)
                ->setImagePath($imagePath)
                ->setAudioPath($audioPath)
                ->setAuthor($this->userRepository->find(2))
            ;
            $this->lessonRepository->save($lesson, true);
        }
        return new JsonResponse($lesson ?? [], $lesson == null ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK);
    }
}