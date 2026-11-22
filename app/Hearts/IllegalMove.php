<?php

namespace App\Hearts;

use RuntimeException;

/**
 * Thrown when a Player attempts an action the rules or the current state do not allow.
 */
class IllegalMove extends RuntimeException {}
