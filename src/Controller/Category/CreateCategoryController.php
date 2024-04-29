<?php

namespace App\Controller\Category;

use App\Entity\Lesson\Category;
use App\Entity\User;
use App\Repository\Lesson\CategoryRepository;
use App\Services\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateCategoryController extends AbstractController
{
    public function __construct(
        private readonly FileUploadService $fileUploader,
        private readonly CategoryRepository $categoryRepository,
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

        $name = $request->get('name');

        $image = $request->files->get('image');

        $imagePath = $this->fileUploader->uploadFile($image);

        $category = null;

        if ($imagePath) {
            $category = (new Category())
                ->setName($name)
                ->setImagePath($imagePath, '/categories/images')
            ;
            $this->categoryRepository->save($category, true);
        }
        return new JsonResponse($category ?? [], $category == null ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK);
    }
}