<?php


namespace Core\Model\QueryBuilder;

/**
 * Class Limit
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class Limit extends AbstractPart
{
    /**
     * Where constructor.
     */
    public function __construct()
    {
        $this->data = 0;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function addLimit(int $limit): self
    {
        $this->data = $limit;
        return $this;
    }

    /**
     * @return string
     */
    public function process(): self
    {
        $this->data = (int) $this->data;
        if (empty($this->limit)) {
            $this->result = '';
            return $this;
        }

        $this->result = ' LIMIT ' . $this->data;
        return $this;
    }
}