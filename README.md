Elastic Search notes

## Install

For these experiments I use Docker, so we grab the official container and launch it using Kinematic. This gives us Elastic running on http://192.168.99.100:32771 (this URL will vary).

## Add data

We can add data by doing a PUT. Note that we cannot add CouchDB documents as is because both CouchDB and Elastic use the _id field, so we need to delete that from the CocuhDB document. We also need to URL encode the document id (typically a DOI or URL stub).

## Search

https://www.elastic.co/guide/en/elasticsearch/reference/current/search.html

### Simple search

```
http://192.168.99.100:32771/bib/article/_search?q=Sulawesi
```

### Search using DSL

In this case we search for author by last name, which is part of a nested object, hence the “author.” prefix.

```
{
    “query” : {
        “match” : {
            “author.lastname” : “Hill”
        }
    }
}
```


## Geospatial

To get current mapping:

http://192.168.99.100:32771/bib/_mapping?pretty=true

We grab this file and edit it. The mapping describes mapping for every type of data in the index, but the PUT option for Elastic updates the mapping at the level of data type, so we need to extract the data type we need (e.g., article). So, if the JSON is:

```
{
  “bib” : {
    “mappings” : {
      “article” : {…
      }
    }
  }
}
```
we want just:

```
{
      “article” : {…
    }
}
```

Then we delete the index, recreate it, update the mapping, and reload data (see https://mapbutcher.gitbooks.io/using-spatial-data-in-elasticsearch/content/exercise_2_-_indexing_some_spatial_data/retrieve_the_index_mapping.html )

Example query to find documents that overlap the polygon for “Sulawesi Tengah” (data from https://github.com/lontongcorp/geodata/blob/master/IDN_province_bbox.geojson )

```
{
  “query”: {
    “geo_shape”: {
      “geometry”: { 
        “shape”: {
        “type”:”Polygon”,”coordinates”:[[[119.44694,-3.52023],[119.44694,1.37323],[124.03366,1.37323],[124.03366,-3.52023],[119.44694,-3.52023]]]
        }
      }
    }
  }
}
```

For more on how to search see https://www.elastic.co/guide/en/elasticsearch/guide/current/querying-geo-shapes.html

## Reading

https://www.gitbook.com/book/mapbutcher/using-spatial-data-in-elasticsearch/details