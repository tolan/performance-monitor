<?php

namespace PM\Main\Http\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This class defines methods of http request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Method extends Enum {
    const GET    = 'GET';
    const POST   = 'POST';
    const PUT    = 'PUT';
    const DELETE = 'DELETE';
}


