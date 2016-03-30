<?php  namespace Netinteractive\Combiner;

/**
 * Class ConfigException
 * @package Netinteractive\Combiner
 */
class ConfigException extends \Exception
{
    /**
     * @param string $keyName
     * @param string $skin
     * @param string $type
     * @param string $mode
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($keyName, $skin, $type, $mode, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = sprintf(_('Key %s missing in config file for skin %s, type %s, mode %s !'), $keyName, $skin, $type, $mode);
        }
        $this->message = $message;
    }
}

/**
 * Class NoConfigException
 * @package Netinteractive\Combiner
 */
class NoConfigException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _("Coudn't load config file!");
        }
        $this->message = $message;
    }
}
