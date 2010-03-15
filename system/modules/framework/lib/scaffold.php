#!/usr/bin/env php
<?php 


class Scaffold
{
  protected $script;
  protected $args;
  protected $interactive;
  protected $extensionPath;
  protected $table;
  protected $module;
  protected $dcaPath;
  protected $langsPath;
  protected $fields = array();



  public function __construct( $argv, $interactive = false )
  {
    $this->script         = array_shift( $argv );
    $this->args           = $argv;
    $this->interactive    = $interactive;
  }



  public function run()
  {
    $this->parseArgs();
  }



  protected function parseArgs()
  {
    $extensionsPath = dirname( dirname( dirname( __FILE__ ) ) );
    $extension      = array_shift( $this->args );

    if ( $extension == 'help' or $extension == '--help' or $extension == '-h' )
    {
      $this->printHelp();
      exit( 1 );
    }

    if ( count( $this->args ) < 3 )
    {
      $this->printHelp();
        exit( 1 );
    }

    $this->module = array_shift( $this->args );
    $this->table  = array_shift( $this->args );

    $this->extensionPath  = $extensionsPath . '/' . $extension;

    if ( ! is_dir( $this->extensionPath ) )
    {
      throw new Exception( "Extension " . $this->extensionPath . " does not exists" );
      exit( 1 );
    }

    $this->dcaPath        = $this->extensionPath . '/dca/' . $this->table . '.php';
    $this->dbPath         = $this->extensionPath . '/config/database.sql';
    $this->confPath       = $this->extensionPath . '/config/config.php';
    $this->langsPath      = array();

    foreach ( glob( $this->extensionPath . '/languages/*' ) as $file )
    {
      if ( is_dir( $file ) )
      {
        $this->langsPath[] = $file . '/' . $this->table . '.php';
      }
    }

    foreach ( $this->args as $arg )
    {
      $chunks = explode( ':', $arg );
      if ( count( $chunks ) != 2 )
      {
        $this->printHelp();
        exit( 1 );
      }

      $this->fields[ $chunks[0] ] = $chunks[1];
    }

    $this->checkPermissions();

    if ( $this->interactive and ! $this->confirm() )
    {
      echo "aborting\n";
      exit( 1 );
    }

    $this->generateDCA();
    $this->generateDatabase();
    $this->generateConfig();
    $this->generateLangs();
  }



  protected function checkPermissions()
  {
    $filesToCheck  = array( $this->extensionPath, $this->dbPath, $this->confPath);
    $filesToCreate = array_merge( array( $this->dcaPath ), $this->langsPath );

    foreach ( $filesToCheck as $fileToCheck )
    {
      if ( ! is_writable( $fileToCheck ) )
      {
        throw new Exception( $fileToCheck . " is not writable" );
        exit( 1 );
      }
    }


    foreach ( $filesToCreate as $fileToCreate )
    {
      if ( file_exists( $fileToCreate ) )
      {
        throw new Exception( $fileToCreate . " already exists" );
        exit( 1 );
      }

      if ( ! is_writable( dirname( $fileToCreate ) ) )
      {
        throw new Exception( dirname( $fileToCreate ) . " is not writable" );
        exit( 1 );
      }
    }
  }


  protected function confirm()
  {
    $langList = "";

    echo "extension: " . $this->extensionPath . "\n";
    echo "module:    " . $this->module . "\n";
    echo "dca:       " . $this->dcaPath . "\n";
    echo "config:    " . $this->confPath . "\n";
    echo "database:  " . $this->dbPath . "\n"; 
    echo "fields:    \n";
    foreach ( $this->fields as $field => $type  )
    {
      echo "  $field => $type\n";
    }
    echo "langs:     \n";
    foreach ( $this->langsPath as $lang )
    {
      echo '  ' . $lang . '/' . $this->table . ".php\n";
    }
    echo "\n";
    $ask = readline( "Ok? [y/N] " );

    if ( $ask == 'y' )
    {
      return true;
    }

    return false;
  }



  protected function generateDCA()
  {
    $fieldsStr = '';
    $fieldList = '';

    foreach ( $this->fields as $field => $type )
    {
      $fieldList .= $field . ';';

      $template = dirname( __FILE__ ) . '/templates/dca/' . $type . '.tpl';
      if ( ! file_exists( $template ) )
      {
        $template = dirname( __FILE__ ) . '/templates/dca/field_default.tpl';
      }

      ob_start();
      require( $template );
      $fieldsStr .= ob_get_clean();
    }

    $template = dirname( __FILE__ ) . '/templates/dca/dca_file.tpl';
    ob_start();
    require( $template );
    $templateStr = ob_get_clean();
    $templateStr = str_replace( '[?php', '<?php', $templateStr  );

    file_put_contents( $this->dcaPath, $templateStr );
    if ( $this->interactive )
    {
      echo "Created " . $this->dcaPath . "\n";
    }
  }



  protected function generateDatabase()
  {
    $sqlFields = array(  
      'checkbox'  => "  `%s` char(1) NOT NULL default '',",
      'date'      => "  `%s` int(10) unsigned NOT NULL default '0',",
      'filetree'  => "  `%s` varchar(255) NOT NULL default '',",
      'rte'       => "  `%s` text NULL,",
      'select'    => "  `%s` varchar(255) NOT NULL default '',",
      'text'      => "  `%s` varchar(255) NOT NULL default '',",
      'unknown'   => "  `%s` varchar(255) NOT NULL default '',",
    );

    $fields = "";
    foreach ( $this->fields as $field => $type )
    {
      if ( ! array_key_exists( $type, $sqlFields ) )
      {
        if ( $this->interactive )
        {
          echo "Unknown type: $type\nDon't forget to review database.sql!\n";
        }

        $type = 'unknown';
      }

      $fields .= sprintf( $sqlFields[ $type ], $field ) . "\n";
    }

    $table = "\n\n-- --------------------------------------------------------\n\n--\n-- Table `%s`\n--\n\nCREATE TABLE `%s` (\n  `id` int(10) unsigned NOT NULL auto_increment,\n  `pid` int(10) unsigned NOT NULL default '0',\n  `sorting` int(10) unsigned NOT NULL default '0',\n  `tstamp` int(10) unsigned NOT NULL default '0',\n  `created_at` int(10) unsigned NOT NULL default '0',\n%s  PRIMARY KEY  (`id`),\n  KEY `pid` (`pid`),\n) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $table = sprintf( $table, $this->table, $this->table, $fields );

    file_put_contents( $this->dbPath, $table, FILE_APPEND );
    if ( $this->interactive )
    {
      echo "Updated " . $this->dbPath . "\n";
    }
  }



  protected function generateConfig()
  {
    $confStr = str_replace( '?>', '', file_get_contents( $this->confPath ) );

    $confStr .= sprintf( "\n\narray_insert( \$GLOBALS[ 'BE_MOD' ][ 'content' ], 0, array(\n'%s' => array\n(\n'tables'       => array('%s'),\n'icon'         => '',\n),\n));\n",
                         $this->module,
                         $this->table
                       );

    file_put_contents( $this->confPath, $confStr );
    if ( $this->interactive )
    {
      echo "Updated " . $this->confPath . "\n";
    }
  }



  protected function generateLangs()
  {
    $template = dirname( __FILE__ ) . '/templates/lang.tpl';
    ob_start();
    require( $template );
    $templateStr = ob_get_clean();
    $templateStr = str_replace( '[?php', '<?php', $templateStr  );

    foreach ( $this->langsPath as $lang )
    {
      file_put_contents( $lang, $templateStr );
      if ( $this->interactive )
      {
        echo "Created " . $lang . "\n";
      }
    }
  }



  protected function printHelp()
  {
    echo $this->script . " <extension> <modulename> <table> <field:type> [<field:type> ...]\n";
    echo "Known types:\n";
    foreach ( glob( dirname( __FILE__ ) . '/templates/dca/*' ) as $type )
    {
      if ( $type != 'field_default.tpl' and $type != 'dca_file.tpl' )
      {
        echo "  - " . basename( $type, '.tpl' ) . "\n";
      }
    }
    echo "\n";
    echo "Unknown types will produce vanilla dca field and to be reviewed sql file field\n";
  }
}

if ( count( $argv ) )
{
  $scaffold = new Scaffold( $argv, true );
  $scaffold->run();
}
