/*
* JS HELPER FUNCTIONS
*
*/

/*
* Get Url Parameters in jQuery
* http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
*
* Usage: var blog = GetURLParameter('blog');
*
*/

function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}  