<?php

namespace App\Entity\Lesson;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Lesson\AddCommentController;
use App\Controller\Lesson\CreateLessonController;
use App\Controller\Lesson\FindSimilarController;
use App\Controller\Lesson\GetCommentsController;
use App\Entity\User;
use App\Repository\Lesson\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: 'lessons/{id}',
            normalizationContext: [
                "groups" => ["lesson.fRead"]
            ],
            name: 'lessons.get',
        ),
        new Get(
            uriTemplate: 'lessons/{id}/comments',
            controller: GetCommentsController::class,
            normalizationContext: [
                "groups" => ["comment.fRead"]
            ],
            name: 'lessons.get.comment',
        ),
        new Get(
            uriTemplate: 'lessons/{id}/similar',
            controller: FindSimilarController::class,
            normalizationContext: [
                "groups" => ["lesson.pRead"]
            ],
            name: 'lessons.get.similar',
        ),
        new Post(
            uriTemplate: 'lessons',
            controller: CreateLessonController::class,
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
                                    'audio' => [
                                        'type' => 'string',
                                        'format' =>  'binary'
                                    ],
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                    'description' => [
                                        'type' => 'string'
                                    ],
                                    'category' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            deserialize: false,
            name: 'lessons.post',
        ),
        new Post(
            uriTemplate: 'lessons/{id}/comments',
            controller: AddCommentController::class,
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
                                    'audio' => [
                                        'type' => 'string',
                                        'format' =>  'binary'
                                    ],
                                    'message' => [
                                        'type' => 'string'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            deserialize: false,
            name: 'lessons.add.comment',
        ),
        new GetCollection(
            uriTemplate: 'lessons',
            normalizationContext: [
                "groups" => ["lesson.pRead"]
            ],
            name: 'lessons.getCollection',
        ),
        new Delete(
            uriTemplate: 'lessons/{id}',
            name: 'lessons.delete',
        )
    ]
)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['lesson.pRead', 'lesson.fRead', 'category.fRead', 'users.fRead'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['lesson.pRead', 'lesson.fRead', 'category.fRead', 'users.fRead'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['lesson.pRead', 'lesson.fRead', 'users.fRead'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['lesson.pRead', 'lesson.fRead', 'category.fRead', 'users.fRead'])]
    private ?string $imagePath = null;

    #[ORM\Column(length: 255)]
    #[Groups(['lesson.pRead', 'lesson.fRead'])]
    private ?string $audioPath = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[Groups(['lesson.fRead', 'users.fRead',])]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[Groups(['lesson.pRead', 'lesson.fRead'])]
    private ?User $author = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'lesson')]
    #[Groups(['comment.fRead'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getAudioPath(): ?string
    {
        return $this->audioPath;
    }

    public function setAudioPath(string $audioPath): static
    {
        $this->audioPath = $audioPath;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setLesson($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getLesson() === $this) {
                $comment->setLesson(null);
            }
        }

        return $this;
    }
}
