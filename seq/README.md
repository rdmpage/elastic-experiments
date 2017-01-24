# Elastic search as DNA sequences



## Fast sequence search (alternative to BLAST)

Index COI barcodes using 5-grams:

```javascript
{
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
          "min_gram": 5,
          "max_gram": 5,
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
      }
   }
}
```

To search database for a sequence and return up to 100 hits:

POST http://192.168.99.100:32769/dna/_search

```javascript
{
    "size":"100",
    "query": {
        "match": {
           "seq": {
"analyzer":"ngram_tokenizer_analyzer","query":"ACCTAATTTTTGGTGCTTGAGCAGGTATAGTAGGAACAGCCCTAAGTATTCTAATCCGTACAGAATTAGGGCAACCAGGTGCCCTACTAGGAGATGATCAAATCTACAATGTAATTGTTACCGCCCATGCTTTTGTCATAATTTTCTTTATGGTAATACCTATAATAATTGGAGGTTTTGGAAACTGACTTGTTCCACTTATAATTGGAGCACCAGACATAGCTTTCCCACGAATAAATAACATAAGCTTCTGACTTTTACCTCCTTCATTTCTCCTTCTTCTAGCATCATCAATAGTAGAAGCCGGAGCAGGAACAGGCTGAACAGTATATCCACCCCTAGCTGGAAACTTAGCCCATGCAGGAGCATCAGTAGACCTGACAATTTTTTCCTTACACTTAGCAGGTGTTTCATCAATCTTAGGTGCTATTAACTTTATCACAACTATCATCAATATAAAACCACCTGCTATAACACAATATCAAACACCCCTATTTGTATGATCTGTACTAATCACCGCAGTCCTTCTTTTACTTTCACTTCCTGTTTTAGCAGCAGGTATTACAATACTTTTAACCGACCGCAATCTTAACACAACCTTCTTTGATCCTGCTGGAGGAGGGGATCCAATCCTTTATCAACATCTA"
}
        }
    }
}
```


## Alignment-free phylogeny

Build trees without aligning sequences using k-tuple distances. Use 5-tuple (1024 permutations for DNA sequences).

Yang, K., & Zhang, L. (2008, January 10). Performance comparison between k-tuple distance and four model-based distances in phylogenetic tree reconstruction. Nucleic Acids Research. Oxford University Press (OUP). https://doi.org/10.1093/nar/gkn075 https://www.ncbi.nlm.nih.gov/pmc/articles/PMC2275138

Take search result, extract sequences, build distance matrix based on 5-tuples, export in NEXUS format for PAUP.

S(X,Y) = SUM_i=1^4k |X_i - Y_i|^2 

where Xi and Yi correspond to the tuple i's frequencies (=counts/n−k + 1) in sequences X and Y, respectively; n is the sequence length of either sequence X or Y; k is the tuple length


## Geographic search?

Add geographic index so we can have geographic searches.

```javascript
{
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
}
```

Can also include taxonomic information in a spatial query (in this case “Costa Rica” and “Reptilia” (country bounding box from https://raw.githubusercontent.com/lontongcorp/geodata/master/country_bbox.geojson ):

```javascript
{
	"size": "100",
	"query": {
		"bool": {
			"must": [{
				"geo_shape": {
					"geometry": {
						"shape": {
							"type": "Polygon",
							"coordinates": [
								[
									[-85.898621, 8.026709],
									[-85.898621, 11.21361],
									[-82.565193, 11.21361],
									[-82.565193, 8.026709],
									[-85.898621, 8.026709]
								]
							]
						}
					}
				}
			}, {
				"match": {
					"class": "Reptilia"
				}
			}]
		}
	}
}
```





## Taxonomic search using facets?

Can add taxonomic groups to queries (see above).

## Phylogeny

Can build trees in browser using NJ Javascript code from https://github.com/biosustain/neighbor-joining (I’ve hacked this to remove dependency on timsort, need to text. Code also doesn’t midpoint root NJ trees.

## Examples

### COI barcodes not in BOLD

https://www.ncbi.nlm.nih.gov/nuccore/FJ790672 Tatera indica (georeferenced)

https://www.ncbi.nlm.nih.gov/nuccore/GU938873 Akodon montensis DNA barcoding of sigmodontine rodents: identifying wildlife reservoirs of zoonoses (not georeferenced) **BUT** other sequences from this species and study are in BOLD, e.g. https://www.ncbi.nlm.nih.gov/nuccore/GU938924 but not linked to BOLD (see http://bins.boldsystems.org/index.php/Public_RecordView?processid=GBMA5154-13 )

### Not in iBOL public dump but in BOLD website 

 


