/*
	ELSWebAppKit HTML Document
	
	This class provides extensions to the DOM document to provide functionality similar to that of the corresponding PHP classes.
*/

// logging function
document.log = function(sMessage, bReverse) {
	var reverse = true;
	if (bReverse && bReverse != true)
		reverse = false;
	var console = document.getElementById('PageConsole');
	if (!console)
	{
		console = document.body.appendChild(document.createElement('div'));
		console.id = 'PageConsole';
	}
	var now = new Date();
	var div = document.createElement('div');
	div.appendChild(document.createTextNode(
		now.getHours() + ':'
			+ (now.getMinutes() < 10? '0': '') + now.getMinutes() + ':'
			+ (now.getSeconds() < 10? '0': '') + now.getSeconds() + ' - '
			+ sMessage));
	if (reverse)
		console.insertBefore(div, console.firstChild);
	else
		console.appendChild(div);
};