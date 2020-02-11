<?php


namespace Entity;


class CommentEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $projectId;


    private $commentDate;

    /**
     * @var string
     */
    private $pseudo;

    /**
     * @var string
     */
    private $content;

    /**
     * @var boolean
     */
    private $validate;



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
        if('projectId' === $property) {
            $this->projectId = (string) $value;
        }
        if('commentDate' === $property) {
            $this->commentDate = (string) $value;
        }
        if('pseudo' === $property) {
            $this->pseudo = (string) $value;
        }
        if('content' === $property) {
            $this->content = (string) $value;
        }
        if('validate' === $property) {
            $this->validate = (string) $value;
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
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return mixed
     */
    public function getCommentDate()
    {
        return $this->commentDate;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isValidate(): bool
    {
        return $this->validate;
    }



}