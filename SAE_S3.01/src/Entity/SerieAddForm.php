<?php

namespace App\Entity;

class SerieAddForm
{


   private $title;

   public function getTitle(): ?string
   {
       return $this->title;
   }

    public function setTitle(string $title): self
    {
         $this->title = $title;
    
         return $this;
    }

}