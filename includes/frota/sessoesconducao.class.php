<?php
class SessaoConducao
{
    private $sessaoActiva;
    private $_matricula, $_utilizador;
    private $_kmsInicio, $_kmsFim;

    public function __CONSTRUCT($utilizador, $matricula, $kmsInicio)
    {
        $sessaoActiva = true;

    }

    public function FecharSessao($kmsFim, $obs)
    {
        $sessaoActiva = false;
    }
}

?>