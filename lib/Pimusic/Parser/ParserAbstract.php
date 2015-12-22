<?php

namespace Pimusic\Parser;

abstract class ParserAbstract
{

    abstract public function match($text);

    abstract public function fetch($url);



}
