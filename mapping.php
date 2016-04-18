<?php


/*

Create mapping for geospatial data

To get current mapping http://192.168.99.100:32771/bib/_mapping?pretty=true

We edit this, and rebuild index, then reload data

This index is for BibJSON with my GeoJSON addition


*/

require_once (dirname(__FILE__) . '/elastic.php');

$json= '{
      "article" : {
        "properties" : {
          "_rev" : {
            "type" : "string"
          },
          "author" : {
            "properties" : {
              "firstname" : {
                "type" : "string"
              },
              "lastname" : {
                "type" : "string"
              },
              "name" : {
                "type" : "string"
              }
            }
          },
          "bhl_pages" : {
            "properties" : {
              "Page 169" : {
                "type" : "long"
              },
              "Page 170" : {
                "type" : "long"
              },
              "Page 171" : {
                "type" : "long"
              },
              "Page 172" : {
                "type" : "long"
              },
              "Page 173" : {
                "type" : "long"
              },
              "Page 174" : {
                "type" : "long"
              },
              "Page 175" : {
                "type" : "long"
              },
              "Page 176" : {
                "type" : "long"
              },
              "Page 177" : {
                "type" : "long"
              },
              "Page 178" : {
                "type" : "long"
              },
              "Page 179" : {
                "type" : "long"
              },
              "Page 180" : {
                "type" : "long"
              }
            }
          },
          "citation" : {
            "type" : "string"
          },
          "citation_string" : {
            "type" : "string"
          },
          "classification" : {
            "type" : "string"
          },
          "date" : {
            "type" : "long"
          },
          "geometry" : {
            "type": "geo_shape",
            "tree": "quadtree",
            "precision": "1m"
          },
          "identifier" : {
            "properties" : {
              "id" : {
                "type" : "string"
              },
              "type" : {
                "type" : "string"
              }
            }
          },
          "journal" : {
            "properties" : {
              "identifier" : {
                "properties" : {
                  "id" : {
                    "type" : "string"
                  },
                  "type" : {
                    "type" : "string"
                  }
                }
              },
              "issue" : {
                "type" : "string"
              },
              "name" : {
                "type" : "string"
              },
              "pages" : {
                "type" : "string"
              },
              "volume" : {
                "type" : "string"
              }
            }
          },
          "link" : {
            "properties" : {
              "anchor" : {
                "type" : "string"
              },
              "url" : {
                "type" : "string"
              }
            }
          },
          "names" : {
            "properties" : {
              "identifiers" : {
                "properties" : {
                  "namebankID" : {
                    "type" : "long"
                  }
                }
              },
              "namestring" : {
                "type" : "string"
              },
              "pages" : {
                "type" : "long"
              }
            }
          },
          "provenance" : {
            "properties" : {
              "mysql" : {
                "properties" : {
                  "id" : {
                    "type" : "string"
                  },
                  "modified" : {
                    "type" : "string"
                  }
                }
              }
            }
          },
          "publisher" : {
            "type" : "string"
          },
          "status" : {
            "type" : "long"
          },
          "thumbnail" : {
            "type" : "string"
          },
          "title" : {
            "type" : "string"
          },
          "type" : {
            "type" : "string"
          },
          "year" : {
            "type" : "string"
          }
        }
      }
}';


$obj = json_decode($json);

echo "Delete\n";
// Delete index 
$elastic->send('DELETE');

echo "Recreate\n";
// Recreate index 
$elastic->send('PUT');

echo "Mapping\n";
// Mapping
$elastic->send('PUT', '_mapping/article', json_encode($obj));

/*
 An example search

{
  "query": {
    "geo_shape": {
      "geometry": { 
        "shape": {
        "type":"polygon","coordinates":[[[119.44694,-3.52023],[119.44694,1.37323],[124.03366,1.37323],[124.03366,-3.52023],[119.44694,-3.52023]]]
        }
      }
    }
  }
}

*/

?>
