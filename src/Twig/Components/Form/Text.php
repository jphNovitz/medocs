<?php

namespace App\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class Text
{

    public mixed $field;
    public string $label ;
    public string $placeholder = "";
}