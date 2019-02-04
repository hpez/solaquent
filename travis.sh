#!/usr/bin/env bash

wget http://mirror.23media.de/apache/lucene/solr/7.6.0/solr-7.6.0.tgz
tar xzf solr-7.6.0.tgz solr-7.6.0/bin/install_solr_service.sh --strip-components=2
sudo ./install_solr_service.sh solr-7.6.0.tgz
sudo /opt/solr/bin/solr create -c colletion1 -n data_driven_schema_configs
