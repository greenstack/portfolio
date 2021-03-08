<?php

/**
 * @file
 * Defines the hooks exposed by CFS Mail.
 */

/**
 * Alters the parameters exposed to a transition.
 *
 * Used to alter the parameters sent through the transition, regardless of the
 * transition id. It allows developers to edit or add parameters before being
 * sent in to the transition. Please note that this is not where the values of
 * tokens is set.
 *
 * @param array $params
 *   The parameters being passed into the transition.
 * @param string $id
 *   The id of the transition message whose params are being altered.
 */
function hook_mail_transition_params_alter(array &$params, $id) {

}

/**
 * Alters the parameters for a transition with the specified ID.
 *
 * Like `hook_mail_transition_params_alter`, but will only be fired for the
 * transition message with the specific ID.
 *
 * @param array $params
 *   The parameters being passed into the transition.
 * @param string $id
 *   The id of the transition message whose params are being altered.
 *
 * @see hook_mail_transition_params_alter
 */
function hook_mail_transition_TRANSITION_ID_params_alter(array &$params, $id) {

}

/**
 * Evaluates the value of a variable in a Mailparse block.
 *
 * Determines the truthiness of a variable in a Mailparse block. Note that if
 * any implementation says that $varname is false, the whole block will return
 * false. Therefore, your implementation should return true if it's not the
 * variable you're looking for.
 *
 * @param string $varname
 *   The name of the variable being passed evaluated in mailparse.
 * @param array $args
 *   The arguments passed to the interpreter.
 *
 * @return bool
 *   The evaluation of $varname.
 */
function hook_mailparse_eval($varname, array $args) {
  // The example below shows using args and the varname to determine the value
  // of the if statement in the Mailparse block.
  return $varname == "my_varname" ?
    array_key_exists('data', $args['new_arg']) :
    TRUE;
}

/**
 * Alters the arguments passed into the mailparser before interpretation.
 *
 * The value passed into $args can be of any type, as their truthiness
 * will be determined in hook_mailparse_eval. Invoked in MailParser::parse.
 *
 * @param array &$args
 *   The arguments to pass into the array. Equivalent to mailparse_eval $args.
 * @param string $interpreter_id
 *   The name of the current interpretation.
 */
function hook_mailparse_args_alter(array &$args, $interpreter_id) {
  // Say we wanted to add an argument here.
  $args['new_arg'] = ['data' => 'point'];
  // You can really add as many things as you want. Sometimes, data is passed
  // in, and you never really know the order it will be called in, so editing
  // existing data may not be the best idea.
}

/**
 * Alters the token replacements in the mail message.
 *
 * This hook allows you to set the values of the tokens that will be used in
 * producing the email. These values should not be objects or arrays, as this
 * can cause problems when inserting the values into the email text. Use the
 * $params parameter to get information, such as the node and other information.
 *
 * @param array $tokens
 *   An array passed by reference with all token keys and their values.
 */
function hook_mail_tokens_alter(array &$tokens, $params) {
  // Say a user made a message with the :mischief token defined in the example
  // below. We set the value of the token this way:
  $tokens[':mischief'] = rand(0, 100);
  // The mailparser would then be able to replace all instances of :mischief
  // with that random value. Note that this value isn't saved from call to call
  // as this hook is called each time a message is sent.
  // Furthermore, you have the ability to edit other tokens:
  $tokens[':subtotal'] = 0;
}

/**
 * Allows modules to define their own tokens for CFS Mail.
 *
 * This is only used
 * when building a list of tokens that the module can use.
 *
 * @param array $tokens
 *   The tokens available and their descriptions.
 */
function hook_mail_token_list_alter(array &$tokens) {
  // Say we wanted to define a token called 'mischief'. We'd do so this way:
  $tokens[':mischief'] = 'A mischevious token with unknown behavior.';
  // Do that, and you've got what you want.
  // If you wanted to modify a token's description because your module modifies
  // the behavior, you can accomplish that this way:
  if (array_key_exists(':subtotal')) {
    $tokens[':subtotal'] = 'Change it this way.';
  }

  // We don't recommend removing any keys, as this will hide information from
  // those who'd want to see it.
}
