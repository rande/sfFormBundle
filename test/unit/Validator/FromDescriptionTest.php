<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

use Bundle\FormBundle\Validator\FromDescription;
use Bundle\FormBundle\Validator\String;
use Bundle\FormBundle\Validator\Decorator;
use Bundle\FormBundle\Validator\Integer;

use Symfony\Components\Yaml\Yaml;

// var_dump(Yaml::load('[ { toto:titi } ]'));
// die();

$t = new lime_test(98);

// var_dump($results); die();
$tests = array(
  // 'Bundle\FormBundle\Validator\Integer',
  // 'Bundle\FormBundle\Validator\Integer()',
  // 'Bundle\\FormBundle\\Validator\\Integer({min: 18})',
  // 'Bundle\\FormBundle\\Validator\\Integer(  {  min:  18  }  )',
  // 'Bundle\\FormBundle\\Validator\\Integer({min: 18}, {required: "This is required"})',
  '(Bundle\\FormBundle\\Validator\\Integer)',
  // '
  //  (Bundle\\FormBundle\\Validator\\Integer)
  // ',
  // '(
  //   Bundle\\FormBundle\\Validator\\Integer
  //  )',
  // 
  // 'Bundle\\FormBundle\\Validator\\Integer and Bundle\\FormBundle\\Validator\\String',
  // 'Bundle\\FormBundle\\Validator\\Integer or Bundle\\FormBundle\\Validator\\String',
  // 'Bundle\\FormBundle\\Validator\\Integer and (Bundle\\FormBundle\\Validator\\String or Bundle\\FormBundle\\Validator\\Email)',
  // 
  // 'age:Bundle\\FormBundle\\Validator\\Integer',
  // 'age:Bundle\\FormBundle\\Validator\\Integer()',
  // 'age:Bundle\\FormBundle\\Validator\\Integer({min: 18})',
  // 'age:Bundle\\FormBundle\\Validator\\Integer({min: 18}, {required: "This is required"})',
  // '(age:Bundle\\FormBundle\\Validator\\Integer)',
  // '
  //   (age:Bundle\\FormBundle\\Validator\\Integer)
  // ',
  // '(
  //   age:Bundle\\FormBundle\\Validator\\Integer
  //  )',
  // 
  // 'age == password',
  // 'age ==() password',
  // 'age ==({}, {invalid: "Not equal."}) password',
  // 'age ==(  {  },  {  invalid:  "Not equal."  }  ) password',
  // 'age ==({required: true}, {invalid: "Not equal."}) password',
  // 'age ==({}) password',
  // "age
  //  ==
  //  password",
  // '(age == password)',
  // '
  //   (age == password)
  // ',
  // '(
  //   age == password
  //  )',
  // 
  // 'age != password',
  // 'age > password',
  // 'age >= password',
  // 'age <= password',
  // 'age > password',
  // 
  // 'age:Bundle\\FormBundle\\Validator\\Integer and password:Bundle\\FormBundle\\Validator\\String',
  // 'age:Bundle\\FormBundle\\Validator\\Integer and() password:Bundle\\FormBundle\\Validator\\String()',
  // 'age:Bundle\\FormBundle\\Validator\\Integer and({}, {invalid: "This is invalid."}) password:Bundle\\FormBundle\\Validator\\String({required: true}, {min_length: Min length error message.})',
  // 'age:Bundle\\FormBundle\\Validator\\Integer and({required: true}, {invalid: "This is invalid."}) password:Bundle\\FormBundle\\Validator\\String',
  // 'age:Bundle\\FormBundle\\Validator\\Integer and({}) password:Bundle\\FormBundle\\Validator\\String({}, {})',
  // "age:Bundle\\FormBundle\\Validator\\Integer
  //  and
  //  password:Bundle\\FormBundle\\Validator\\String",
  //  '(age:Bundle\\FormBundle\\Validator\\Integer and password:Bundle\\FormBundle\\Validator\\String)',
  //  'age:Bundle\\FormBundle\\Validator\\Integer or password:Bundle\\FormBundle\\Validator\\String',
  //  '
  //   (age:Bundle\\FormBundle\\Validator\\Integer or password:Bundle\\FormBundle\\Validator\\String)
  //  ',
  //  '(age:Bundle\\FormBundle\\Validator\\Integer or password:Bundle\\FormBundle\\Validator\\String)',
  //  '(
  //    age:Bundle\\FormBundle\\Validator\\Integer or password:Bundle\\FormBundle\\Validator\\String
  //   )',
  // '
  //  (
  //   age:Bundle\\FormBundle\\Validator\\Integer
  //    or
  //   password:Bundle\\FormBundle\\Validator\\String
  //  )
  // ',
  // 
  // "
  //  (first_name:Bundle\\FormBundle\\Validator\\String or age:Bundle\\FormBundle\\Validator\\Integer)
  //   and
  //  age:Bundle\\FormBundle\\Validator\\Integer({min: 18}, {required: \"This is required.\"})
  //   or
  //  (
  //    age:Bundle\\FormBundle\\Validator\\Integer({max: 18})
  //     and
  //    is_young:Bundle\\FormBundle\\Validator\\Boolean({required: true})
  //  )
  // ",
  // 
  // 'email:Bundle\\FormBundle\\Validator\\Email and (age:Bundle\\FormBundle\\Validator\\Integer({min: 18}) or (age:Bundle\\FormBundle\\Validator\\Integer({max: 18}) and is_young:Boolean({required: true})))',
  // '(password == password_bis) and begin_date <= end_date and password:Bundle\\FormBundle\\Validator\\String({min_length: 4, max_length: 18})',
  // 'countries:Bundle\\FormBundle\\Validator\\Choice({choices: [France, USA, Italy, Spain]}) and password ==({}, {invalid: "Passwords must be the same (%left_field% != %right_field%)"}) password_bis and begin_date <= end_date and password:Bundle\\FormBundle\\Validator\\String({min_length: 4, max_length: 18})',
);

foreach ($tests as $test)
{
  $v = new FromDescription($test);

  $embedValidator = $v->getValidator();

  eval('$evaledValidator = '.$v->asPhp().';');

  $t->is($evaledValidator->asString(), $v->asString(), sprintf('FromDescription is able to parse "%s"', str_replace("\n", '\n', $test)));

  $v1 = new FromDescription($embedValidator->asString());
  $embedValidator1 = $v1->getValidator();

  $v2 = new FromDescription($embedValidator1->asString());

  $t->is($v1->asString(), $v2->asString(), sprintf('FromDescription is able to parse "%s"', str_replace("\n", '\n', $test)));
  

}
