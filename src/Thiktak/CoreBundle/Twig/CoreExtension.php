<?php

namespace Thiktak\CoreBundle\Twig;

class CoreExtension extends \Twig_Extension
{
    protected $translator;

    public function __construct($translator)
    {
        $this -> translator = $translator;
    }

    public function getFunctions()
    {
        return array(
            'arrayIndex'  => new \Twig_Function_Method($this, 'arrayIndex'),
            'transExists' => new \Twig_Function_Method($this, 'transExists'),
        );
    }

    public function arrayIndex($index, array $array, $else = null)
    {
        if( is_object($index) )
            $index = (string) $index;

        if( array_key_exists($index, $array) )
            return $array[$index];

        return $else;
    }

    public function transExists($trans)
    {
        if( ($trans2 = $this->translator->trans($trans)) != $trans )
            return $trans2;
        return null;
    }

    public function getName()
    {
        return 'core_extension';
    }
}