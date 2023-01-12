<?php

namespace Tests\Unit\Helper\File;

use App\Helper\File\Exception\InvalidImageException;
use App\Helper\File\ImageHelper;
use Tests\TestCase;

class ImageHelperTest extends TestCase{


    public function testSaveImagePng(){

        $file = 'my_file.png';

        $image = ImageHelper::saveImage(ImageRepo::IMAGE_PNG, $file);
        $this->assertIsString($image);
        $this->assertTrue(ImageHelper::existeImage($file));

        ImageHelper::deleteImage($file);

        $this->assertFalse(ImageHelper::existeImage($file));
    }        


    public function testSaveImagePngRandomName(){

       

        $image = ImageHelper::saveImage(ImageRepo::IMAGE_PNG);
        $this->assertIsString($image);
  
        $this->assertTrue(ImageHelper::existeImage($image));

        ImageHelper::deleteImage($image);

        $this->assertFalse(ImageHelper::existeImage($image));

    }   

    public function testSaveImageJpg(){
       
        $image = ImageHelper::saveImage(ImageRepo::IMAGE_JPG);

        $this->assertIsString($image);

        $this->assertTrue(ImageHelper::existeImage($image));

        ImageHelper::deleteImage($image);

        $this->assertFalse(ImageHelper::existeImage($image));

    }   


    public function testSaveImageJpgRandomName(){
       
        $image = ImageHelper::saveImage(ImageRepo::IMAGE_JPG);

        $this->assertIsString($image);
  
        $this->assertTrue(ImageHelper::existeImage($image));

        ImageHelper::deleteImage($image);

        $this->assertFalse(ImageHelper::existeImage($image));

    } 

    public function testImageWith_prefix(){
        $image = ImageHelper::saveImage(ImageRepo::IMAGE_JPG, null, 'prefixtest_');

        $this->assertIsString($image);

        $this->assertStringContainsString('prefixtest_', $image);

        $this->assertTrue(ImageHelper::existeImage($image));

        ImageHelper::deleteImage($image);

        $this->assertFalse(ImageHelper::existeImage($image));
    }    


    public function testImageInvalidFileException(){
        $this->expectException(InvalidImageException::class);
        $imageFailed = "aaaa";
        $image = ImageHelper::saveImage($imageFailed);

    }   


    public function testImageEmptyFileException(){
        $this->expectException(InvalidImageException::class);
        $imageFailed = "";
        $image = ImageHelper::saveImage($imageFailed);
    }   

    public function testImageBigSizeFileException(){

        $this->expectException(InvalidImageException::class);
        $imageFailed = ImageRepo::IMAGE_JPG . ImageRepo::IMAGE_JPG;
        $image = ImageHelper::saveImage($imageFailed);

    }   
}