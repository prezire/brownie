<?php namespace prezire;
final class DumpFileException extends \Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
  }
}