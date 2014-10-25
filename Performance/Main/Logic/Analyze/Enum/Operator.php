<?php

namespace PM\Main\Logic\Analyze\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This class defines types of operators.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Operator extends Enum {
    const OP_AND  = 'AND';
    const OP_OR   = 'OR';
    const OP_NAND = 'NAND';
    const OP_NOR  = 'NOR';
    const OP_XOR  = 'XOR';
    const OP_XNOR = 'XNOR';
}
