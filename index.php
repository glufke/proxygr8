<?php

$de      = htmlspecialchars( $_GET["debug"] ); 
$url     = htmlspecialchars( !empty($_GET["url"]) ? $_GET["url"] : 'msx2.org' );
$pagesize= htmlspecialchars( !empty($_GET["pagesize"]) ? (int) $_GET["pagesize"] : 18 );
$page    = htmlspecialchars( !empty($_GET["page"]) ? (int) $_GET["page"] : 1 );

//------------------------------------------------------
// DEBUG info
//------------------------------------------------------
if ($de!='') {
  //Generate all types of errors in the screen.
  error_reporting(E_ALL);
  ini_set("display_errors", "1"); // shows all errors
  ini_set("log_errors", 1);

  $indicesServer = array('PHP_SELF',
  'argv',
  'argc',
  'GATEWAY_INTERFACE',
  'SERVER_ADDR',
  'SERVER_NAME',
  'SERVER_SOFTWARE',
  'SERVER_PROTOCOL',
  'REQUEST_METHOD',
  'REQUEST_TIME',
  'REQUEST_TIME_FLOAT',
  'QUERY_STRING',
  'DOCUMENT_ROOT',
  'HTTP_ACCEPT',
  'HTTP_ACCEPT_CHARSET',
  'HTTP_ACCEPT_ENCODING',
  'HTTP_ACCEPT_LANGUAGE',
  'HTTP_CONNECTION',
  'HTTP_HOST',
  'HTTP_REFERER',
  'HTTP_USER_AGENT',
  'HTTPS',
  'REMOTE_ADDR',
  'REMOTE_HOST',
  'REMOTE_PORT',
  'REMOTE_USER',
  'REDIRECT_REMOTE_USER',
  'SCRIPT_FILENAME',
  'SERVER_ADMIN',
  'SERVER_PORT',
  'SERVER_SIGNATURE',
  'PATH_TRANSLATED',
  'SCRIPT_NAME',
  'REQUEST_URI',
  'PHP_AUTH_DIGEST',
  'PHP_AUTH_USER',
  'PHP_AUTH_PW',
  'AUTH_TYPE',
  'PATH_INFO',
  'ORIG_PATH_INFO') ;

  echo '<table cellpadding="10">' ;
  foreach ($indicesServer as $arg) {
    if (isset($_SERVER[$arg])) {
        echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
    }
    else {
        echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
    }
  }
  echo '</table>' ;
}

//------------------------------------------------------
// Functions
//------------------------------------------------------
function deb( $dbg, $txt ) {
  if ($dbg !='') { echo '<pre>'.$txt.'</pre>';}
}


function correct_url($vurl, $vvalue ) {
  $vurl2=$vurl;
  if (substr($vurl2,-1)=='/' ) $vurl2 = substr($vurl2,0,-1);
  return $vurl2.'/'.$vvalue;
}



//------------------------------------------------------
// START
//------------------------------------------------------
echo "<html>";

//https://www.php.net/manual/en/reserved.variables.server.php
$serurl =  //"http://{$_SERVER['HTTP_HOST']}      
"{$_SERVER['PHP_SELF']}" ;

deb($de, $serurl );
deb($de, $url );
 
//  $url = rtrim($url);
$url= str_replace(' ','%20' ,$url);
deb($de, $url );  
$htmlin = file_get_contents('http://'. $url);

//echo '<a href="'.$url.'proxygr8.php?url=http://msx2.org/DVD_MSX/Jogos/Especiais%20para%20MSX%20Real/ROMS%20Normais/KUNGFU2.ROM">'."Teste 1".'</a><br>';
//echo '<a href="http://msx2.org/DVD_MSX/Jogos/Especiais%20para%20MSX%20Real/ROMS%20Normais/KUNGFU2.ROM">'."Teste 2".'</a><br>';

//https://regex101.com/
/*This REGEXP will produce an array like this:
Array
(
    [0] => Array
        (
            [0] => 4649421 <A HREF="/_filelist.txt">_filelist.txt</A>
            [1] => 4649421
            [2] => ="/_filelist.txt"
            [3] => _filelist.txt
        )

    [1] => Array
        (
            [0] =>  <A HREF="/_Pastas_em_Rar/">_Pastas_em_Rar</A>
            [1] => 
            [2] => ="/_Pastas_em_Rar/"
            [3] => _Pastas_em_Rar
        )

*/
$regex =  '/([0-9]*)? .?<a href="(.*?)">(.*?)<\/a>/mi';
preg_match_all($regex, $htmlin, $matches, PREG_SET_ORDER, 0);



if ($de!='') {
  echo '<pre>'; 
  print_r( $matches ) ;
  echo '</pre>';
  }
$qty_array =  count( $matches ) ;


$numpages = ceil($qty_array / $pagesize);
$offset = ($page - 1) * $pagesize;
if( $offset < 0 ) $offset = 0;

$yourDataArray = array_slice( $matches, $offset, $pagesize );

//print_r ( $yourDataArray );




//-------------------------------------------------------------
// PAGE on the top  / NEXT and PREV links
//-------------------------------------------------------------
echo 'Page ('.$page.'/'.$numpages.') ';

if ($page < $numpages ) echo '<a href="?page='.($page+1).'&url=' .$url. '">Next Page</a>';
echo " ";
if ($page > 1  ) echo '<a href="?page='.($page-1).'&url=' .$url. '">Prev Page</a>';

echo "<br>";

//------------------------------------------------------------
// UP DIR
//------------------------------------------------------------

//If last char is a / remove it!
if (substr( $url, strlen($url)-1, 1) == '/' ) {
  $url2 = substr( $url, 0, strlen($url)-1);
  }
else {
  $url2 = $url;
  }

//Remove last directory from URL
$url2 = substr( $url2, 0, strrpos($url2,'/',-1) );

if ($url2 != '' ) {
  echo '<a href="?url=' .correct_url($url2,''). '">UP DIR</a>';
  echo "<br>";
  }

//---------------------------------------------------------
// Print DIRs and Files
//---------------------------------------------------------

echo "<br>";
foreach ($yourDataArray as &$value) {


  if ($value[1]=='') { 
    //DIRECTORY:
    echo '[DIR] '; 

    echo '<a href="'.$serurl.'?url='. correct_url($url, $value[3]).'">';
    echo $value[3];
    echo '</a>';
  } 

  else {
   // IF IT'S A FILE:

   //TO DO:
   //If extension is ZIP, should UNZIP and save somewhere to be downloaded.
   //If extension is ROM, create 3 more links (for choosing the Gr8Net MODE)
   //If extension is MP3, let Gr8Net PLAY the song.
 
    echo '<a href="http://' . correct_url($url, $value[3]) .'">';
    echo $value[3];
    echo '</a>';
  }

  echo  '<br>';

}


?>
</html>
