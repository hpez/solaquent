#!/usr/bin/env bash

wget http://www.us.apache.org/dist/lucene/solr/6.0.1/solr-6.0.1.tgz
tar xzf solr-6.0.1.tgz solr-6.0.1/bin/install_solr_service.sh --strip-components=2
sudo ./install_solr_service.sh solr-6.0.1.tgz
sudo /opt/solr/bin/solr create -c colletion1 -n data_driven_schema_configs
