<?php

namespace App\DataFixtures;

use App\Entity\Lesson\Category;
use App\Entity\Lesson\Lesson;
use App\Entity\User;
use App\Repository\Lesson\CategoryRepository;
use App\Repository\Lesson\LessonRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly UserRepository $userRepository,
        private readonly CategoryRepository $categoryRepository,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $user = (new User())
            ->setFirstname("Daniel")
            ->setLastname("Mwema")
            ->setEmail("danielmwemakapwe@gmail.com")
        ;

        $this->userRepository->save($user);
        $user->setPassword($this->passwordEncoder->hashPassword($user, "123456"));

        $this->userRepository->save($user);

        for ($i = 0; $i < 4; $i++) {
            $category = (new Category())
                ->setName($faker->jobTitle())
                ->setImagePath($faker->imageUrl())
            ;
            for ($j = 0; $j < 5; $j++) {
                $category
                    ->addLesson(
                        (new Lesson())
                            ->setAuthor($user)
                            ->setAudioPath("https://s3.amazonaws.com/scifri-episodes/scifri20181123-episode.mp3")
                            ->setImagePath($faker->imageUrl())
                            ->setTitle($faker->jobTitle())
                            ->setdescription($faker->realText())
                    )
                ;
            }

            $this->categoryRepository->save($category);

        }

        $manager->flush();
    }
}
