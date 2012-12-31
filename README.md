# Solr HTTP API Wrapper

## Why

I wanted a wrapper to query a Solr backend with the parameters its API
provides. A wrapper that

* is straight-forward, if I know Solr I know the wrapper
* can facilitate higher level functionalities
* has minimal code

# Usage

    use SimpleSolrAPI\SolrAPI;
    
    $api = new SolrAPI("http://SOLR_SERVICE_BASE_URL");
    $api->q('Carlin');  // maps to parameter 'q'
    $api->fq('occupation:comedian'); // fq
    $api->fl('name, age, awards'); // fl
    $api->hl(true); // hl
    $api->hl_fl('*'); // hl.fl
    
    Simple filter queries:
    
        $api->fq("field:value");
        
    Complex filter queries:
    
        use SimpleSolrAPI\FilterQuery
    
        $fq = new FilterQuery;
        $fq->add('field', 'value');
        $fq->popen();
        $fq->add('field', 'value');
        $fq->or('field', 'value');
        $fq->pclose();
        
        $api->fq($fq->getFilterQuery());
        
    



