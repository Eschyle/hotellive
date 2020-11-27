<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use DateTime;

class DateTransformer implements DataTransformerInterface{

    /**
     * @param DateTime $date
     * @return string
     */
    public function transform($date)
    {
        if ($date === NULL){
            return '';
        }
        return $date->format('d-m-Y');
    }

    /**
     * @param string $dateString
     * @return DateTime
     */
    public function reverseTransform($dateString)
    {
        if ($dateString === NULL){
            throw new TransformationFailedException();
        }
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        if ($date === false) {
            throw new TransformationFailedException('Format de date incorrect');
        }
        return $date;
    }

}
