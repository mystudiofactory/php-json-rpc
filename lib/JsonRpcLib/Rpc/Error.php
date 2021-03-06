<?php

namespace JsonRpcLib\Rpc;

/**
 * @see https://github.com/zendframework/zf2/issues/2456#issuecomment-15134060
 *
 * The correct interpretation is:
 *
 * -32768 to -32000 / pre-defined errors (#): These error-codes and their
 *  meanings are defined by the JSON-RPC 2.0 specification.
 *
 * -32099 to -32000 / implementation-defined server-errors (*): These error-codes
 *  can be used for JSON-RPC 2.0 servers/frameworks/libraries for
 *  their own errors (e.g. if the JSON-RPC-server itself fails).
 *
 * All other codes (>-32000 or <-32768) / application defined errors: These can
 * freely be used by procedures which are called via JSON-RPC.
 *
 *  -32768                -32099   -32000             0
 * ____|_____________________|________|_______________|_
 *     |#####################|########|               |
 *     |                     |********|               |
 */
class Error
{
    /**
     * Parse error - Invalid JSON was received by the server.
     * An error occurred on the server while parsing the JSON text.
     */
    const PARSE_ERROR = -32700;

    /**
     * Invalid Request - The JSON sent is not a valid Request object.
     */
    const INVALID_REQUEST = -32600;

    /**
     * Method not found - The method does not exist / is not available.
     */
    const METHOD_NOT_FOUND = -32601;

    /**
     * Invalid params - Invalid method parameter(s).
     */
    const INVALID_PARAMS = -32602;

    /**
     * Internal error - Internal JSON-RPC error.
     */
    const INTERNAL_ERROR = -32603;

    /**
     * Server error - Reserved for implementation-defined server-errors.
     *
     * -32000 to -32099
     */
    const SERVER_ERROR = -32000;

    /**
     * @var array
     */
    protected static $messages = array(
        self::PARSE_ERROR       => 'Parse error',
        self::INVALID_REQUEST   => 'Invalid Request',
        self::METHOD_NOT_FOUND  => 'Method not found',
        self::INVALID_PARAMS    => 'Invalid params',
        self::INTERNAL_ERROR    => 'Internal error',
        self::SERVER_ERROR      => 'Server error',
    );

    /**
     * A Number that indicates the error type that occurred.
     * This MUST be an integer.
     *
     * @var integer
     */
    private $code = null;

    /**
     * A String providing a short description of the error.
     * The message SHOULD be limited to a concise single sentence.
     *
     * @var string
     */
    private $message = null;

    /**
     * A Primitive or Structured value that contains additional information about the error.
     * This may be omitted.
     * The value of this member is defined by the Server (e.g. detailed error information, nested errors etc.).
     *
     * @var array
     */
    private $data = null;

    /**
     * Constructor
     *
     * @param  string $message
     * @param  int    $code
     * @param  mixed  $data
     * @return void
     */
    public function __construct($message = null, $code = self::SERVER_ERROR, $data = null)
    {
        $this->setMessage($message)
            ->setCode($code)
            ->setData($data)
        ;
    }

    /**
     * Set error code
     *
     * @param  int   $code
     * @return Error
     */
    public function setCode($code)
    {
        if (!is_scalar($code)) {
            return $this;
        }

        $this->code = (int) $code;

        return $this;
    }

    /**
     * Get error code
     *
     * @return int|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set error message
     *
     * @param  string $message
     * @return Error
     */
    public function setMessage($message)
    {
        if (!is_scalar($message)) {
            return $this;
        }

        $this->message = (string) $message;

        return $this;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set error data
     *
     * @param  mixed $data
     * @return Error
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get error data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Cast error to array
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
            'data'    => $this->getData(),
        );

        if (null === $data['data']) {
            unset($data['data']);
        }

        return $data;
    }

    /**
     * Cast error to JSON
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Cast to string (JSON)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    public static function __callStatic($value, $args)
    {
        $code = constant(sprintf('%s::%s', get_called_class(), $value));

        if (array_key_exists($code, self::$messages)) {
            return self::$messages[$code];
        }

        return $value;
    }
}
