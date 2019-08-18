<?php
class SiteResultProvider
{
	private $con;
	public function __construct($con) //constructor
	{
		$this->con = $con;


	}


	public function getNumResults($term)  //this function return count of document found on searched term eg.123 result found
	{
		$query = $this->con->prepare("SELECT COUNT(*) as total 
			           FROM sites WHERE title LIKE :term
			           OR url LIKE :term
			           OR keywords LIKE :term
			           OR description LIKE :term");

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
			           FROM sites WHERE title LIKE :term
			           OR url LIKE :term
			           OR keywords LIKE :term
			           OR description LIKE :term
			           ORDER BY clicks DESC
			            LIMIT :fromlimit,:pageSize");

		$searchTerm = "%". $term ."%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromlimit", $fromlimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize,PDO::PARAM_INT);
		$query->execute();

		$resultHtml = "<div class='siteResults'>";


		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			$id  = $row["id"];
			$url  = $row["url"];
			$title  = $row["title"];
			$description  = $row["description"];

			$title  = $this->trimfield($title,55);
			$description  = $this->trimfield($description,230);

			$resultHtml .="<div class='resultContiner'>

						<h3 class='title'>
						   <a class='result' href='$url' date-linkId='$id'>
						   $title
						   </a>

						</h3>

						<span class='url'>$url</span>
						<span class='description'>$description</span>

			</div>";
			
		}


													//this code gives will html document 



		$resultHtml .="</div>";

		return $resultHtml;



	}


	private function trimfield($string,$chracterlimit) {  //this function trims the description if too long

		$dots = strlen($string) > $chracterlimit ? "..":"";

		return substr($string,0,$chracterlimit) . $dots;

	}










}

?>