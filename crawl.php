<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();  //it contains all the visted url 
$crawling = array();  //this contains present url after going through it will put it into alreadycrawled var
$alreadyFoundImages = array();

function  linkExists($url)
{
	global $con;
	$query= $con->prepare("SELECT * FROM sites WHERE url = :url");

	$query->bindParam(":url",$url);
	$query->execute();
	

	return $query->rowCount() !=0;
}


function  insertLink($url,$title,$description,$keywords)
{
	global $con;
	$query= $con->prepare("INSERT INTO sites(url,title,description,keywords) 
		                    VALUES(:url,:title,:description,:keywords)");

	$query->bindParam(":url",$url);
	$query->bindParam(":title",$title);
	$query->bindParam(":description",$description);
	$query->bindParam(":keywords",$keywords);

	return $query->execute();
}


function  insertImage($url,$src,$alt,$title)
{
	global $con;
	$query= $con->prepare("INSERT INTO imges(siteUrl,imageUrl,alt,title) 
		                    VALUES(:siteUrl,:imageUrl,:alt,:title)");

	$query->bindParam(":siteUrl",$url);
	$query->bindParam(":imageUrl",$src);
	$query->bindParam(":alt",$alt);
	$query->bindParam(":title",$title);

     return $query->execute();
}






function createlink($src,$url) {   //this function is used display all links in that url

	$scheme = parse_url($url)["scheme"];  //htttp
	$host = parse_url($url)["host"];  //www.reecekenney.com/about.php

	if(substr($src, 0,2)=="//")   //this diffrent casees of src of url
	{
		$src = $scheme . ":" . $src;
	}
	else if(substr($src, 0,1)=="/") 
	{
		$src = $scheme . "://" . $host . $src;

	}

	else if(substr($src, 0,2)=="./") 
	{
		$src = $scheme . "://" . $host . dirname(parse_url($url["path"])) . substr($src, 1);
	}

	else if(substr($src, 0,3)=="../") 
	{
		$src = $scheme . "://" . $host . "/" . $src;
	}

	else if(substr($src, 0,5) !=="https" && substr($src, 0,4) !=="http")
	{
		$src = $scheme . "://" . $host . "/" . $src;

	}



	return $src;
	
}


function getDetails($url)  //this function gives the descrption and the link i.e when u search some thing in google it gives the link and description same as such
{

	global $alreadyFoundImages;

	$parser = new DomDocumentParser($url);

	$titleArray = $parser->getTitleTags();

	if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL)
	{
		return;
	}

	$title= $titleArray->item(0)->nodeValue;
	$title = str_replace("\n","",$title);

	if($title == "")
	{
		return;

	}


	$description = "";
	$keywords = "";

	$metaArray = $parser->getMetatags();

	foreach($metaArray as $meta)
	{
		if($meta->getAttribute("name") == "description")
		{
			$description = $meta->getAttribute("content");
		}

		if($meta->getAttribute("name") == "keywords")
		{
			$keywords = $meta->getAttribute("content");
		}
	
	}

	$description = str_replace("\n","",$description);
	$keywords = str_replace("\n","",$keywords);


    if(linkExists($url))
    {
    	echo "$url already exists<br>";
    }
    elseif(insertLink($url,$title,$description,$keywords))
    {
    	echo "SUCCESS: $url<br>";
    }
    else
    {
    	echo "ERROR: Failed to insert $url<br>";
    }

    $imageArray  = $parser->getImages();
    foreach($imageArray as $image)
    {
    	$src = $image->getAttribute("src");
    	$alt = $image->getAttribute("alt");
    	$title = $image->getAttribute("title");

    	if(!$title && !$alt)
    	{
    		continue;
    	}

    	$src = createlink($src,$url);

    	if(!in_array($src,$alreadyFoundImages))
    	{
    		$alreadyFoundImages[] = $src;

    		//insert the image

    		insertImage($url,$src,$alt,$title);
    	}


    }
	





	


	//echo "URL: $url, Title: $title , Desription: $description ,   Keywords: $keywords  <br>";



}


function followlink($url) 
{
	global $alreadycrawled;
	global $crawling;


	$parser = new DomDocumentParser($url);  //this the instance to the DomDocument parser class

	$linklist =  $parser->getLinks();  //this contains all the link of given url

	foreach($linklist as $link)   //$link loops every time for differnt html or a found on that url 
	{
		$href = $link->getAttribute("href");

		if(strpos($href, "#") !== false)   //removes all # in that url  strpos returns boolen value
		{
			continue;
		}

		else if(substr($href, 0,11)== "javascript:")
		{
			continue;
		}

		$href = createlink($href,$url);

		if(!in_array($href, $alreadycrawled)) 
		{
			$alreadycrawled[] = $href;
			$crawling[] = $href;


			getDetails($href);
		}
		

		
	}


	array_shift($crawling);

	foreach($crawling as $site)
	{
		followlink($site);
	}


}

$startUrl = "http://www.facebook.com";
followlink($startUrl);

?>
