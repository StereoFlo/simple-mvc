<?php


namespace Core\Model\Query\Columns;


class Explode
{
    /**
     * @var string
     */
    protected $columns = '*';

    /**
     * @var array
     */
    protected $deliminators = [",", ".", "|", ":", "_"];

    /**
     * @var array
     */
    protected $clean = [];

    /**
     * Explode constructor.
     *
     * @param string $columns
     * @param array  $deliminators
     */
    public function __construct(string $columns, array $deliminators = [])
    {
        if ($columns) {
            $this->columns = $columns;
        }

        if ($deliminators) {
            $this->deliminators = $deliminators;
        }

        $this->init();
    }

    /**
     * @return array
     */
    public function getClean(): array
    {
        return $this->clean;
    }

    /**
     * @return Explode
     */
    protected function init(): self
    {
        $this->columns = \str_replace($this->deliminators, $this->deliminators[0], $this->columns);
        $array = \explode($this->deliminators[0], $this->columns);
        foreach ($array as $r) {
            $this->clean[] = \trim($r);
        }
        return $this;
    }
}