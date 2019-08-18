<?php

class DomDocumentParser {

	private $doc;  //doc contains all html of that page it is in function below after exe memory get lost so we defined here outside the class

	public function __construct($url) {  //this all means go that url and passing the content of that website into DomDocument obj and $doc var contain whole html of that webpage

		$option = array('http'=>array('method'=>"GET",'header'=>"User-Agent:kiranweb/0.1\n")
	    );

	    $context = stream_context_create($option); //this is built in function to request a webpage

	    $this->doc = new DomDocument();  //built in class which allows to perform action on webpages
	    @$this->doc->loadHTML(file_get_contents($url,false,$context));  //@ means dont show any error and warning
	}

	public function getLinks()
	{
		return $this->doc->getElementsByTagName("a");  //this function returns all link found we dom object i.e tag anchor tag a which contain link
	}

	public function getTitletags()
	{
		return $this->doc->getElementsByTagName("title");  //this function returns all link found we dom object i.e title tag a which contain link
	}

	public function getMetaTags()
	{
		return $this->doc->getElementsByTagName("meta");  //this function returns all metadata found we dom object i.e meradata a which contain link
	}

	public function getImages()
	{
		return $this->doc->getElementsByTagName("img");  //this function returns all images found we dom object i.e meradata a which contain link
	}
}


?>