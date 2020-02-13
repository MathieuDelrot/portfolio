<?php


namespace App\Entity;

class ProjectEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $content;

    private $modificationDate;

    /**
     * @var integer
     */
    private $authorId;

    private $realisationDate;

    /**
     * @var string
     */
    private $technologies;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $intro;


    public function __construct(array $datas = null)
    {
        if(!empty($datas)){
            $this->hydrate($datas);
        }
    }

    public function hydrate(array $datas)
    {
        foreach ($datas as $key => $value)
        {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @return mixed
     */
    public function getRealisationDate()
    {
        return $this->realisationDate;
    }

    /**
     * @return string
     */
    public function getTechnologies(): string
    {
        return $this->technologies;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param int $id
     * @return ProjectEntity
     */
    public function setId(int $id): ProjectEntity
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @param string $title
     * @return ProjectEntity
     */
    public function setTitle(string $title): ProjectEntity
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $slug
     * @return ProjectEntity
     */
    public function setSlug(): ProjectEntity
    {
        $this->slug =  preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($this->getTitle()));
        return $this;
    }

    /**
     * @param string $content
     * @return ProjectEntity
     */
    public function setContent(string $content): ProjectEntity
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param mixed $modificationDate
     * @return ProjectEntity
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
        return $this;
    }

    /**
     * @param int $authorId
     * @return ProjectEntity
     */
    public function setAuthorId(int $authorId): ProjectEntity
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @param mixed $realisationDate
     * @return ProjectEntity
     */
    public function setRealisationDate($realisationDate)
    {
        $this->realisationDate = $realisationDate;
        return $this;
    }

    /**
     * @param string $technologies
     * @return ProjectEntity
     */
    public function setTechnologies(string $technologies): ProjectEntity
    {
        $this->technologies = $technologies;
        return $this;
    }

    /**
     * @param string $url
     * @return ProjectEntity
     */
    public function setUrl(string $url): ProjectEntity
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $intro
     * @return ProjectEntity
     */
    public function setIntro(string $intro): ProjectEntity
    {
        $this->intro = $intro;
        return $this;
    }


}