<?php

namespace App\Entity\Lesson;

use App\Entity\User;
use App\Repository\Lesson\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment.fRead'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['comment.fRead'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[Groups(['comment.fRead'])]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    #[Groups(['comment.fRead'])]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['comment.fRead'])]
    private ?string $audio_path = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['comment.fRead'])]
    private ?string $image_path = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Lesson $lesson = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getAudioPath(): ?string
    {
        return $this->audio_path;
    }

    public function setAudioPath(?string $audio_path): static
    {
        $this->audio_path = $audio_path;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(?string $image_path): static
    {
        $this->image_path = $image_path;

        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): static
    {
        $this->lesson = $lesson;

        return $this;
    }
}
