<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class FileTransformer implements DataTransformerInterface{

    /**
     * @param string $filePath
     * @return File
     */
    public function transform($filePath)
    {
        if ($filePath == NULL or empty($filePath)){
            return '';
        }
//        return $date->format('Y-m-d');
        $uploadsDir = __DIR__.'\..\..\..\public\\';
        dump($uploadsDir);
        $file = new File($uploadsDir.$filePath);
        return $file;
//        return $file->buildForm(['path'=>$filePath]);
    }

    /**
     * @param File $file
     * @return string
     */
    public function reverseTransform($file)
    {
        if ($file === NULL){
            return '';
        }
//        $date = DateTime::createFromFormat('Y-m-d', $dateString);
//        $file = DateTime::createFromFormat('d/m/Y', $file);
//        if ($file === false) {
//            throw new TransformationFailedException();
//        }
        dump($file);
        $filePath = $file->getPath();
        return $file;
    }
}
