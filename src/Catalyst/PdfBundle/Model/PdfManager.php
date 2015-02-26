<?php

namespace Catalyst\PdfBundle\Model;

use Doctrine\ORM\EntityManager;
use Ensepar\Html2pdfBundle\Factory\Html2pdfFactory;


class PdfManager 
{
    protected $em;
    protected $pdf;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function newPdf(){
        return $this->get('html2pdf_factory');
    }
}