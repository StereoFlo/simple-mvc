<?php


namespace Core\Model\QueryBuilder;

/**
 * Class OrderBy
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class OrderBy extends AbstractPart
{
    const DIRECTION_DESC = 'DESC';
    const DIRECTION_ACS  = 'ASC';

    public function __construct()
    {
        $this->result = ' ORDER BY ';
    }

    /**
     * @param string      $column
     * @param string|null $direction
     *
     * @return $this
     */
    public function addOrderBy(string $column, string $direction = null)
    {
        $this->data[$column] = $direction;
        return $this;
    }

    /**
     * @return OrderBy
     */
    public function process(): self
    {
        if (empty($this->data)) {
            $this->result = '';
            return $this;
        }
        foreach ($this->data as $col => $direct) {
            if (empty($direct)) {
                if (empty($ret)) {
                    $this->result .= $col;
                } else {
                    $this->result .= ', ' . $col;
                }
                continue;
            }
            if (empty($ret)) {
                $this->result.= $col . ' ' . $direct;
            } else {
                $this->result .= ', ' . $col . ' ' . $direct;
            }
        }

        return $this;
    }


}