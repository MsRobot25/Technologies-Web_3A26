<?php
namespace App\Service\HappyQuote;

class HappyQuote
{
public function getHappyMessage(): string
{
$messages = [
'GO BRO GO ',
'YOU CAN DO THIS KYLIE GO KYLIE GO!!',
'SBEHHHHHHHKHIGHHHHHHHHH ! ',
'OPPAAAAAAAAAAA!',
];
$index = array_rand($messages);
return $messages[$index]; }
}
