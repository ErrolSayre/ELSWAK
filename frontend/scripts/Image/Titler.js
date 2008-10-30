/*
	ELSWebAppKit Image Titler - this script runs at page load, scans for all the images on the page, and sets the title to the alt attribute if it is not set. Since DOM modifications don't affect the validity of a document, the ALT tag is required for most DTDs, and the browsers most likely to be using alt tags (instead of images) will probably not support javascript, it is unlikely that setting the alt to the title will matter.
*/
if (!document.ELSWebAppKit)
	document.ELSWebAppKit = {};
if (!document.ELSWebAppKit.Image)
	document.ELSWebAppKit.Image = {};
document.ELSWebAppKit.Image.Titler = function()
{
	// grab all the images in the document
	var images = document.getElementsByTagName('img');
	
	// for each image, see if either the title or alt is not set
	for (var i in images)
		// determine if the image has a title
		if (images[i].title == '')
			// determine if the title can be set to the alt
			if (images[i].alt != '')
				images[i].title = images[i].alt;
}
try
{
	if (window.attachEvent)
		window.attachEvent('onload', document.ELSWebAppKit.Image.Titler);
	else if (window.addEventListener)
		window.addEventListener('load', document.ELSWebAppKit.Image.Titler);
}
catch(e)
{
	if (document.addEventListener)
		document.addEventListener('DOMContentLoaded', document.ELSWebAppKit.Image.Titler, false);
}
