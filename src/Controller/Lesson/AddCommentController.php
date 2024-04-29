<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Comment;
use App\Entity\Lesson\Lesson;
use App\Entity\User;
use App\Repository\Lesson\CommentRepository;
use App\Services\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCommentController extends AbstractController
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
        private readonly CommentRepository $commentRepository,

    ) {}

    public function __invoke(Lesson $lesson, Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user === null) {
            throw $this->createAccessDeniedException();
        }

        $image = $request->files->get('image');
        $audio = $request->files->get('audio');
        $message = $request->get('message');
        $date = new \DateTime();

        $comment = (new Comment())
            ->setLesson($lesson)
            ->setAuthor($user)
            ->setDate($date)
        ;

        if (!empty($image)) {
            $comment->setImagePath($this->fileUploadService->uploadFile($image));
        }

        if (!empty($audio)) {
            $comment->setAudioPath($this->fileUploadService->uploadFile($audio, '/lessons/audios'));
        }

        if (!empty($message)) {
            $comment->setMessage($message);
        }

        $this->commentRepository->save($comment, true);
        return new JsonResponse([], Response::HTTP_OK);

    }
}