<?php

namespace Drupal\Tests\cfs_mail\Unit;

use Drupal\cfs_mail\Parser\Tokenizer;
use Drupal\cfs_mail\Parser\TokenType;
use Drupal\Tests\UnitTestCase;

/**
 * Unit tests for the MailParser Tokenizer utility.
 *
 * @group cfs_mail
 * @group capstone_suite
 *
 * @coversDefaultClass \Drupal\cfs_mail\Parser\Tokenizer
 */
class TokenizerTest extends UnitTestCase {
  /**
   * The text to be parsed.
   *
   * @var string
   */
  private $mailParseText;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->mailParseText = "
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
    ";
  }

  /**
   * Tests the tokenization of the text.
   */
  public function testTokenize() {
    $tokens = Tokenizer::tokenize($this->mailParseText);
    $expectedTokenTypes = [
      TokenType::T_CHARPLUS,
      TokenType::T_IF, TokenType::T_VAR, TokenType::T_END, TokenType::T_CHARPLUS,
      TokenType::T_IF, TokenType::T_VAR, TokenType::T_END,
      TokenType::T_CHARPLUS,
      TokenType::T_ELSEIF, TokenType::T_VAR, TokenType::T_END,
      TokenType::T_CHARPLUS,
      TokenType::T_ELSE,
      TokenType::T_CHARPLUS,
      TokenType::T_ENDIF,
      TokenType::T_CHARPLUS,
      TokenType::T_ENDIF,
      TokenType::T_CHARPLUS,
    // No comma here on purpose - T_EOF should always be the last token.
      TokenType::T_EOF,
    ];
    foreach ($tokens as $index => $token) {
      $this->assertEquals($expectedTokenTypes[$index], $token->getType());
    }
  }

}
