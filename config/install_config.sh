#!/bin/bash

# Autor: Tino Schuldt
# Date: 30.11.2017


# External IP-Adress
var_webserver_ip="192.168.174.160"				# External IP-ADDRESS (do not use 127.0.0.1)

# Project paths
path_cgi_bin="/usr/lib/cgi-bin"
path_apache_doc_root="/var/www"
path_apache_html="/var/www/html"
path_postgis="/usr/share/postgresql/9.4/contrib/postgis-2.1"
path_sitesavailable="/etc/apache2/sites-available/"

# Database connections
var_postgres_password="postgres"			# Password for default Postgres User
var_db_host="localhost"
var_db_port="5432"
var_db_name="efdb"
var_db_user="efuser"
var_db_password="efpassword"

# Database for Web-Mapping-App (Mapbender3)
var_dbm_name="mapbender3"

# Authentication for WFS-T Service (TinyOWS)
var_tinyows_path="wfs-t"				# For http://127.0.0.1/cgi-bin/wfs-t/tinyows
var_tinyows_name="tinyuser"
var_tinyows_password="tinypassword"

# Roles for Mapbender3 login
var_roles_admin_name="root"
var_roles_admin_password="root"
var_roles_admin_email="root@example.com"

# Variablen
var_url_mapbender="mapbender3"				# For http://127.0.0.1/mapbender3
var_sitesavailable_mapbender="mapbender3.conf"
var_sitesavailable_tinyows="auth-tinyows.conf"
var_enable_fcgi=true					# Enable FastCGI for Server speed-up?

# Mapbender Application
var_application_name="extension_fractures"
var_application_db_schema="geodata" # for no schema use "public"
var_application_description="This application shows the temporal representation of the extension fractures"
var_application_srid=4326
var_application_other_srs=("EPSG:4326" "EPSG:25832" "EPSG:25833" "EPSG:31466" "EPSG:31467" "EPSG:31468" "EPSG:3857" "EPSG:900913")
var_application_import_password="geodata-upload"

# WMS/WFS -> OWS (OpenGIS Web Services)
var_ows_title="Extension fractures"
var_ows_mapfile_name="ef_all.map"

var_ows_title_year="Yearly distance"
var_ows_mapfile_name_year="ef_year.map"

var_ows_title_max="Maximal distance"
var_ows_mapfile_name_max="ef_max.map"

var_ows_mapfile_path="mapserver"
var_ows_html_featureinfo_path="mapserver"
# WMS Metadata
var_ows_mapfile_metadata_title="Extension fractures"
var_ows_mapfile_metadata_title_year="Yearly distance"
var_ows_mapfile_metadata_title_max="Maximal distance"
var_ows_mapfile_metadata_abstract="WMS-Data from the Postgis-Database with Mapserver"
var_ows_mapfile_metadata_keywordlist="Mapserver WMS"
var_ows_mapfile_metadata_fees="none"
var_ows_mapfile_metadata_accessconstraints="none"
var_ows_mapfile_metadata_contactperson="Herr/Frau Muster"
var_ows_mapfile_metadata_contactorganization="..."
var_ows_mapfile_metadata_contactposition="Supporter"
var_ows_mapfile_metadata_contactvoicetelephone="0395-..."
var_ows_mapfile_metadata_contactfacsimiletelephone="0395-..."
var_ows_mapfile_metadata_contactelectronicmailaddress="lg11127@hs-nb.de"
var_ows_mapfile_metadata_address="HS"
var_ows_mapfile_metadata_addresstype="postal"
var_ows_mapfile_metadata_city="Neubrandenburg"
var_ows_mapfile_metadata_stateorprovince="Mecklenburg-Vorpommern"
var_ows_mapfile_metadata_postcode="17033"
var_ows_mapfile_metadata_county="Germany"


# PACKAGES: Names for the install (apt-get install)
# Basic packages
pkg_apache="apache2"
pkg_php="php"
pkg_fcgid="libapache2-mod-fcgid"
pkg_fcgid_dev="libfcgi-dev"

# Database packages (PostgreSQL + PostGIS)
pkg_postgresql="postgresql"
pkg_postgresql_contrib="postgresql-contrib"
pkg_postgresql_postgis="postgis"

# MapServer packages
pkg_cgi_mapserver="cgi-mapserver"
pkg_mapserver_bin="mapserver-bin"
pkg_mapserver_doc="mapserver-doc"

# WFS-T packages (TinyOWS)
pkg_tinyows="tinyows-v.1.1.1"
pkg_gcc="gcc"
pkg_libxml_dev="libxml2-dev"
pkg_postgresql_server_dev="postgresql-server-dev-all"
pkg_flex="flex"
pkg_make="make"
pkg_autoconf="autoconf"

# Web-Mapping-App (Mapbender3)
pkg_mapbender="mapbender3-starter-3.0.6.3"
pkg_php_gd="php-gd"
pkg_php_curl="php-curl"
pkg_php_cli="php-cli"
pkg_php_xml="php-xml"
pkg_php_sqlite="php-sqlite3"
pkg_sqlite="sqlite3"
pkg_php_intl="php-intl"
pkg_openssl="openssl"
pkg_php_zip="php-zip"
pkg_php_mbstring="php-mbstring"
pkg_php_bz2="php-bz2"
pkg_php_pgsql="php-pgsql"

