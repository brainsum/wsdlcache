# Mabiasz WSDL Cache Documentation

MAjOR TODO:
ErrorException in HelperFunctions.php line 111:
simplexml_load_file(): /var/www/wsdlcache.mabiasz.hu/container/wsdlStatus.xml:1: parser error : Document is empty
TryCatch it, if empty then fill

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
     
### Version Control
* The application was created using git
* Remote host for the repository is here: https://github.com/brainsum/wsdlcache     
     
### Planning     
Catalog
* We need to store WSDL data in a file
* Create a map file like an xml
* checks should run from cron: https://laravel.com/docs/master/scheduling
     
### First test trial
* plain php soap service
* request to lumen client
* lumen proxy
     
### Others
* SoapUI    
    
### Used dependencies
* laravel-soap: https://github.com/artisaninweb/laravel-soap
* Nathanmac parser: https://github.com/nathanmac/Parser

### Cron job
* crontab -e
* Add this to crontab: " * * * * * php /path/to/project-folder/artisan schedule:run >> /dev/null 2>&1 "
* Note: this runs every minute, use the schedule to config each command separately.
* Note2: we could also refine this, but task-based intervals are ok
