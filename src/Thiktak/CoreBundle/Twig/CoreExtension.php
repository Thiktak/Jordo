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
            'timeAgo'     => new \Twig_Function_Method($this, 'timeAgo'),
        );
    }

    public function getFilters()
    {
        return array(
            'printDate'  => new \Twig_Filter_Method($this, 'printDate'),
            'timeAgo'    => new \Twig_Filter_Method($this, 'timeAgo'),
        );
    }

    function timeAgo($tm, $rcs = 0) {
       $tm = $tm instanceof \DateTime ? $tm : new \DateTime($tm);
       $cur_tm = time(); $dif = $cur_tm-$tm->format('U');
       $pds = array('seconde','minute','heure','jour','semaine','mois','année','decade');
       $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
       for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

       $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
       if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
       return str_replace('ss', 's', $x);
    }

    public function printDate($DateTime = null, $short = true, $format = null)
    {
        if( !($DateTime instanceof \DateTime) )
            $DateTime = new \DateTime($DateTime);
        
        $return = $DateTime->format($format ?: 'd/m/Y h\hi');

        if( $short )
            $return = preg_replace('`(:00){1,}$`', null, $return);

        return $return;
    }

    public function arrayIndex($index, array $array, $else = null)
    {
        if( is_object($index) )
            $index = (string) $index;

        if( array_key_exists($index, $array) )
            return $array[$index];

        return $else;
    }

    public function transExists($trans, $return = false)
    {
        if( ($trans2 = $this->translator->trans($trans)) != $trans )
            return $trans2;
        return $return ? $trans : null;
    }

    public function getName()
    {
        return 'core_extension';
    }
}