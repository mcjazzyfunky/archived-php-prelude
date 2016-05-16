<?php
/**
 * Muse PHP library
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file 'licenses/license-muse.txt'.
 *
 * @package    muse.base
 * @copyright  (c) 2008-2009 dmc digital media center GmbH and Ralf Obmann
 * @license    New BSD License
 * @author     Ralf Obmann
 * @version    $Id: muse.inc.php 36180 2012-02-12 10:40:03Z rob $
 */

/**
 * Common Muse include file that makes all Muse classes been loaded
 * automatically.
 *
 * Please make sure that really ALL public Muse classes are registered below.
 * Otherwise auto-loading will not work properly.
 */

// --- namespace ----------------------------------------------------
#namespace Muse::Base;
#use Muse::Base::_MuseAutoClassLoader;

// --- includes -----------------------------------------------------
require_once(dirname(__FILE__)
    . '/main/base/internal/_MuseAutoClassLoader.class.php');

// --- main ---------------------------------------------------------
// Register all Muse classes explicitly for auto-loading.
_MuseAutoClassLoader::register(
    // Associative array that maps all Muse class names to their subfolders
    // (relative to folder 'muse/main').
    // This information is used to include necessary PHP class files
    // when auto-loading Muse classes (file names are by convention
    // '{className}.class.php').
    array(
      //--- production packages --------
      // package muse.base
      'ApplicationException' => 'base',

      // package muse.util
      'Config' => 'util',
      'ConfigException' => 'util',
      'DynValueObject' => 'util',
      'Seq' => 'util',
      'SeqGenerator' => 'util',
      'Utils' => 'util',

      // package muse.io
      'IOException' => 'io',
      'Sys' => 'io',

      // package muse.dbd
      'Database' => 'db',
      'DatabaseCursor' => 'db',
      'DatabaseException' => 'db',
      'SqlQueryBundle' => 'db',

      // package muse.tools
      'Exporter' => 'tools',
      'ExporterDatasource' => 'tools',
      'ExporterAuxiliaryData' => 'tools',
      
      // package muse.slang
      'AbstractSlangNode' => '../experimental/slang',
      'SlangApplicationNode' => '../experimental/slang',
      'SlangListNode' => '../experimental/slang',
      'SlangMapNode' => '../experimental/slang',
      'SlangNumberNode' => '../experimental/slang',
      'SlangParserException' => '../experimental/slang',
      'SlangSetup' => '../experimental/slang',
      'SlangParser' => '../experimental/slang',
      '__OldSlangParser' => '../experimental/slang',
      'SlangStringNode' => '../experimental/slang',
      'SlangSymbolNode' => '../experimental/slang',
      'SlangTools' => '../experimental/slang',
      'SlangEvaluator' => '../experimental/slang',
      'SlangTemplator' => '../experimental/slang',
    )
);