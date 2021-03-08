<?php

namespace Drupal\cfs_mail\Parser;

/**
 * Parses the tokens created by a MailParse block.
 */
class Parser {

  /**
   * The list of tokens being parsed.
   *
   * @var Drupal\cfs_mail\Parser\Token[]
   */
  private $tokens;

  /**
   * The index of the token being checked.
   *
   * @var int
   */
  private $index = 0;

  /**
   * The current token being checked.
   *
   * @var Drupal\cfs_mail\Parser\Token
   */
  private $currentToken;

  /**
   * Contains the table for properly parsing MailParse syntax.
   *
   * @var mixed[]
   */
  private $parseTable = [
    'START' => [
      TokenType::T_CHARPLUS => ['_string', '_start'],
      TokenType::T_IF => ['_conditional', '_start'],
      TokenType::T_EOF => ['_t_eof'],
    ],
    'NEST' => [
      TokenType::T_CHARPLUS => ['_string', '_nest'],
      TokenType::T_IF => ['_conditional', '_nest'],
      TokenType::T_EOF => ['_t_eof'],
      TokenType::T_ELSEIF => ['_epsilon'],
      TokenType::T_ELSE => ['_epsilon'],
      TokenType::T_ENDIF => ['_epsilon'],
    ],
    'CON' => [
      TokenType::T_IF => [
        '_t_if',
        '_t_var',
        '_t_end',
        '_nest',
        '_option',
        '_t_endif',
      ],
      TokenType::T_CHARPLUS => ['_epsilon'],
      TokenType::T_ELSEIF => ['_epsilon'],
      TokenType::T_ELSE => ['_epsilon'],
      TokenType::T_ENDIF => ['_epsilon'],
    ],
    'OPT' => [
      TokenType::T_ELSEIF => [
        '_t_elseif',
        '_t_var',
        '_t_end',
        '_nest',
        '_option',
      ],
      TokenType::T_ELSE => ['_t_else', '_nest'],
      TokenType::T_ENDIF => ['_epsilon'],
    ],
    'STRING' => [
      TokenType::T_CHARPLUS => ['_t_charplus'],
      TokenType::T_ELSEIF => ['_epsilon'],
      TokenType::T_ELSE => ['_epsilon'],
      TokenType::T_ENDIF => ['_epsilon'],
      TokenType::T_END => ['_epsilon'],
      TokenType::T_IF => ['_epsilon'],
    ],
  ];

  /**
   * Constructs a parser.
   *
   * @param array $tokens
   *   An array of tokens to be parsed.
   */
  public function __construct(array $tokens) {
    $this->tokens = $tokens;
    $this->index = 0;
    $this->currentToken = $this->tokens[$this->index];
  }

  /**
   * Checks the syntax of the tokens, to ensure proper function.
   */
  public function checkSyntax() {
    $this->_start();
  }

  /**
   * Throws an error when an unexpected token is found.
   */
  public function error(array $expected) {
    $token_type = $this->currentToken->getType();
    $token_value = $this->currentToken->getValue();
    $expected = array_keys($expected);
    $glued = implode(' or ', $expected);
    $line = $this->currentToken->getLine();
    throw new \Exception("Syntax error: $token_type ($token_value), expected $glued (on line $line)");
  }

  /**
   * Begins parsing the tokens.
   */
  // @codingStandardsIgnoreStart
  private function _start() {
  // @codingStandardsIgnoreEnd
    $token_type = $this->currentToken->getType();
    if (empty($this->parseTable['START'][$token_type])) {
      $this->error($this->parseTable['START']);
    }
    foreach ($this->parseTable['START'][$token_type] as $function) {
      $this->$function();
    }
  }

  /**
   * Tells the parser to enter a nested mode.
   */
  // @codingStandardsIgnoreStart
  private function _nest() {
  // @codingStandardsIgnoreEnd
    $token_type = $this->currentToken->getType();
    if (empty($this->parseTable['NEST'][$token_type])) {
      $this->error($this->parseTable['NEST']);
    }
    foreach ($this->parseTable['NEST'][$token_type] as $function) {
      $this->$function();
    }
  }

  /**
   * Tells the parser to evaluate the syntax of a conditional statement.
   */
  // @codingStandardsIgnoreStart
  private function _conditional() {
  // @codingStandardsIgnoreEnd
    $token_type = $this->currentToken->getType();
    if (empty($this->parseTable['CON'][$token_type])) {
      $this->error($this->parseTable['CON']);
    }
    foreach ($this->parseTable['CON'][$token_type] as $function) {
      $this->$function();
    }
  }

  /**
   * Parses an option-type token.
   */
  // @codingStandardsIgnoreStart
  private function _option() {
  // @codingStandardsIgnoreEnd
    $token_type = $this->currentToken->getType();
    if (empty($this->parseTable['OPT'][$token_type])) {
      $this->error($this->parseTable['OPT']);
    }
    foreach ($this->parseTable['OPT'][$token_type] as $function) {
      $this->$function();
    }
  }

  /**
   * Parses a string-type token.
   */
  // @codingStandardsIgnoreStart
  private function _string() {
  // @codingStandardsIgnoreEnd
    $token_type = $this->currentToken->getType();
    if (empty($this->parseTable['STRING'][$token_type])) {
      $this->error($this->parseTable['STRING']);
    }
    foreach ($this->parseTable['STRING'][$token_type] as $function) {
      $this->$function();
    }
  }

  /**
   * Evaluates the empty character.
   */
  // @codingStandardsIgnoreStart
  private function _epsilon() {}
  // @codingStandardsIgnoreEnd

  /**
   * Ensures that the current token is the End of File token.
   */
  // @codingStandardsIgnoreStart
  private function _t_eof() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_EOF);
  }

  /**
   * Checks if the current token is a string (T_CHARPLUS).
   */
  // @codingStandardsIgnoreStart
  private function _t_charplus() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_CHARPLUS);
    $this->getNext();
  }

  /**
   * Checks if the token is T_IF.
   */
  // @codingStandardsIgnoreStart
  private function _t_if() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_IF);
    $this->getNext();
  }

  /**
   * Checks if the token is T_END.
   */
  // @codingStandardsIgnoreStart
  private function _t_end() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_END);
    $this->getNext();
  }

  /**
   * Checks if the token is T_ENDIF.
   */
  // @codingStandardsIgnoreStart
  private function _t_endif() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_ENDIF);
    $this->getNext();
  }

  /**
   * Checks if the token is T_ELSEIF.
   */
  // @codingStandardsIgnoreStart
  private function _t_elseif() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_ELSEIF);
    $this->getNext();
  }

  /**
   * Checks if the token is T_ELSE.
   */
  // @codingStandardsIgnoreStart
  private function _t_else() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_ELSE);
    $this->getNext();
  }

  /**
   * Checks if the token is T_VAR.
   */
  // @codingStandardsIgnoreStart
  private function _t_var() {
  // @codingStandardsIgnoreEnd
    $this->check(TokenType::T_VAR);
    $this->getNext();
  }

  /**
   * Ensures that the current token is of the expected type.
   *
   * @param string $token_to_expect
   *   The expected token type.
   */
  private function check($token_to_expect) {
    $type = $this->currentToken->getType();
    $correct = $type == $token_to_expect;
    $message = $correct ? 'ok' : "expected $token_to_expect";
    $message = "$type found, " . $message . " on line " . $this->currentToken->getLine();
    // \Drupal::logger('cfs_mail_parser')->notice($message);
    if (!$correct) {
      throw new \Exception($message);
    }
  }

  /**
   * Sets the current token to the next token in the array and returns it.
   *
   * @return \Drupal\cfs_mail\Parser\Token
   *   The now current token.
   */
  private function getNext() {
    $this->index++;
    $this->currentToken = $this->tokens[$this->index];
    return $this->currentToken;
  }

}
