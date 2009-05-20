require 'yaml'
require 'mysql'

module Framework
  FW_ROOT   = File.expand_path( File.join( File.dirname(__FILE__ ), '..'))
  TL_ROOT   = File.expand_path( File.join( FW_ROOT, '..', '..', '..' ))
  Fixtures  = File.join( FW_ROOT, "tests", "fixtures" )
  TL_config = File.join( TL_ROOT, 'system', 'config', 'localconfig.php' )

  file = File.open "#{FW_ROOT}/config/config.yml"
  Conf = YAML::load( file )
  file.close


  def change_db( to )
    content = File.read( TL_config )
    new_content = content.gsub( /\$GLOBALS\['TL_CONFIG'\]\['dbDatabase'\] = '.+?';/, "$GLOBALS['TL_CONFIG']['dbDatabase'] = '#{to}';" )
    file = File.open( TL_config, 'w' )
    file.puts new_content
    file.close
  end

  def prepare_db( env='test' )
    content = File.read( TL_config )
    content =~ /\$GLOBALS\['TL_CONFIG'\]\['dbDatabase'\] = *'(.+?)' *;/
    @from_db = $1
    change_db( Conf[ 'mysql' ][ env ][ 'database' ] )
    Kernel.at_exit { restore_db }
  end

  def restore_db
    @from_db ||= Conf[ 'mysql' ][ 'dev' ][ 'database' ]
    change_db( @from_db )
  end

  def load_fixtures
    test_db = Conf[ 'mysql' ][ 'test' ]
    host, user, pass, db = test_db[ 'host' ], test_db[ 'user' ], test_db[ 'password' ], test_db[ 'database' ]
    %x( mysql -h#{host} -u#{user} -p#{pass} #{db} < #{Fixtures}/fixtures.sql )
    co = Mysql.new host, user, pass, db

    Dir.glob( "#{Fixtures}/*.yml" ).each do |f|
      file = File.open( f )
      if ( fixture_tables = YAML::load(file) )
        # truncate desired tables
        if fixture_tables[ 'truncate' ]
          fixture_tables[ 'truncate' ].each do |table|
            co.query sprintf( "delete from %s", table )
          end

          fixture_tables.delete 'truncate'
        end

        # load the fixtures
        fixture_tables.each do |table, fixtures|
          fixtures.each do |name,fixture|
            query = "insert into `#{table}`(%s) values(%s)"
            fields = ""
            values = ""
            fixture.each do |field,value|
              fields << "`#{field}`,"
              values << "'#{value}',"
            end

            co.query sprintf( query, fields.chop, values.chop )
          end
        end
      end
      file.close
    end

    co.close
  end
end

