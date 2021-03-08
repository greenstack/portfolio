<?php

namespace Drupal\cfs_mail\Parser;

use Drupal\Core\Messenger\MessengerTrait;

/**
 * A facade for performing all the required work on a block of MailParse text.
 */
class MailParser {

  use MessengerTrait;

  /**
   * Parses the mail block.
   *
   * It will first tokenize the text, then parse it, and finally interpret the
   * block of MailParse text.
   *
   * @param string $interpreter_id
   *   The name of the section being parsed.
   * @param string $code
   *   The code being parsed.
   * @param mixed[] $args
   *   Arguments various hooks can use to eval variables in $code.
   *
   * @return bool|string
   *   False if the parsing failed; otherwise, the interpreted string.
   */
  public static function parse($interpreter_id, $code, array &$args = []) {
    // The empty string is valid, and if all we get is the empty string,
    // then there's no sense trying to perform all the other actions.
    if (empty($code)) {
      return "";
    }
    $tokens = Tokenizer::tokenize($code);
    $parser = new Parser($tokens);
    try {
      $parser->checkSyntax();
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addMessage($e->getMessage(), 'error');
      return FALSE;
    }
    \Drupal::moduleHandler()->alter('mailparse_args', $args, $interpreter_id);
    $interpreter = new Interpreter($tokens, $args);
    return $interpreter->interpret();
  }

}
