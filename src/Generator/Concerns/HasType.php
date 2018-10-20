<?php

namespace Radic\GraphqlStreamsApiModule\Generator\Concerns;

trait HasType
{
    /** @var string */
    protected $type;

    /** @var bool */
    protected $isNonNull = false;

    /** @var bool */
    protected $isList = false;

    /** @var bool */
    protected $isListNonNull = false;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNonNull(): bool
    {
        return $this->isNonNull;
    }

    /**
     * @param bool $isNonNull
     *
     * @return $this
     */
    public function setIsNonNull(bool $isNonNull)
    {
        $this->isNonNull = $isNonNull;
        return $this;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->isList;
    }

    /**
     * @param bool $isList
     *
     * @return $this
     */
    public function setIsList(bool $isList)
    {
        $this->isList = $isList;
        return $this;
    }

    /**
     * @return bool
     */
    public function isListNonNull(): bool
    {
        return $this->isListNonNull;
    }

    /**
     * @param bool $isListNonNull
     *
     * @return $this
     */
    public function setIsListNonNull(bool $isListNonNull)
    {
        $this->isListNonNull = $isListNonNull;
        return $this;
    }

    public function getTypeString()
    {
        $segments = [];
        if ($this->isList()) {
            $segments[] = '[';
        }
        $segments[] = $this->getType();

        if ($this->isNonNull()) {
            $segments[] = '!';
        }
        if ($this->isList()) {
            $segments[] = ']';
        }
        if ($this->isListNonNull()) {
            $segments[] = '!';
        }
        return implode('', $segments);
    }

}