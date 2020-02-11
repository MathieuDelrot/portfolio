<?php


namespace Entity;


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

    public function __construct(array $datas)
    {
        $this->hydrate($datas);
    }

    public function hydrate(array $datas)
    {
        foreach ($datas as $key => $value)
        {
           $this->__set($key, $value);
        }
    }

    public function __set($property, $value){

        if('id' === $property) {
            $this->id = (int) $value;
        }
        if('title' === $property) {
            $this->title = (string) $value;
        }
        if('slug' === $property) {
            $this->slug = (string) $value;
        }
        if('content' === $property) {
            $this->content = (string) $value;
        }
        if('modificationDate' === $property) {
            $this->modificationDate = $value;
        }
        if('authorId' === $property) {
            $this->authorId = (bool) $value;
        }
        if('realisationDate' === $property) {
            $this->realisationDate =  $value;
        }
        if('technologies' === $property) {
            $this->technologies = (string) $value;
        }
        if('url' === $property) {
            $this->url = (string) $value;
        }
        if('intro' === $property) {
            $this->intro = (string) $value;
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


}