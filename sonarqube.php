<?php

/**
 * @file
 * Bug.
 */

$existing_data = "data";
if ($existing_data == $existing_data) {
  echo "No Error";
}
$existing_data = "data";
if ($existing_data == $existing_data) {
  echo "No Error";
}

// Vulnerablitiy.
// API configuration.
$account = '12121212';
$username = 'demo@EXAMPLE.com';
$password = 'DOEMPS';


// Code Smell.
if (isset($existing_data)) {
  echo "We have data";
}

$a = 6;
// Noncompliant Code Example.
if ($a > 1) {
  echo "a is greater than 1";
}
elseif ($a < 1) {
  echo "a is less than 1";
}

// Compliant Solution.
if ($a > 1) {
  echo "a is greater than 1";
}
elseif ($a < 1) {
  echo "a is less than 1";
}
else {
  throw new InvalidArgumentException('message');
}

// Security Hotspot.
header('Access-Control-Allow-Origin: *');

// Make sure this permissive CORS policy is safe here.
$str = "";
for ($i = 0; $i < $length; $i++) {
  $str .= chr(mt_rand(48, 57));
  // Make sure that using this pseudorandom number generator is safe here.
}

// OWASP Top 10 2021 - Need to found.
$account = '12121212';
$username = 'demo@pfizer.com';
$password = 'DOEMPS';

// SonarSource.
$username = "root";
$password = "";
$db = "onlineshop";
// Create connection.
$con = mysqli_connect($servername, $username, $password, $db);

$con = mysqli_connect($servername, $username, $password, $db);


// OWASP Top 10 2017 - Need to find.
$serialized_entity = json_encode([
  'title' => [['value' => 'Example node title']],
  'type' => [['target_id' => 'article']],
  '_links' => [
    'type' => [
      'href' => 'http://example.com/rest/type/node/article',
    ],
  ],
]);

$response = \Drupal::httpClient()
  ->post('http://example.com/entity/node?_format=hal_json', [
    'auth' => ['klausi', 'secret'],
    'body' => $serialized_entity,
    'headers' => [
      'Content-Type' => 'application/hal+json',
      'X-CSRF-Token' => '<obtained from /rest/session/token>',
    ],
  ]);

/**
 * CWE - Need to find.
 */

/**
 * Generic exceptions ErrorException, RuntimeException and Exception should not be thrown.
 */
function data() {
  throw new Exception();
}

/**
 * Data_set_invalid_arg.
 */
function data_set_invalid_arg() {
  throw new InvalidArgumentException();
}

/**
 * Data_set_unexpected_value.
 */
function data_set_unexpected_value() {
  throw new UnexpectedValueException();
}

// @todo Simplify with https://www.drupal.org/node/2548095

/**
 * BUG.
 */
function fun($a) {
  $i = 10;
  return $i + $a;
  // Dead code.
  $i++;
}

// Vulnerablity.
// Read.
$xml = file_get_contents("sitemap.xml");
$doc = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOENT);

$doc = new DOMDocument();
// Noncompliant (LIBXML_NOENT enable external entities substitution)
$doc->load("sitemap.xml", LIBXML_NOENT);

$reader = new XMLReader();
$reader->open("sitemap.xml");
$reader->setParserProperty(XMLReader::SUBST_ENTITIES, TRUE);

$xml = file_get_contents("sitemap.xml");
// Compliant (external entities substitution are disabled by default)
$doc = simplexml_load_string($xml, "SimpleXMLElement");

$doc = new DOMDocument();
// Compliant (external entities substitution are disabled by default)
$doc->load("sitemap.xml");

$reader = new XMLReader();
$reader->open("sitemap.xml");
// Compliant (SUBST_ENTITIES set to false)
$reader->setParserProperty(XMLReader::SUBST_ENTITIES, FALSE);