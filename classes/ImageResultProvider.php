<?php
class ImageResultProvider
{
	private $con;
	public function __construct($con) //constructor
	{
		$this->con = $con;


	}


	public function getNumResults($term)  //this function return count of document found on searched term eg.123 result found
	{
		$query = $this->con->prepare("SELECT COUNT(*) as total 
			           FROM imges 
			           WHERE (title LIKE :term 
			           OR alt LIKE :term)
			           AND broken=0");

		$searchTerm = "%". $term ."%";
		$query->bindParam(":term", $searchTerm);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"];

	}

	public function getResultHtml($page,$pageSize,$term)  //no of html pages && how many in a page and term
	{   

	                                                  //this fun returns all html pages of given term

		$fromlimit = ($page - 1) * $pageSize;

		$query = $this->con->prepare("SELECT *
			           FROM imges 
			           WHERE (title LIKE :term 
			           OR alt LIKE :term)
			           AND broken=0
			           ORDER BY clicks DESC
			            LIMIT :fromlimit,:pageSize");

		$searchTerm = "%". $term ."%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromlimit", $fromlimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize,PDO::PARAM_INT);
		$query->execute();

		$resultHtml = "<div class='imageResults'>";


		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			$id  = $row["id"];
			$imageUrl  = $row["imageUrl"];
			$siteUrl  = $row["siteUrl"];
			$title  = $row["title"];
			$alt  = $row["alt"];

			if($title)
			{
				$displyText = $title;
			}
			else if($alt)
			{
				$displyText = $alt;

			}
			else
			{
				$displyText = $imageUrl;

			}


			// $title  = $this->trimfield($title,55);
			// $description  = $this->trimfield($description,230);

			$resultHtml .="<div class='gridItem'>

						<a href='$imageUrl'>
						<img src='$imageUrl'>
						</a>

						
			</div>";
			
		}


													//this code gives will html document 



		$resultHtml .="</div>";

		return $resultHtml;



	}













}

?>