<?php
include("config.php");
include("classes/SiteResultProvider.php");
include("classes/ImageResultProvider.php");

if(isset($_GET["term"]))
{
	$term=$_GET["term"];
}
else
{
	exit("You must enter a search term");
}


if(isset($_GET["type"]))
{
	$type=$_GET["type"]; 
}	
else{
	$type = "sites";
}

$page = isset($_GET["page"]) ? $_GET["page"] : 1;


?>

<!DOCTYPE html>
<html> 
<head>
	<title>Google Search Engine</title>
</head>
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
<script src="assets/js/jquery.js" ></script>
<body>

	<div class="wrapper"> 

			<div class="header">

				<div class="headerContent">
					
					<div class="logoContainer">
						<a href="index.php">
					       <img src="assets/images/kiran.png">
					    </a>
					
		            </div>

			            <div class="searchContainer">

			            	<form action="search.php" method="GET">

			            		<div class="searchBarContainer">
			            			<input type="hidden" name="type" value="<?php echo $type;?>">
			            			<input  class="searchBox" type="text" name="term"  value="<?php echo $term; ?>" >
			            			<button class="searchButton">
			            				<img src="assets/images/search.png">
			            			</button>
			            			
			            		</div>
			            		
			            	</form>
			            	
			            </div>
				</div>


					<div class="tabsContainer">
						<ul class="tabList">
							<li class="<?php echo $type == 'sites' ? 'active':'' ?>">
								<a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
							</li>

							<li  class="<?php echo $type == 'images' ? 'active':'' ?>">
								<a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
							</li>

							
						</ul>

						
					</div>





				
			</div>

			<div class="mainResultSection">
				<?php 

				if($type=="sites")
				{
					$resultProvider = new SiteResultProvider($con);
					$pageLimit = 20;

				}

				else
				{
					$resultProvider = new ImageResultProvider($con);
					$pageLimit = 30;
				}

				
				$numResults =  $resultProvider->getNumResults($term);
				echo "<p class='resultsCount'>$numResults results found</p>";

				echo $resultProvider->getResultHtml($page,$pageLimit,$term);


				?>
				
			</div>


			<div class="paginationContainer">

				<div class="pageButtons">
				
					<div class="pageNumberContainer">
						<img src="assets/images/pagestart.png">
					</div>

					<?php

					$currentPage = 1;
					$pageLeft = 10;

					while($pageLeft!=0)
					{

						if($currentPage == $page) {

						echo "<div class='pageNumberContainer'>
							<img src='assets/images/pageselected.png'>
							<span class='pageNumber'>$currentPage</span>
						</div>";

					}

					else 
					{
						echo "<div class='pageNumberContainer'>
							<a href='search.php?term=$term&type=$type&page=$currentPage'>
								<img src='assets/images/a.png'>
								<span class='pageNumber'>$currentPage</span>
							</a>
							</div>";
					}


						


						

						$currentPage++;
						$pageLeft--;




					}

					?>






					<div class="pageNumberContainer">
						<img src="assets/images/pagestart.png">
					</div>

				


			</div>



			

    </div>

    <script type="text/javascript" src="assets/js/scriptd.js" ></script>

</body>
</html>