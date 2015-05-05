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
    $totalAmount = 0;
    $frequentRenterPoints = 0;
    $rentals = $this->_rentals;
    $result = "Rental Record for " . $this->getName() . "\n";
    
    foreach($rentals as $rental){
      $thisAmount = 0;
      $each = $rental;

      switch ($each->getMovie()->getPriceCode()){
      case Movie::REGULAR:
	$thisAmount += 2;
	if($each->getDaysRented() > 2){
	  $thisAmount += ($each->getDaysRented() -2) * 1.5;
	}
	break;
      case Movie::NEW_RELEASE:
	$thisAmount += $each->getDaysRented() * 3;
	break;
      case Movie::CHILDRENS:
	$thisAmount += 1.5;
	if( $each->getDaysRented() > 3 ){
	  $thisAmount += ($each->getDaysRented() -3 ) * 1.5;
	}
	break;
      }

      $frequentRenterPoints ++;
      if( ($each->getMovie()->getPriceCode() === Movie::NEW_RELEASE ) && $each->getDaysRented() > 1){
	$frequentRenterPoints ++;
      }

      $result .= "\t" . $each->getMovie()->getTitle() . "\t"
	. $thisAmount . "\n";
      $totalAmount += $thisAmount;
      
    }
    
    $result .= "Amount owed is " . $totalAmount  . "\n";
    $result .= "You earned " . $frequentRenterPoints . " frequent renter points\n";
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
