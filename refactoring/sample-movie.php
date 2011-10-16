#!/usr/bin/php
<?php
class Movie{
    const CHILDRENS = 2;
    const REGULAR = 0;
    const NEW_RELEASE = 1;
  
    private $_title;
    private $_priceCode;
  
    public function __construct($title, $priceCode)
    {
        $this->_title = $title;
        $this->_priceCode = $priceCode;
    }

    public function getPriceCode()
    {
        return $this->_priceCode;
    }

    public function setPriceCode($arg)
    {
        $this->_priceCode = $arg;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getCharge($daysRented)
    {
        $result = 0;

        switch ($this->getPriceCode()){
        case Movie::REGULAR:
            $result += 2;
            if($daysRented > 2){
                $result += ($daysRented -2) * 1.5;
            }
            break;
        case Movie::NEW_RELEASE:
            $result += $daysRented * 3;
            break;
        case Movie::CHILDRENS:
            $result += 1.5;
            if( $daysRented > 3 ){
                $result += ($daysRented -3 ) * 1.5;
            }
            break;
        }

        return $result;
    }

    public function getFrequentRenterPoints($daysRented)
    {
        if( ($this->getPriceCode() === Movie::NEW_RELEASE ) && $daysRented > 1){
            return 2;
        }else{
            return 1;
        }
    }
}

class Rental {
    private $_movie;
    private $_daysRented;
  
    public function __construct($movie, $daysRented)
    {
        $this->_movie = $movie;
        $this->_daysRented = $daysRented;
    }
  
    public function getDaysRented()
    {
        return $this->_daysRented;
    }
  

    public function getMovie()
    {
        return $this->_movie;
    }


    public function getCharge()
    {
        return $this->_movie->getCharge($this->_daysRented);
    }

    public function getFrequentRenterPoints()
    {
        return $this->_movie->getFrequentRenterPoints($this->_daysRented);
    }


}


class Customer {
    private $_name;
    private $_rentals =  array();

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function addRental($arg)
    {
        $this->_rentals[] = $arg;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function statement()
    {
        $rentals = $this->_rentals;
        $result = "Rental Record for " . $this->getName() . "\n";
    
        foreach($rentals as $rental){
            $each = $rental;

            $result .= "\t" . $each->getMovie()->getTitle() . "\t"
                . $each->getCharge() . "\n";
      
        }
    
        $result .= "Amount owed is " . $this->getTotalCharge()  . "\n";
        $result .= "You earned " . $this->getTotalFrequentRenterPoints() . " frequent renter points\n";
        return $result;

    }

    public function htmlStatement()
    {
        $rentals = $this->_rentals;
        $result = "<h1>Rentals for <em>" . $this->getName() . "</em?</ht1><p>\n";
        foreach($rentals as $rental){
            $each = $rental;
            
            $result .= $each->getMovie()->getTitle() . ": "
                . $this->getCharge() . "<br>\n";
        }

        $result .= "<p>you owe<em>" . $this->getTotalCharge() . "</em></p>\n";
        $result .= "On this rental you earned <em> " . $this->getTotalFrequentRenterPoints(). "</em> frequent renter points<p>";
        return $result;
    }

    private function getTotalCharge()
    {
        $result = 0;
        foreach( $this->_rentals as $rental){
            $result += $rental->getCharge();
        }
        return $result;
    }

    private function getTotalFrequentRenterPoints()
    {
        $result = 0;
        foreach( $this->_rentals as $rental){
            $result += $rental->getFrequentRenterPoints();
        }

        return $result;

    }

 
}

require('lime.php');
$t = new lime_test(null, new lime_output_color());
$movie1 = new Movie('Avatar', 1);
$movie2 = new Movie('Beauty And Beast', 2);
$rented = new Rental($movie1, 5);

$customer = new Customer('DQNEO');
$customer->addRental($rented);
$customer->addRental(new Rental($movie2 , 4));
$customer->addRental(new Rental(new Movie('Titanic', 0) , 4));

$ret1 = "Rental Record for DQNEO\n\tAvatar\t15\n\tBeauty And Beast\t3\n\tTitanic\t5\nAmount owed is 23\nYou earned 4 frequent renter points\n";

$t->is($customer->statement(), $ret1);

$c2 = new Customer('PYON');
$c2->addRental(new Rental(new Movie('Avatar', 1) , 1));
$c2->addRental(new Rental(new Movie('Cars', 2) , 1));
$c2->addRental(new Rental(new Movie('Titanic', 0) , 1));

$ret2 = "Rental Record for PYON\n\tAvatar\t3\n\tCars\t1.5\n\tTitanic\t2\nAmount owed is 6.5\nYou earned 3 frequent renter points\n";

$t->is($c2->statement(), $ret2);

$c3 = new Customer('PYON');
$c3->addRental(new Rental(new Movie('Avatar', 1) , 2));

$t->is($c3->statement(), "Rental Record for PYON\n\tAvatar\t6\nAmount owed is 6\nYou earned 2 frequent renter points\n");



$c4 = new Customer('PYON4');
$c4->addRental(new Rental(new Movie('Titanic', 0) , 3));

$t->is($c4->statement(), "Rental Record for PYON4\n\tTitanic\t3.5\nAmount owed is 3.5\nYou earned 1 frequent renter points\n");
