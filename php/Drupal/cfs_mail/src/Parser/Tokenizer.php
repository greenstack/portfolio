<?php

namespace Drupal\cfs_mail\Parser;

/**
 * Tokenizes a block of MailParse text, then passes it for parsing.
 */
class Tokenizer {

  /**
   * Takes a string and turns it into an array of tokens.
   *
   * @param string $string
   *   The string containing the text to be parsed.
   *
   * @return mixed[]
   *   An array containing the tokens in the order they are found.
   */
  public static function tokenize($string) {
    $tokens = [];
    $line_number = 1;
    $current = 0;
    $in_statement = FALSE;
    for ($i = 0; $i < strlen($string); $i++) {

      if (substr($string, $i, 1) === "\n") {
        $line_number++;
      }

      if ($in_statement && strpos($string, ' ', $i) === $i) {
        $current++;
        continue;
      }
      elseif (strpos($string, Token::$CLOSE_STATEMENT, $i) === $i) {
        $t = new Token(TokenType::T_END, Token::$CLOSE_STATEMENT, $line_number);
      }
      elseif (strpos($string, Token::$IF_STATEMENT, $i) === $i) {
        $t = new Token(TokenType::T_IF, Token::$IF_STATEMENT, $line_number);
      }
      elseif (strpos($string, Token::$ELSEIF_STATEMENT, $i) === $i) {
        $t = new Token(TokenType::T_ELSEIF, Token::$ELSEIF_STATEMENT, $line_number);
      }
      elseif (strpos($string, Token::$ENDIF_STATEMENT, $i) === $i) {
        $t = new Token(TokenType::T_ENDIF, Token::$ENDIF_STATEMENT, $line_number);
      }
      elseif (strpos($string, Token::$ELSE_STATEMENT, $i) === $i) {
        $t = new Token(TokenType::T_ELSE, Token::$ELSE_STATEMENT, $line_number);
      }
      else {
        continue;
      }

      $str = substr($string, $current, $i - $current);
      if (preg_match('/^[\r\n|\r|\n]*$/', $str)) {
        $str = '';
      }

      $token_type = $in_statement && preg_match('/^[a-zA-Z_][a-zA-Z_0-9]*$/', $str) ? TokenType::T_VAR : TokenType::T_CHARPLUS;

      if ($t->getType() == TokenType::T_ELSEIF || $t->getType() == TokenType::T_IF) {
        $in_statement = TRUE;
      }
      elseif ($t->getType() == TokenType::T_END) {
        $in_statement = FALSE;
      }
      if (strlen($str) > 0) {
        $tokens[] = new Token($token_type, $str, $line_number);
      }
      // $offset = (cond) ? -1 : 0;
      $tokens[] = $t;
      // + $offset;
      $i += strlen($t->getValue()) - 1;
      $current = $i + 1;
    }
    $last_str = substr($string, $current, $i - $current);
    if (strlen($last_str)) {
      $tokens[] = new Token(TokenType::T_CHARPLUS, $last_str, $line_number);
    }
    $tokens[] = new Token(TokenType::T_EOF, NULL, $line_number);
    return $tokens;
  }

  /**
   * Determines the token type based on context and contents of the string.
   *
   * @param string $string
   *   The contents of the string. If <<, it will set the "in_statement" flag.
   *
   * @return string
   *   The type of the token.
   */
  private static function setTokenType($string) {
    static $in_statement = FALSE;
    $token_type = NULL;

    if (!$in_statement) {
      return 'STRING';
    }

    switch ($string) {
      case '<<if':
        $in_statement = TRUE;
        return 'IF_STATEMENT';

      case '<<elseif':
        $in_statement = TRUE;
        return 'ELSEIF_STATEMENT';

      case '<<endif>>':
        $in_statement = TRUE;
        return 'ENDIF_STATEMENT';

      case '<<else>>':
        $in_statement = TRUE;
        return 'ELSE_STATEMENT';

      case '>>':
        $in_statement = TRUE;
        return 'CLOSE_STATEMENT';

      default:
        return 'VARIABLE';
    }
  }

}
