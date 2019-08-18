$(document).ready(function() {

	$(".result").on("click" , function() {

		var id = $(this).attr("date-linkId");
		var url = $(this).attr("href");

		if(!id)
		{
			alert("date-linkId atribute not found");
		}

		increaseLinkClicks(id,url)


		
		return false;
		


	});

});

function increaseLinkClicks(linkId,url) {

	$.post("ajax/updateLinkCount.php",{linkId:linkId})
	.done(function(result)
		{
			if(result != "")
			{
				alert(result);
				return;
			}

			window.location.href=url;
		});

}