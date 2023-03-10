<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Series
 *
 * @ORM\Table(name="series", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_3A10012D85489131", columns={"imdb"})})
 * @ORM\Entity
 */
class Series
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
     * @ORM\Column(name="title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plot", type="text", length=0, nullable=true)
     */
    private $plot;

    /**
     * @var string
     *
     * @ORM\Column(name="imdb", type="string", length=128, nullable=false)
     */
    private $imdb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="poster", type="blob", length=0, nullable=true)
     */
    private $poster;

    /**
     * @var string|null
     *
     * @ORM\Column(name="director", type="string", length=128, nullable=true)
     */
    private $director;

    /**
     * @var string|null
     *
     * @ORM\Column(name="youtube_trailer", type="string", length=128, nullable=true)
     */
    private $youtubeTrailer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="awards", type="text", length=0, nullable=true)
     */
    private $awards;

    /**
     * @var int|null
     *
     * @ORM\Column(name="year_start", type="integer", nullable=true)
     */
    private $yearStart;

    /**
     * @var int|null
     *
     * @ORM\Column(name="year_end", type="integer", nullable=true)
     */
    private $yearEnd;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Country", mappedBy="series")
     */
    private $country = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="series")
     */
    private $user = array();

    /**
     * @var \Season
     *
     * @ORM\OneToMany(targetEntity="Season", mappedBy="series")
     */
    private $seasons ;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Actor", mappedBy="series")
     */
    private $actor = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Genre", mappedBy="series")
     */
    private $genre = array();

    /**
     * @var \Rating
     *
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="series")
     */
    private $rating;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->country = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genre = new \Doctrine\Common\Collections\ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->rating = new ArrayCollection();
    }

    /**
     * Permet d'obtenir l'id d'une s??rie
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Permet d'obtenir le titre de la s??rie
     *
     * @return ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Permet de d??finir le titre d'une s??rie
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Permet d'obtenir le r??sum?? de la s??rie
     *
     * @return ?string
     */
    public function getPlot(): ?string
    {
        return $this->plot;
    }

    /**
     * Permet de d??finir le r??sum?? d'une s??rie
     *
     * @param ?string $plot
     *
     * @return self
     */
    public function setPlot(?string $plot): self
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * Permet d'obtenir le lien de la base de donn??es de la s??rie
     *
     * @return ?string
     */
    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    /**
     * Permet de d??finir le lien de la base de donn??es
     *
     * @param string $imdb
     *
     * @return self
     */
    public function setImdb(string $imdb): self
    {
        $this->imdb = $imdb;

        return $this;
    }

    /**
     * Permet d'obtenir le poster de la s??rie
     *
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Permet de d??finir le poster de la s??rie
     *
     * $poster le poster de la s??rie
     *
     * @return self
     */
    public function setPoster($poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Permet d'obtenir le directeur de la s??rie
     *
     * @return ?string
     */
    public function getDirector(): ?string
    {
        return $this->director;
    }

    /**
     * Permet de d??finir le directeur de la s??rie
     *
     * @param ?string $director
     *
     * @return self
     */
    public function setDirector(?string $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Permet d'obtenir le trailer de youtube de la s??rie
     *
     * @return ?string
     */
    public function getYoutubeTrailer(): ?string
    {
        return $this->youtubeTrailer;
    }

    /**
     * Permet de d??finir le trailer youtube de la s??rie
     *
     * @param ?string $youtubeTrailer
     *
     * @return self
     */
    public function setYoutubeTrailer(?string $youtubeTrailer): self
    {
        $this->youtubeTrailer = $youtubeTrailer;

        return $this;
    }

    /**
     * Permet d'obtenir les r??compenses de la s??rie
     *
     * @return ?string
     */
    public function getAwards(): ?string
    {
        return $this->awards;
    }

    /**
     * Permet de d??finir les r??compenses de la s??rie
     *
     * @param ?string $awards les r??compenses de la s??rie
     *
     * @return self
     */
    public function setAwards(?string $awards): self
    {
        $this->awards = $awards;

        return $this;
    }

    /**
     * Permet d'obtenir l'ann??e de d??but de la s??rie
     *
     * @return ?int
     */
    public function getYearStart(): ?int
    {
        return $this->yearStart;
    }

    /**
     * Permet de d??finir l'ann??e de d??but de la s??rie
     *
     * @param ?int $yearStart
     *
     * @return self
     */
    public function setYearStart(?int $yearStart): self
    {
        $this->yearStart = $yearStart;

        return $this;
    }

    /**
     * Permet d'obtenir l'ann??e de fin de la s??rie
     *
     * @return ?int
     */
    public function getYearEnd(): ?int
    {
        return $this->yearEnd;
    }

    /**
     * Permet de d??finir l'ann??e de fin de la s??rie
     *
     * @param ?int $yearEnd
     *
     * @return self
     */
    public function setYearEnd(?int $yearEnd): self
    {
        $this->yearEnd = $yearEnd;

        return $this;
    }

    /**
     * Permet d'obtenir la liste des pays dans lesquelles la s??rie a ??t?? cr??e
     *
     * @return Collection<int, Country>
     */
    public function getCountry(): Collection
    {
        return $this->country;
    }

    /**
     * Permet d'ajouter un pays ?? la liste des pays de la s??rie
     *
     * @param Country $country
     *
     * @return self
     */
    public function addCountry(Country $country): self
    {
        if (!$this->country->contains($country)) {
            $this->country->add($country);
            $country->addSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer un pays de la liste des pays de la s??rie
     *
     * @param Country $country
     *
     * @return self
     */
    public function removeCountry(Country $country): self
    {
        if ($this->country->removeElement($country)) {
            $country->removeSeries($this);
        }

        return $this;
    }

    /**
     * Permet d'obtenir la liste des utilisateurs qui suivent la s??rie
     *
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    /**
     * Permet d'ajouter un utilisateur ?? la liste des utilisateurs qui suivent la s??rie
     *
     * @param User $user
     *
     * @return self
     */
    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->addSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer un utilisateur de la liste des utilisateurs qui suivent la s??rie
     *
     * @param User $user
     *
     * @return self
     */
    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            $user->removeSeries($this);
        }

        return $this;
    }

    /**
     * Permet d'obtenir la liste des acteurs qui jouent dans la s??rie
     *
     * @return Collection<int, Actor>
     */
    public function getActor(): Collection
    {
        return $this->actor;
    }

    /**
     * Permet d'ajouter un acteur ?? la liste des acteurs ayant jouer dans la s??rie
     *
     * @param Actor $actor
     *
     * @return self
     */
    public function addActor(Actor $actor): self
    {
        if (!$this->actor->contains($actor)) {
            $this->actor->add($actor);
            $actor->addSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer un acteur de la s??rie
     *
     * @param Actor $actor
     *
     * @return self
     */
    public function removeActor(Actor $actor): self
    {
        if ($this->actor->removeElement($actor)) {
            $actor->removeSeries($this);
        }

        return $this;
    }

    /**
     * Permet d'obtenir la liste des genres de la s??rie
     *
     * @return Collection<int, Genre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    /**
     * Permet d'ajouter un genre ?? la s??rie
     *
     * @param Genre $genre
     *
     * @return self
     */
    public function addGenre(Genre $genre): self
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
            $genre->addSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer un genre de la s??rie
     *
     * @param Genre $genre
     *
     * @return self
     */
    public function removeGenre(Genre $genre): self
    {
        if ($this->genre->removeElement($genre)) {
            $genre->removeSeries($this);
        }

        return $this;
    }

    /**
     * Permet d'afficher a liste des saisons de la s??rie
     *
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    /**
     * Permet d'ajouter une saison ?? la s??rie
     *
     * @param Season $season
     *
     * @return self
     */
    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer une saison de la s??rie
     *
     * @param Season $season
     *
     * @return self
     */
    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed) REVOIR
            if ($season->getSeries() === $this) {
                $season->setSeries(null);
            }
        }
        return $this;
    }

    /**
     * Permet d'affihcer le poster de la s??rie
     *
     * @return self
     */
    public function displayPoster()
    {
        $poster = stream_get_contents($this->getPoster());
        $poster = base64_encode($poster);
        echo '<img src="data:poster/jpeg;base64,'.$poster.'" alt="Poster"/>';
    }

    /**
     * Permet d'obtenir le trailer youtube de la s??rie
     *
     * @return string
     */
    public function getEmbedTrailerLink(): string
    {
        return "https://youtube.com/embed/".substr($this->youtubeTrailer, strrpos($this->youtubeTrailer, '=')+1);
    }

    /**
     * Permet d'obtenir la liste des notes de la s??rie
     *
     * @return Collection<int, Rating>
     */
    public function getRating(): Collection
    {
        return $this->rating;
    }

    /**
     * Permet d'ajouter une note ?? la liste des notes de la s??rie
     *
     * @param Rating $rating
     *
     * @return self
     */
    public function addRating(Rating $rating): self
    {
        if (!$this->rating->contains($rating)) {
            $this->rating->add($rating);
            $rating->setSeries($this);
        }

        return $this;
    }

    /**
     * Permet de supprimer une note ?? la liste des notes de la s??rie
     *
     * @param Rating $rating
     *
     * @return self
     */
    public function removeRating(Rating $rating): self
    {
        if ($this->rating->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getSeries() === $this) {
                $rating->setSeries(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
