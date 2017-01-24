<?php



require_once (dirname(__FILE__) . '/elastic.php');

$tuple_size = 5;

$json= '{
  "settings": {
    "analysis": {
      "analyzer": {
        "ngram_tokenizer_analyzer": {
          "tokenizer": "ngram_tokenizer"
        }
      },
      "tokenizer": {
        "ngram_tokenizer": {
          "type": "ngram",
          "min_gram": ' . $tuple_size . ',
          "max_gram": ' . $tuple_size . ',
          "token_chars": [
            "letter",
            "digit"
          ]
        }
      }
    }
  },
   "mappings": {
      "doc": {
         "properties": {
            "seq": {
               "type": "text",
               "analyzer": "ngram_tokenizer_analyzer"
            }
         }
      },
	 "sequence": {
		"properties": {
			"geometry": {
			  "type": "geo_shape",
              "tree": "quadtree",
              "precision": "1m"			
			}
		}
	  }
   }
}';


$obj = json_decode($json);


echo "Delete\n";
// Delete index 
$elastic->send('DELETE');


echo "Create\n";
// Create index 
$elastic->send('PUT', '', json_encode($obj));

/*
$obj->analyzer = "my_analyzer";
$obj->text = "AACATTATACTTTATTTTCGGTATCTGATCAGGAATAATTGGATCATCATTAAGATTATTAATTCGAGCTGAATTAGGAAATCCAGG";

$elastic->send('POST', '_analyze', json_encode($obj));
*/


?>
