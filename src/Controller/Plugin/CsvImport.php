<?php

namespace ModulusCsv\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * CsvImport
 *
 * @category ModulusCsv
 * @package ModulusCsv\Controller\Plugin
 * @author BemCasei <contato@bemcasei.com.br>
 */
class CsvImport extends AbstractPlugin implements \Countable, \Iterator
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var bool
     */
    protected $useFirstRecordAsHeader;

    /**
     * @var array
     */
    protected $header;

    /**
     * Create CSV
     *
     * @param string $filepath
     * @param bool $useFirstRecordAsHeader
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return $this
     */
    public function __invoke($filepath, $useFirstRecordAsHeader = true, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->file = new \SplFileObject($filepath);
        $this->file->setFlags(
            \SplFileObject::READ_CSV
            | \SplFileObject::READ_AHEAD
            | \SplFileObject::SKIP_EMPTY
            | \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl($delimiter, $enclosure, $escape);
        $this->useFirstRecordAsHeader = $useFirstRecordAsHeader;

        return $this;
    }

    /**
     * Get file
     *
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Rewind, optionally consumes the first record as the header
     *
     */
    public function rewind()
    {
        $this->file->rewind();
        if ($this->useFirstRecordAsHeader) {
            if (! $this->file->valid()) {
                throw new \RuntimeException('Expected first row to be header, but reached EOF instead');
            }
            $this->header = $this->file->current();
            $this->file->next();
        }
    }

    /**
     * Key, return the row number of the element
     *
     * @return int
     */
    public function key()
    {
        return $this->file->key() + 1;
    }

    /**
     * Current, return the current record
     *
     * @return array|string
     */
    public function current()
    {
        $row = $this->file->current();
        if ($this->useFirstRecordAsHeader) {
            $header = $this->header;
            if (count($row) != count($header)) {
                $size   = min(count($header), count($row));
                $header = array_slice($this->header, 0, $size);
                $row    = array_slice($row, 0, $size);
            }
            return array_combine($header, $row);
        }

        return $row;
    }

    /**
     * Next, move forward to next element
     *
     */
    public function next()
    {
        $this->file->next();
    }

    /**
     * Valid, checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * Count number of records
     *
     * @return int
     */
    public function count()
    {
        $total = 0;
        foreach ($this->file as $row) {
            $total++;
        }

        if ($this->useFirstRecordAsHeader && $total > 0) {
            $total--;
        }

        return $total;
    }
}
