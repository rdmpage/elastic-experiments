# Bitnami

Virtual machines (hosted by Google)

https://google.bitnami.com/vms

## Elastic search 2.2.0

https://google.bitnami.com/vms/bitnami-elasticsearch-dm-4693

## Papers

The Global Genome Biodiversity Network (GGBN) Data Standard specification https://doi.org/10.1093/database/baw125

## Design ideas

- For each data source (BOLD, GenBank, etc.) write converters to convert to Darwin Core Archive (DwCA)
- Store each DwCA in separate GitHub repository so can be edited, e.g. adding missing locality information.
- Optionally mint DOIs via Zenodo
- Import DwCA into Elastic Search as flat JSON document that includes DNA sequence

