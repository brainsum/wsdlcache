# Mabiasz WSDL Cache Documentation

## List of Todos, Planning, Specs
### Specs
* JIRA task: https://brainsum.atlassian.net/browse/MABIASZ-452
* Create this with as minimal code as possible, use a microframework like Lumen
* No DB is needed
* Functions:
  * Save WSDL files
  * Parse these as necessary (e.g: create a single files by actually including what needs to be included)
  * Periodically check for WSDL availability
  * When WSDL is not available, send status as an email
  * List page of tracked WSDLs
     * Name, Status (with green/red indicators), Last check date, Last modification date
  * When WSDL is modified (== is different from the cache), send an email about it with the diff
  * Logging for each WSDL
     * Go down to this level: HTTP request, SSL handshake, etc.
     
### Planning     
Catalog
* We need to store WSDL data in a file
* Create a map file like an xml
     
    