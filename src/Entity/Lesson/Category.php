<?php

namespace App\Entity\Lesson;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Category\CreateCategoryController;
use App\Repository\Lesson\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: 'categories/{id}',
            normalizationContext: [
                "groups" => ["category.fRead", "lesson.pRead"]
            ],
            name: 'categories.get'
        ),
        new Post(
            uriTemplate: 'categories',
            controller: CreateCategoryController::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'image' => [
                                        'type' => 'string',
                                        'format' =>  'binary'
                                    ],
                                    'name' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            deserialize: false,
            name: 'categories.post',
        ),
        new Delete(
            uriTemplate: 'categories/{id}',
            name: 'categories.delete',
        ),
        new GetCollection(
            uriTemplate: 'categories',
            normalizationContext: [
                "groups" => ["category.pRead"]
            ],
            name: 'categories.getCollection',
        )
    ]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category.pRead', 'category.fRead', 'lesson.fRead', 'lesson.pRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category.pRead', 'category.fRead', 'lesson.fRead', 'lesson.pRead', 'users.fRead'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Lesson>
     */
    #[ORM\OneToMany(targetEntity: Lesson::class, mappedBy: 'category', cascade: ['persist', 'remove'])]
    #[Groups(['category.fRead'])]
    private Collection $lessons;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['category.pRead', 'category.fRead', 'lesson.fRead', 'lesson.pRead'])]
    private ?string $imagePath = null;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCategory($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCategory() === $this) {
                $lesson->setCategory(null);
            }
        }

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }
}
