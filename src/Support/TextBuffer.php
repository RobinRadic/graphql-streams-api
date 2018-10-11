<?php

namespace Radic\GraphqlStreamsApiModule\Support;


class TextBuffer
{
    protected $lines = [];

    protected $indent = 0;

    /**
     * TextBuffer constructor.
     *
     * @param array $lines
     * @param int   $indent
     */
    public function __construct(array $lines = [], int $indent = 0)
    {
        $this->lines  = $lines;
        $this->indent = $indent;
    }

    public function prepend($line = '')
    {
        $prefix = $this->indent === 0 ? '' : str_repeat("\t", $this->indent);
        array_unshift($this->lines, $prefix . $line);
        return $this;
    }

    public function append($line = '')
    {
        $prefix        = $this->indent === 0 ? '' : str_repeat("\t", $this->indent);
        $this->lines[] = $prefix . $line;
        return $this;
    }

    public function setIndent(int $indent)
    {
        $this->indent = $indent;
        return $this;
    }

    public function indent()
    {
        $this->indent++;
        return $this;
    }

    public function unindent()
    {
        $this->indent--;
        return $this;
    }

    public function toString()
    {
        return implode("\n", $this->lines);
    }

    public function __toString()
    {
        return $this->toString();
    }


}