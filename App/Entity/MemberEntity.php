<?php

namespace App\Entity;

class MemberEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $passwordKey;


    private $keyDate;

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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }



    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }


    /**
     * @return string
     */
    public function getPasswordKey(): string
    {
        return $this->passwordKey;
    }


    /**
     * @return mixed
     */
    public function getKeyDate()
    {
        return $this->keyDate;
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
     * @return MemberEntity
     */
    public function setId(int $id): MemberEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $firstName
     * @return MemberEntity
     */
    public function setFirstName(string $firstName): MemberEntity
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string $email
     * @return MemberEntity
     */
    public function setEmail(string $email): MemberEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return MemberEntity
     */
    public function setPassword(string $password): MemberEntity
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $passwordKey
     * @return MemberEntity
     */
    public function setPasswordKey(string $passwordKey): MemberEntity
    {
        $this->passwordKey = $passwordKey;
        return $this;
    }

    /**
     * @param mixed $keyDate
     * @return MemberEntity
     */
    public function setKeyDate($keyDate)
    {
        $this->keyDate = $keyDate;
        return $this;
    }

    /**
     * @param bool $validate
     * @return MemberEntity
     */
    public function setValidate(bool $validate): MemberEntity
    {
        $this->validate = $validate;
        return $this;
    }



}