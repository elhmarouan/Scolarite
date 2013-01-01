<?php

/**
 * Parser abstract
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.parser
 * 
 */
abstract class ParserAbstract {

   abstract public function fromHtml($textToParse);

   abstract public function toHtml($textToParse);
}