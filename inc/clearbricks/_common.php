<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

require dirname(__FILE__).'/common/_main.php';

# Database Abstraction Layer
$__autoload['dbLayer']		= dirname(__FILE__).'/dblayer/dblayer.php';
$__autoload['dbStruct']		= dirname(__FILE__).'/dbschema/class.dbstruct.php';
$__autoload['dbSchema']		= dirname(__FILE__).'/dbschema/class.dbschema.php';

# Files Manager
$__autoload['filemanager']	= dirname(__FILE__).'/filemanager/class.filemanager.php';
$__autoload['fileItem']		= dirname(__FILE__).'/filemanager/class.filemanager.php';

# Feed Reader
$__autoload['feedParser']	= dirname(__FILE__).'/net.http.feed/class.feed.parser.php';
$__autoload['feedReader']	= dirname(__FILE__).'/net.http.feed/class.feed.reader.php';

# HTML Filter
$__autoload['htmlFilter']	= dirname(__FILE__).'/html.filter/class.html.filter.php';

# HTML Validator
$__autoload['htmlValidator']	= dirname(__FILE__).'/html.validator/class.html.validator.php';

# Image Manipulation Tools
$__autoload['imageMeta']		= dirname(__FILE__).'/image/class.image.meta.php';
$__autoload['imageTools']	= dirname(__FILE__).'/image/class.image.tools.php';

# Send Mail Utilities
$__autoload['mail']			= dirname(__FILE__).'/mail/class.mail.php';

# Send Mail Through Sockets
$__autoload['socketMail']	= dirname(__FILE__).'/mail/class.socket.mail.php';

# HTML Pager
$__autoload['pager']		= dirname(__FILE__).'/pager/class.pager.php';

# REST Server
$__autoload['restServer']	= dirname(__FILE__).'/rest/class.rest.php';
$__autoload['xmlTag']		= dirname(__FILE__).'/rest/class.rest.php';

# Database PHP Session
$__autoload['sessionDB']		= dirname(__FILE__).'/session.db/class.session.db.php';

# Simple Template Systeme
$__autoload['template']		= dirname(__FILE__).'/template/class.template.php';

# URL Handler
$__autoload['urlHandler']	= dirname(__FILE__).'/url.handler/class.url.handler.php';

# Wiki to XHTML Converter
$__autoload['wiki2xhtml']	= dirname(__FILE__).'/text.wiki2xhtml/class.wiki2xhtml.php';

# Common Socket Class
$__autoload['netSocket']		= dirname(__FILE__).'/net/class.net.socket.php';

# HTTP Client
$__autoload['netHttp']		= dirname(__FILE__).'/net.http/class.net.http.php';
$__autoload['HttpClient']	= dirname(__FILE__).'/net.http/class.net.http.php';

# Zip tools
$__autoload['fileUnzip']		= dirname(__FILE__).'/zip/class.unzip.php';
$__autoload['fileZip']		= dirname(__FILE__).'/zip/class.zip.php';

$__autoload['xmlrpcValue']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcMessage']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcRequest']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcDate']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcBase64']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcClient']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcClientMulticall']		= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcServer']				= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
$__autoload['xmlrpcIntrospectionServer']	= dirname(__FILE__).'/net.xmlrpc/class.net.xmlrpc.php';
?>