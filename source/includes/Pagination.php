<?php


class Pagination {
	
  public $pageCourante;
  public $itemsParPage;
  public $nTotalItems;

  public function __construct($page=1, $parPage=ITEMSPARPAGE, $totalItems=0){
  	$this->pageCourante = (int)$page;
    $this->itemsParPage = (int)$parPage;
    $this->nTotalItems = (int)$totalItems;
    if($this->pageCourante>$this->nbTotalPages()) $this->pageCourante=$this->nbTotalPages();
  }

  public function getDecalage() {

    return ($this->pageCourante - 1) * $this->itemsParPage;
  }

  public function nbTotalPages() {
    return ceil($this->nTotalItems/$this->itemsParPage);
	}
	
  public function getNPagePrec() {
    return $this->pageCourante - 1;
  }
  
  public function getNPageSuiv() {
    return $this->pageCourante + 1;
  }

	public function pagePrecedenteExiste(): bool
    {
		return $this->getNPagePrec() >= 1;
	}

	public function pageSuivanteExiste(): bool
    {
		return $this->getNPageSuiv() <= $this->nbTotalPages();
	}


}
