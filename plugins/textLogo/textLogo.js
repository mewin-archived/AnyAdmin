function tl_out()
{
	var tl1 = document.getElementById("tl_1");
	var tl2 = document.getElementById("tl_2");

	tl1.className = "tl_1";
	tl2.className = "tl_2";
}

function tl_over()
{
	var tl1 = document.getElementById("tl_1");
	var tl2 = document.getElementById("tl_2");

	tl1.className = "tl_2";
	tl2.className = "tl_1";
}