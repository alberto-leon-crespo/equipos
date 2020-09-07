<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Team;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Players
 *
 * @ORM\Table(name="players", indexes={@ORM\Index(name="IDX_264E43A6296CD8AE", columns={"team_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 *
*/
class Player implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=false)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $age;

    /**
     * @var string|null
     *
     * @ORM\Column(name="position", type="text", nullable=true)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $position;

    /**
     * @var float|null
     *
     * @ORM\Column(name="price", type="decimal", nullable=true)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", cascade={"persist", "refresh"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     * })
     * @Assert\Type(type="App\Entity\Team")
     * @Assert\Valid()
     */
    private $team;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Player
     */
    public function setId(int $id): Player
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Player
     */
    public function setName(string $name): Player
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return Player
     */
    public function setAge(int $age): Player
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     * @return Player
     */
    public function setPosition(?string $position): Player
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return Player
     */
    public function setPrice(?float $price): Player
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return \App\Entity\Team
     */
    public function getTeam(): \App\Entity\Team
    {
        return $this->team;
    }


    /**
     * @param \App\Entity\Team $team
     * @return Player
     */
    public function setTeam(\App\Entity\Team $team): Player
    {
        $this->team = $team;
        return $this;
    }

    public function addChild (Team $team) {
        $this->team = $team;
    }

    public function jsonSerialize()
    {
        return [
            "id"=> $this->getId(),
            "name" => $this->getName(),
            "age" => $this->getAge(),
            "position" => $this->getPosition(),
            "price" => $this->getPrice(),
            "team" => $this->getTeam()
        ];
    }
}
