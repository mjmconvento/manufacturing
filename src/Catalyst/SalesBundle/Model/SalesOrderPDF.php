<?php

namespace Catalyst\SalesBundle\Model;

use Catalyst\InventoryBundle\Model\Gallery;
use Catalyst\InventoryBundle\Entity\Product;
use FPDF\FPDF;
use DateTime;
use DateTimeZone;

class SalesOrderPDF
{
    public function generate($so, $pm)
    {
        
        $sventry = $so->getEntries();

        //gallery get images
        $counter = 0;
        foreach ($so->getEntries() as $sventry)
        {
            $gallery[$counter] = new Gallery(__DIR__ . '/../../../../web/uploads/images', $sventry->getProduct()->getID());
            $images[$counter] = $gallery[$counter]->getImages();
            $counter++;
        }
                  
        // $so_data = $so->getData();
        if (isset($so_data['bal_amount']))
            $balance = $so_data['bal_amount'];
        else
            $balance = 0.00;

        if (isset($so_data['tax']))
            $tax = $so_data['tax'];
        else
            $tax = 0.00;

        $datenow = new \DateTime('now', new DateTimeZone("Asia/Taipei"));

        if (isset($so_data['dp_amount']))
            $downpayment = $so_data['dp_amount'];
        else
            $downpayment = 0.00;

        $address = $so->getWarehouse()->getAddress();
        $code = $so->getCode();
        $customer = $so->getCustomer()->getName();
        $customernumber = $so->getCustomer()->getContactNumber();
        $date = $so->getDateIssue()->format("M d, Y");
        $issuedby = $so->getUser()->getName();
        // $material = $so->getProduct()->getPMaterialName();
        // $note = $so->getNote();
        $totalprice = $so->getTotalPrice();
        // $due_date = $so->getDateNeed()->format("M d, Y");;
        $warehousenumber = $so->getWarehouse()->getContactNumber();
        $customeraddress = $so->getCustomer()->getAddress();
        $customeremail = $so->getCustomer()->getEmail();
        $downpayment = $so->getDownpayment();
        $payment_method = $so->getPaymentMethod()->getName();
        $balance = $so->getBalance();
        // $payment = $so->getData();
        // $downpayment_method = $pm[($payment["dp_payment_id"]) - 1]->getName();
        // $balance_method = $pm[($payment["bal_payment_id"]) - 1]->getName();


        //check if date is null
        // if ($so->getDateCompleted() != null)
        // {
        //     $completed = $so->getDateCompleted()->format("M d, Y");
        // }
        // else
        // {
        //     $completed = "";  
        // }

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->Image(__DIR__ . '/../Resources/public/pdf/billto.png', 2, 5, 207, 80);
       
        //address and phone number for top header
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(60,7,"","","","C");
        $pdf->SetY(0);
        $pdf->SetX(63);
        $pdf->MultiCell(80,5,"\n\n\n".$address."\nTel. Nos.: ".$warehousenumber."\nEmail Add.: doctorleather.ph@gmail.com",0,"L");
    
        //Job Order
        $pdf->SetY(37);
        $pdf->SetX(2);
        $pdf->AddFont('stac222n_0','','stac222n_0.php');
        $pdf->SetFont('stac222n_0','',30);
        $pdf->Cell(340,-40,"Sales Order",0,1,"C");      
        $pdf->SetY(0);
        $pdf->SetX(2);
        $pdf->SetFont('Arial','b',14);
        $pdf->Cell(310,50,"No:",0,1,"C");        
        $pdf->SetY(10);
        $pdf->SetY(0);
        $pdf->SetX(162);
        $pdf->Cell(330,50,$code,0,1,"L");

        // Customer Name        
        // $pdf->SetFont('stac222n_0','',13); 
        $pdf->SetFont('Arial','',12);
        $pdf->SetY(40);
        $pdf->SetX(30);
        $pdf->Cell(10,10,$customer,"","","L");

        //Customer Address
        $pdf->SetY(49);
        $pdf->SetX(30);
        $pdf->MultiCell(85, 6, $customeraddress, "", "", "");

        //Customer Email         
        $pdf->SetY(65);
        $pdf->SetX(30);
        $pdf->Cell(10,10,$customeremail,"","","L");

        //Date Issued
        $pdf->SetY(49);
        $pdf->SetX(136);
        $pdf->Cell(10,10,$date,"","","C");

        //Due Date
        $pdf->SetY(70);
        $pdf->SetX(158);
        // $pdf->Cell(10,10,$due_date,"","","C");

        //User issued by
        $pdf->SetY(70);
        $pdf->SetX(127);
        $pdf->Cell(10,10,$issuedby,"","","L");

        //Customer Phone Number 
        $pdf->SetY(70);
        $pdf->SetX(30);  
        $pdf->Cell(68,13,$customernumber,"","","L");

        //Date Completed   
        $pdf->SetY(70);
        $pdf->SetX(75); 
        // $pdf->Cell(180,20,$completed,"","","C");
        $pdf->Ln(20);  
        $pdf->SetFont('Arial','B',10);

        //Table header

        $pdf->SetFillColor(169,169,169);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetX(5); 
        $pdf->Cell(23,6,"QTY",1,"","C",true);

        $pdf->Cell(138,6,"DESCRIPTION",1,"","C",true);
        $pdf->Cell(40,6,"AMOUNT",1,"","C",true);
        $pdf->Ln();


        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',12);

        //tables loop
        $x = 0;
        foreach ($so->getEntries() as $sventry)
        {
            $pdf->SetX(5); 
            $pdf->Cell(23,6,$sventry->getQuantity(),1,"","C");
            $pdf->Cell(138,6,$sventry->getProduct()->getName(),1,"","C");
            $pdf->Cell(40,6,$sventry->getPrice(),1,"","C");
            $pdf->Ln();
            $x++;
        }

        for($i=$x;$i<5;$i++)
        {
            $pdf->SetX(5); 
            $pdf->Cell(23,6,"",1,"","C");
            $pdf->Cell(138,6,"",1,"","C");
            $pdf->Cell(40,6,"",1,"","C");
            $pdf->Ln();
        }

        //Boxes
        $pdf->SetX(5); 
        $pdf->Cell(201,53,"",1,"","R");
        $pdf->Ln();

        $pdf->SetY(161);
        $pdf->SetX(5); 
        $pdf->Ln();

        $pdf->SetX(5); 

        //image        
        $pdf->SetY(125);
        $pdf->SetX(0);
        $pdf->Cell(40,8,"PICTURE(s):",0,"","C");
        $pdf->Cell(40,8,"",0,"","C");

        //Images for Sales Order
        $pdf->SetFont('Arial','',8);
        $counter = 0;
        $x = 10;
        foreach ($so->getEntries() as $sventry)
        {
            //Validation if there is image for product
            if(isset($images[$counter][0]))
            {
                $pdf->Image(__DIR__ . '/../../../../web/uploads/images/'.$images[$counter][0], $x, 137, 25, 25);   
            }
            else
            {
                $pdf->Image(__DIR__ . '/../Resources/public/pdf/no_image.png', $x, 137, 25, 25);
            }
            $pdf->SetX($x);
            $pdf->Cell(30,90,$sventry->getProduct()->getName(),0,"","L");
            $counter++;
            $x += 40;
        }

        //NOTES
        $pdf->SetTextColor(0,0,0);
        $pdf->SetY(125);
        $pdf->SetX(5);
        // $pdf->Cell(30,8,"NOTES:",0,""[0],"L");
        $pdf->Cell(30,8,"",0,"","L");
        $pdf->SetY(132);
        $pdf->SetX(6);
        // $pdf->MultiCell(85, 4, $note, "", "", "");
        $pdf->MultiCell(85, 4, "", "", "", "");


        //Text for waiver        
        $pdf->SetY(197);
        $pdf->SetX(5);

        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(105, 4, file_get_contents(__DIR__ . '/../Resources/public/pdf/waiver.txt'), "", "", "");


        if(1==1)
        {

        }
        $pdf->SetFont('Times','b',11);  
        $pdf->SetFillColor(169,169,169);
        $pdf->SetTextColor(255,255,255);


        //Total Amount
        $pdf->SetY(200);
        $pdf->SetX(112);
        $pdf->Cell(35,8,"TOTAL AMOUNT",1,"","C",true);

        $pdf->SetFont('Arial','',12);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(50,8,$totalprice,1,"","C");
        $pdf->Ln();

        //Downpayment
        $pdf->SetFont('Times','b',11);  
        $pdf->SetTextColor(255,255,255);
        $pdf->SetY(208);
        $pdf->SetX(112);
        $pdf->Cell(35,8,"DOWN PAYMENT",1,"","C",true);
        $pdf->SetTextColor(0,0,0);

        $pdf->SetFont('Arial','',12);
        $pdf->Cell(50,8,$downpayment." (".$payment_method.")",1,"","C");
        // if ($payment["dp_status"] == "unpaid")
        // {
        //     $pdf->Cell(50,8,"",1,"","C");
        // }
        // else 
        // {
        //    $pdf->Cell(50,8,$downpayment." (".$downpayment_method.") ",1,"","C");
        // }       
        $pdf->Ln();

        //Balance
        $pdf->SetFont('Times','b',11);  
        $pdf->SetTextColor(255,255,255);
        $pdf->SetY(216);
        $pdf->SetX(112);
        $pdf->Cell(35,8,"BALANCE",1,"","C",true);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',12);         
        $pdf->Cell(50,8,$balance,1,"","C");

        // if ($payment["bal_status"] == "unpaid")
        // {
        //     $pdf->Cell(50,8,$balance,1,"","C");
        // }
        // else 
        // {       
        //     $pdf->Cell(50,8,$balance." (".$balance_method.") ",1,"","C");
        // }
        $pdf->Ln(); 

        //Waiver Heading
        $pdf->SetY(186);
        $pdf->SetX(2);
        $pdf->SetFont('Arial','b',14);
        $pdf->Cell(110,7,"Waiver","","","C");
        $pdf->SetFont('Arial','',8);

        $pdf->SetY(252);
        $pdf->SetX(15);
        $pdf->Cell(10,10,"_______________________________________","","","");
        $pdf->SetFont('Arial','',8);
        $pdf->SetY(256);
        $pdf->SetX(27);
        $pdf->Cell(10,10,"Signature over Printed Name","","","");
        $pdf->SetY(252);
        $pdf->SetX(140);
        $pdf->Cell(10,10,"_______________________________________","","","");

        $pdf->SetY(256);
        $pdf->SetX(168);
        $pdf->Cell(10,10,"Date","","","");
        $pdf->SetY(263);
        $pdf->SetX(10);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(10,10,"Note: Unclaimed items for 90 days will be forfeited","","","");
        $pdf->SetFont('Arial','',7);
        $pdf->SetY(266);
        $pdf->SetX(10);
        $pdf->Cell(10,10,"Date Printed: ".$datenow->format('Y-m-d'),"","","");
        $pdf->SetY(266);
        $pdf->SetX(170);
        $pdf->SetFont('stac222n_0','',17); 
        $pdf->Cell(10,10,"Doctor leather","","","");
        $pdf->Output();
    }

}
