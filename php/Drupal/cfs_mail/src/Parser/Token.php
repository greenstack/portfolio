<?php

namespace Drupal\cfs_mail\Parser;

/**
 * Represents a token to be evaluated or interpreted.
 */
class Token {
  /**
   * The type of token this is.
   *
   * @var string
   */
  private $tokenType;
  /**
   * The value stored in the token.
   *
   * @var string
   */
  private $tokenValue;
  /**
   * The line number this token was found on.
   *
   * @var int
   */
  private $lineNumber;

  // @codingStandardsIgnoreStart
  /**
   * The symbol that closes a statement.
   *
   * @var string
   */
  static public $CLOSE_STATEMENT = ']]';

  /**
   * The symbol that starts a statement.
   *
   * @var string
   */
  static public $IF_STATEMENT = "[[if";

  /**
   * The symbol that declares an elseif statement.
   *
   * @var string
   */
  static public $ELSEIF_STATEMENT = "[[elseif";
  
  /**
   * The symbol that declares an endif statement.
   *
   * @var string
   */
  static public $ENDIF_STATEMENT = "[[endif]]";

  /**
   * The symbol that declares an else statement.
   *
   * @var string
   */
  static public $ELSE_STATEMENT = "[[else]]";
  // @codingStandardsIgnoreEnd

  /**
   * Creates a new token with a type and a value.
   *
   * @param string $type
   *   The token type.
   * @param mixed $value
   *   The value to associate with the token.
   * @param int $lineNumber
   *   The line that this token came from.
   */
  public function __construct($type, $value, $lineNumber) {
    $this->tokenType = $type;
    $this->tokenValue = $value;
    $this->lineNumber = $lineNumber;
  }

  /**
   * Retrieves the type of this token.
   *
   * @return string
   *   The token's type.
   */
  public function getType() {
    return $this->tokenType;
  }

  /**
   * Retrieves the token's stored value.
   *
   * @return string
   *   The token's value.
   */
  public function getValue() {
    return $this->tokenValue;
  }

  /**
   * Retrieves the line number for this token.
   *
   * @return int
   *   The line number where this token was found.
   */
  public function getLine() {
    return $this->lineNumber;
  }

}
