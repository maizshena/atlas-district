<?php

namespace App\Exceptions;

use RuntimeException;

// app/Exceptions/ConflictException.php
// reported as http 409 with a readable message
class ConflictException extends RuntimeException {}
