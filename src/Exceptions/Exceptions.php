<?php namespace Veemo\Auth\Exceptions;


class UserAlreadyActivatedException extends \RuntimeException {}
class UserNotFoundException extends \OutOfBoundsException {}
class UserNotActivatedException extends \RuntimeException {}
class UserBannedException extends \RuntimeException {}
class UserExistsException extends \UnexpectedValueException {}
