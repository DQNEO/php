<?php

// TDDの練習(Moneyクラス)
error_reporting( E_ALL || E_STRICT );
ini_set('display_errors', 'On');

require_once('/home/userdqn/8122/trunk/lib/Lime/lime.php');
$t = new lime_test(null, new lime_output_color() );

$t->diag('TEST of Money Class');

/*

TODO
- ok $5 * 2 = $10
- ok 副作用
- ok equals()
- ok public field
- ok 5 CHF * 2 = 10 CHF
- ok generalize of equals
- ok comparison of Dollar and Franc

- Exchange Currencies
- まるめ処理
- nullとの等価性
- objectとの等価性
- duplication of Dollar and Franc
- generalize of times
*/

// multiplication
$five = Money::dollar(5);
$t->ok( $five->times(2)->equals(Money::dollar(10)) );
$t->ok( $five->times(3)->equals(Money::dollar(15)) );
unset($five);

// multiplication
$five = Money::franc(5);
$t->ok( $five->times(2)->equals(Money::franc(10)) );
$t->ok( $five->times(3)->equals(Money::franc(15)) );
unset($five);


// equality
$five = Money::dollar(5);
$t->ok(  $five->equals( Money::dollar(5) ), 'equals');
$t->ok(! $five->equals( Money::dollar(6) ), 'equals');

$five = Money::franc(5);
$t->ok(  $five->equals( Money::franc(5) ), 'equals');
$t->ok(! $five->equals( Money::franc(6) ), 'equals');
$t->ok(! $five->equals( Money::dollar(5) ), 'equals');

abstract class Money {
    protected $amount;

    function equals($money)
    {
        return ( get_class($this) === get_class($money) &&  $this->amount === $money->amount);
    }

	static function dollar($amount)
	{
	  return new Dollar($amount);
	}

	static function franc($amount)
	{
	  return new Franc($amount);
	}

	abstract function times($multiplier);



}

class Dollar extends Money {

    function __construct($amount)
    {
        $this->amount = $amount;
    }

    function times($multiplier)
    {
        return new Dollar($this->amount * $multiplier);
    }


}

class Franc extends Money {

    function __construct($amount)
    {
        $this->amount = $amount;
    }

    function times($multiplier)
    {
        return new Franc($this->amount * $multiplier);
    }

}