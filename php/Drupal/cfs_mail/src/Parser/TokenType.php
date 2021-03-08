<?php

namespace Drupal\cfs_mail\Parser;

/**
 * A class containing constants for MailParse token types.
 */
abstract class TokenType {
  const T_CHARPLUS = "charplus";
  const T_IF = "if";
  const T_ELSE = "else";
  const T_ELSEIF = "elseif";
  const T_END = "end";
  const T_VAR = "var";
  const T_ENDIF = "endif";
  const T_EOF = "eof";

}
