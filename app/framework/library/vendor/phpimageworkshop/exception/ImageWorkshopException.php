<?php

namespace phpimageworkshop\exception;

use phpimageworkshop\exception\ImageWorkshopBaseException as ImageWorkshopBaseException;

// If no autoloader, uncomment these lines:
require_once(__DIR__.'/ImageWorkshopBaseException.php');

/**
 * ImageWorkshopException
 * 
 * Manage ImageWorkshop exceptions
 * 
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshopException extends ImageWorkshopBaseException
{
}