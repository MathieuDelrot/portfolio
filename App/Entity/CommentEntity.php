<?php


namespace App\Entity;

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



    public function __construct(array $datas = null)
    {
        if(!empty($datas)){
            $this->hydrate($datas);
        }
    }

    public function hydrate(array $datas = null)
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

    /**
     * @param int $id
     * @return CommentEntity
     */
    public function setId(int $id): CommentEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param int $projectId
     * @return CommentEntity
     */
    public function setProjectId(int $projectId): CommentEntity
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @param mixed $commentDate
     * @return CommentEntity
     */
    public function setCommentDate($commentDate)
    {
        $this->commentDate = $commentDate;
        return $this;
    }

    /**
     * @param string $pseudo
     * @return CommentEntity
     */
    public function setPseudo(string $pseudo): CommentEntity
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    /**
     * @param string $content
     * @return CommentEntity
     */
    public function setContent(string $content): CommentEntity
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param bool $validate
     * @return CommentEntity
     */
    public function setValidate(bool $validate): CommentEntity
    {
        $this->validate = $validate;
        return $this;
    }


}