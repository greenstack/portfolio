<?php

namespace Drupal\Tests\cfs_mail\Unit;

use Drupal\Tests\UnitTestCase;

use Drupal\cfs_mail\Parser\Parser;
use Drupal\cfs_mail\Parser\Tokenizer;

/**
 * Tests the parser.
 *
 * @group cfs_mail
 * @group capstone_suite
 */
class ParserTest extends UnitTestCase {

  /**
   * Builds a parser for use.
   *
   * @param string $text
   *   The text to be parsed.
   *
   * @return Drupal\cfs_mail\Parser\Parser
   *   The created parser.
   */
  private function buildParser(string $text) {
    $tokens = Tokenizer::tokenize($text);
    return new Parser($tokens);
  }

  /**
   * Tests the parser against valid syntax.
   */
  public function testValidParse() {
    $this->buildParser("
    Lorem ipsum dolor sit amet
    [[if var]]
      [[if nested]]
      if [[nested text
      [[elseif nested_else]]
      else if nested_else text
      [[else]]
      else text
      [[endif]]
      text included either way
    [[endif]]
    text at the end
    ")
      ->checkSyntax();
    // If it throws, it won't end up here.
    $this->assertTrue(TRUE);
  }

  /**
   * Tests the parser against invalid syntax.
   */
  public function testInvalidElse() {
    $this->expectException(\Exception::class);
    $this->buildParser("[[else]]")->checkSyntax();
  }

  /**
   * Tests for missing end tokens.
   */
  public function testIfWithoutEnd() {
    $this->expectException(\Exception::class);
    $this->buildParser("[[if var")->checkSyntax();
  }

  /**
   * Tests for extra end tokens.
   */
  public function testInvalidEnd() {
    $this->expectException(\Exception::class);
    $this->buildParser("]]")->checkSyntax();
  }

  /**
   * Tests for missing endifs.
   */
  public function testInvalidBlock() {
    $this->expectException(\Exception::class);
    $this->buildParser("[[if var]] then [[else]] oh no, no endif")->checkSyntax();
  }

  /**
   * Tests for missing endifs with nested endifs.
   */
  public function testInvalidBlockNested() {
    $this->expectException(\Exception::class);
    $this->buildParser("[[if var]] and [[if var2]] then [[endif]] missing another endif")->checkSyntax();
  }

}
