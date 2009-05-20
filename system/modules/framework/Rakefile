require 'lib/cucumber_task/task'
require 'config/init'
include Framework

Cucumber::Rake::Task.new( :features ) do |t|
  prepare_db
  load_fixtures
end

desc "Launch specs"
task :specs do
  Dir.chdir( File.join( FW_ROOT, 'tests', 'specs' ) )
  phpspec_bin = File.join( FW_ROOT, 'lib', 'phpspec.php' )
  prepare_db
  load_fixtures
  puts %x( #{phpspec_bin} -r )
end

namespace :db do
  desc "Copy the data and structure from the dev db to the fixture file and load it"
  task :prepare do
    dev_db = Conf[ 'mysql' ][ 'dev' ]
    host, user, pass, db = dev_db[ 'host' ], dev_db[ 'user' ], dev_db[ 'password' ], dev_db[ 'database' ]
    %x( mysqldump -h#{host} -u#{user} -p#{pass} #{db} > #{Fixtures}/fixtures.sql )
    load_fixtures
  end

  desc "Load the fixtures"
  task :load do
    load_fixtures
  end
end
