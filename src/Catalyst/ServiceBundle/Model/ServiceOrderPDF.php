<?php

namespace Catalyst\CatalystService\Model;

use Catalyst\ServiceBundle\Entity\ServiceOrder;
use Catalyst\ServiceBundle\Entity\SVEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\UserBundle\Entity\User;
use Catalyst\ValidationException;
use Doctrine\ORM\EntityManager;
use DateTime;
use FPDF;

Class ServiceOrderPDFController extends CrudController
{
	public function generatePDF($id, $service_order)
	{
        require('bundles/catalystservice/fpdf/fpdf.php');

        $repository = $this->getDoctrine()
            ->getRepository('CatalystServiceBundle:ServiceOrder');
        $service_order = $repository->find($id);

        $repository = $this->getDoctrine()
            ->getRepository('CatalystServiceBundle:SVentry');
        $sventry = $repository->findBy(array('so_id' => $id,));

        $address = $print->getWarehouse()->getAddress();
        $balance = $print->getBalance();
        $code = $print->getCode();
        $color = $print->getProduct()->getPColorName();
        $customer = $print->getCustomer()->getName();
        $customernumber = $print->getCustomer()->getContactNumber();
        $date = $print->getDateIssue()->format("M d, Y");
        $downpayment = $print->getDownpayment();
        $issuedby = $print->getUser()->getUsername();
        $tax = $print->getTax();
        $totalprice = $print->getTotalPrice();
        $warehousenumber = $print->getWarehouse()->getContactNumber();
	}

}    