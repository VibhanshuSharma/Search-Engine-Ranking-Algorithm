<html>
  <head>
    <meta charset="utf-8">
    <title>PHP Solr Client Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
	
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="query" name="q" type="text" placeholder="Enter a Query" value="<?php echo isset($_GET["q"])?$_GET["q"]:""?>"/><br>
      <input type="radio" id="solr" name="result" value="solr" checked <?php echo isset($_GET["result"]) && $_GET["result"] == "solr"?"checked":"";?>/>Solr
      <input type="radio" id="PageRank" name="result" value="PageRank" <?php echo isset($_GET["result"]) && $_GET["result"] == "PageRank"?"checked":""; ?>/>PageRank
    
	<input type="submit"/><br>
</form>

<?php

// make sure browsers see this page as utf-8 encoded HTML
//header('Content-Type: text/html; charset=utf-8');

include 'SpellCorrector.php';  


$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$result = false;
$result=$_REQUEST['result'];


$correctString = "";
$flag = 0;
            function spellCorrection($q)
            {
                $queryArray = explode(" ",$q);
                $size = count($queryArray);
                global $correctString, $flag;
                for ($i = 0; $i < $size ; $i++) 
                {
                    $values = SpellCorrector::correct($queryArray[$i]);
                    $lowercaseString = strtolower($queryArray[$i]);
                    if($values != $lowercaseString) 
                    {
                       $correctString .= $values . " ";
                       $flag = 1;
                    }
                    else
                    {
                        $correctString .= $queryArray[$i] . " "; 
                    }
                }
            }

if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('Apache/Solr/Service.php');
  ini_set('memory_limit', '2048M');
  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }
  
  spellCorrection($query);

  ?>
  
  <p><b><span style="color:red"> Did you mean :</span> <i><a href ="solr.php?q=<?php print $correctString ?>&result=solr"> <?php echo $correctString; ?> </a></i></b></p>
  
  <?php
	
        if($flag == 1)
                  {
                     
                   $newQueryArray = explode(" ",$correctString);
                   $size = count($newQueryArray);
                   $newQuery = "";
                   for ($i = 0; $i < $size-1 ; $i++) 
                    {
                          if($i == $size-2)
                            {
                                $newQuery .= $newQueryArray[$i]; 
                                break;
                            }
                            $newQuery .= $newQueryArray[$i] . "+"; 
                        }
        ?>
                   
  <p>Showing results for : <?php echo $correctString;?></p>
        <?php
                  }
  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    if($result=="PageRank")
	{
		$pageRankStart=0;
		$pageRankRows=10;
		$additionalParameters =array('sort'=>"pageRankFile desc");
		$results=$solr->search($correctString,$pageRankStart,$pageRankRows,$additionalParameters);
	}
  else if($result=="solr")
	{
		$results = $solr->search($query, 0, $limit, $params);
	}
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>

<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  $console.log("reached");foreach ($results->response->docs as $doc)
  {
?>
      <li>
       
<?php
    // iterate document fields / values
   
   $id = $doc->id;
   $title = $doc->title;
   $descipt = $doc->description;
   $url = $doc->og_url;
   
   $var = file_get_contents($id);
   $doc = new DOMDocument();
   $snippet = "null";
   $search = $query;
   while(($term = $doc->getElementsByTagName("script")) && $term->length){ 
   	$term->item(0)->parentNode->removeChild($term->item(0));   
   }
   libxml_use_internal_errors(true);
   $doc->loadHTML($var);
   $content = array();
    foreach($doc->getElementsByTagName('body') as $head){
 	foreach($head->childNodes as $cell){
 		$content[] = $cell->nodeValue; 
 }
}

   $regex_html = '/[^\\>"\/#]{70,100}('.$query.')[^\\>"\/<#]{70,156}/i';
   for($i=0; $i<sizeof($content); $i++){
	if(preg_match($regex_html, $content[$i], $html) == 1 ){
	
		
		$snippet = html_entity_decode($html[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}
	else{
		if(strpos($search, ' ') >=0){
			$parts = preg_split("/[\s]+/", $search);
			foreach($parts as $str){
				$search = $str;


				$regex_html = '/[^\\>"\/#]{70,100}('.$query.')[^\\>"\/<#]{70,156}/i';
				if(preg_match($regex_html, $content[$i], $html)==1){
					$snippet = html_entity_decode($html[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
					break;
				}
			}
		}	
	}
	if($snippet != "null"){
		break;
	}
    }
  
?>
	<p><?php echo $id; ?></p>
	<p><a target="_blank" href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
        <p><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
	<p><?php echo $descipt; ?></p>
	<p><b>Snippet: </b><?php echo $snippet; ?></p><br>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
       <script type="text/javascript">
            $(document).ready(function() {
		
                 $("#query").autocomplete({

                source: function( request, response ) {
	                   
			 $.ajax({
			    type:"GET", 
                            url: "http://localhost:8983/solr/myexample/suggest",
			    dataType:"json",
                            data: {q : $('#query').val()},
			success: function(data) {
                                response(data);
				
	
                            }
                    });
                }
            });
        });
        </script>

  </body>
</html>
