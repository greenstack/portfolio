<?php

namespace Drupal\cfs_mail\Parser;

/**
 * Interprets parsed and syntactically correct MailParse code.
 */
class Interpreter {
  /**
   * The tokens being interpreted/the AST.
   *
   * @var Drupal\cfs_mail\Parser\Token[]
   */
  private $tokens;

  /**
   * The arguments being used to help interpret the message.
   *
   * @var array
   */
  private $arguments;

  /**
   * Builds an Interpreter.
   *
   * @param array $tokens
   *   The tokens the interpreter will analyze.
   * @param array $arguments
   *   The arguments used to help interpret the message.
   */
  public function __construct(array $tokens, array &$arguments) {
    $this->tokens = $tokens;
    $this->arguments = $arguments;
  }

  /**
   * Interprets the provided tokens.
   *
   * @return string
   *   The fully interpreted message.
   */
  public function interpret() {
    // This is to prevent off by one errors.
    $index = -1;
    return $this->evalBlock($index);
  }

  /**
   * Evaluates a single block of MailParse code.
   *
   * @param int $index
   *   The current location in the list of tokens.
   */
  private function evalBlock(&$index) {
    $string = '';
    while (TRUE) {
      $token = $this->getNextToken($index);
      switch ($token->getType()) {
        case TokenType::T_CHARPLUS:
          $string .= $token->getValue();
          continue 2;

        case TokenType::T_IF:
          $string .= $this->evalCondition($index);
          continue 2;

        default:
          $this->getPreviousToken($index);
          break 2;
      }
    }
    return $string;
  }

  /**
   * Evaluates a conditional statement.
   *
   * @param int $index
   *   The current location in the list of tokens.
   */
  private function evalCondition(&$index) {
    $skip_else = FALSE;
    $string = '';
    $var_val = FALSE;
    while (TRUE) {
      $token = $this->getNextToken($index);
      switch ($token->getType()) {
        case TokenType::T_VAR:
          $var_val = $this->checkVar($token);
          continue 2;

        case TokenType::T_ELSE:
          $temp = $this->evalBlock($index);
          if (!$skip_else) {
            $string .= $temp;
          }
          continue 2;

        // This handles both elseif and if since t_end always follows them.
        case TokenType::T_END:
          $temp = $this->evalBlock($index);
          if (!$skip_else && $var_val) {
            $string .= $temp;
            $skip_else = TRUE;
          }
          continue 2;

        case TokenType::T_ENDIF:
          break 2;
      }
    }
    return $string;
  }

  /**
   * Checks the value of a variable.
   *
   * @param \Drupal\cfs_mail\Parser\Token $token
   *   The token being evaluated.
   */
  private function checkVar(Token $token) {
    $result = TRUE;
    $module_handler = \Drupal::moduleHandler();
    foreach ($module_handler->getImplementations('mailparse_eval') as $module) {
      $function = $module . "_mailparse_eval";
      $pre = $function($token->getValue(), $this->arguments);
      if ($pre === NULL) {
        continue;
      }
      // Favor the naysayers.
      $result = $pre && $result;
      $converted = $this->convBool($result);
      $name = $token->getValue();
      \Drupal::logger('cfs_mail_parser')->notice("$name eval'd to $converted");
    }
    return $result;
  }

  /**
   * Converts a boolean to a string. Useful for debugging.
   *
   * @param bool $bool
   *   The value to convert.
   *
   * @return string
   *   The text representation of the boolean value.
   */
  private function convBool($bool) {
    return $bool !== FALSE ? 'true' : 'false';
  }

  /**
   * Runs through a block until it finds the next conditional statement.
   *
   * @param int $index
   *   The current token index.
   */
  private function runThroughBlock(&$index) {
    // If an if is found, set endif counter up by one. Reduce that by one when
    // it is found and when it hits zero, return.
    $endif_count = 1;
    while ($endif_count > 0) {
      $token = $this->getNextToken($index);
      switch ($token->getType()) {
        case TokenType::T_IF:
          $endif_count++;
        case TokenType::T_ENDIF:
          $endif_count--;
        case TokenType::T_ELSEIF:
        case TokenType::T_ELSE:
          $this->getPreviousToken($index);
          return;

        default:
          continue 2;
      }
    }
  }

  /**
   * Retrieves the next token in the message.
   *
   * @param int $index
   *   The index to search.
   *
   * @return Drupal\cfs_mail\Parser\Token
   *   The next token.
   */
  private function getNextToken(&$index) {
    $index++;
    return $this->tokens[$index];
  }

  /**
   * Gets the previous token in the message.
   *
   * @param int $index
   *   The current location in the list of tokens.
   *
   * @return Drupal\cfs_mail\Parser\Token
   *   The previous token.
   */
  private function getPreviousToken(&$index) {
    $index--;
    return $this->tokens[$index];
  }

}
